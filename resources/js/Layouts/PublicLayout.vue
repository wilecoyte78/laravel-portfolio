<script>
// Module scope survives page-to-page remounts of the layout. Used to move
// focus to the main content after client-side navigation (WCAG 2.4.3), but
// not on the first page load or when a form posts back to the same URL.
let lastPathname = null;
</script>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import NavBar from '@/Components/NavBar.vue';

const page = usePage();
const navigation = computed(() => page.props.navigation ?? []);

const GA_MEASUREMENT_ID = 'G-9YHNV829K1';

const appName = import.meta.env.VITE_APP_NAME;

const mainContent = ref(null);

function focusMainAfterNavigation() {
    const { pathname } = window.location;

    if (lastPathname !== null && lastPathname !== pathname) {
        mainContent.value?.focus({ preventScroll: true });
    }

    lastPathname = pathname;
}

onMounted(() => {
    focusMainAfterNavigation();

    if (document.getElementById('ga-gtag-script')) return;

    const script = document.createElement('script');
    script.id = 'ga-gtag-script';
    script.async = true;
    script.src = `https://www.googletagmanager.com/gtag/js?id=${GA_MEASUREMENT_ID}`;
    document.head.appendChild(script);

    window.dataLayer = window.dataLayer || [];
    window.gtag = window.gtag || function () { window.dataLayer.push(arguments); };
    window.gtag('js', new Date());
    window.gtag('config', GA_MEASUREMENT_ID);
});
</script>

<template>
    <div class="min-h-screen bg-paper font-sans text-ink">
        <a
            href="#main-content"
            class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-ink focus:px-4 focus:py-3 focus:text-sm focus:font-medium focus:text-white"
        >
            Skip to main content
        </a>

        <header class="sticky top-0 z-40 border-b border-ink/10 bg-paper/95 backdrop-blur">
            <div class="relative mx-auto flex max-w-4xl items-center justify-between px-6 py-5">
                <Link href="/" class="font-display text-lg text-ink">
                    {{ appName }}<span class="sr-only">{{ appName ? ' - home' : 'Home' }}</span>
                </Link>
                <NavBar :items="navigation" />
            </div>
        </header>

        <main id="main-content" ref="mainContent" tabindex="-1" class="mx-auto max-w-4xl px-6 py-16 focus:outline-none focus-visible:outline-none">
            <slot />
        </main>

        <footer class="border-t border-ink/10 py-10">
            <div class="mx-auto max-w-4xl px-6 text-sm text-stone-500">
                <nav aria-label="Legal" class="mb-3 flex flex-wrap gap-x-5 gap-y-2">
                    <Link href="/privacy-policy" class="hover:text-ink">Privacy Policy</Link>
                    <Link href="/terms-of-service" class="hover:text-ink">Terms of Service</Link>
                </nav>
                <p>&copy; {{ new Date().getFullYear() }} {{ appName }}</p>
            </div>
        </footer>
    </div>
</template>
