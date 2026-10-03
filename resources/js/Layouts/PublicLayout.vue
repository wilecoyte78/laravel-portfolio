<script setup>
import { computed, onMounted } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import NavBar from '@/Components/NavBar.vue';

const page = usePage();
const navigation = computed(() => page.props.navigation ?? []);

const GA_MEASUREMENT_ID = 'G-9YHNV829K1';

const appName = import.meta.env.VITE_APP_NAME;

onMounted(() => {
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
        <header class="sticky top-0 z-40 border-b border-ink/10 bg-paper/95 backdrop-blur">
            <div class="relative mx-auto flex max-w-4xl items-center justify-between px-6 py-5">
                <Link href="/" class="font-display text-lg text-ink">
                    {{ appName }}
                </Link>
                <NavBar :items="navigation" />
            </div>
        </header>

        <main class="mx-auto max-w-4xl px-6 py-16">
            <slot />
        </main>

        <footer class="border-t border-ink/10 py-10">
            <div class="mx-auto max-w-4xl px-6 text-sm text-stone-500">
                <div class="mb-3 space-x-5">
                    <Link href="/privacy-policy" class="hover:text-ink">Privacy Policy</Link>
                    <Link href="/terms-of-service" class="hover:text-ink">Terms of Service</Link>
                </div>
                <p>&copy; {{ new Date().getFullYear() }} {{ appName }}</p>
            </div>
        </footer>
    </div>
</template>
