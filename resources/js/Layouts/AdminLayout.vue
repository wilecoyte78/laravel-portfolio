<script setup>
import { computed, watch } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import { toast } from 'vue3-toastify';

const page = usePage();
const user = computed(() => page.props.auth?.user);

watch(() => page.props.flash.success, (message) => {
  if (message) toast.success(message);

  page.props.flash.success = null;
}, { immediate: true });

watch(() => page.props.flash.error, (message) => {
  if (message) toast.error(message);

  page.props.flash.error = null;
}, { immediate: true });

watch(() => page.props.errors, (errors) => {
  if (errors && Object.keys(errors).length > 0) {
    toast.error("Please fix the errors in the form.");
    
    page.props.errors = {};
  }
}, { deep: true, immediate: true });

// Mark the admin section currently being viewed (WCAG 1.3.1).
function isCurrent(href) {
    // Strip the origin without touching `window`, so this also works during SSR.
    const linkPath = href.replace(/^https?:\/\/[^/]+/, '').split(/[?#]/)[0] || '/';
    const currentPath = (page.url ?? '').split(/[?#]/)[0];

    return currentPath === linkPath || currentPath.startsWith(`${linkPath}/`);
}

function logout() {
    router.post(route('logout'));
}

const navLinks = [
    { label: 'Dashboard', href: route('admin.dashboard') },
    { label: 'Pages', href: route('admin.pages.index') },
    { label: 'Navigation', href: route('admin.navigation.index') },
    { label: 'Resume', href: route('admin.resume.index') },
    { label: 'Database', href: route('admin.database.index') },
    { label: 'Logs', href: route('admin.logs.index') },
];
</script>

<template>
    <div class="min-h-screen bg-slate-50">
        <a
            href="#admin-main"
            class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-slate-900 focus:px-4 focus:py-3 focus:text-sm focus:font-medium focus:text-white"
        >
            Skip to main content
        </a>

        <div class="flex">
            <aside class="hidden w-56 shrink-0 border-r border-slate-200 bg-white p-4 md:block">
                <div class="mb-6 px-2 text-sm font-semibold text-slate-900">Admin</div>
                <nav aria-label="Admin" class="space-y-1">
                    <Link
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        :aria-current="isCurrent(link.href) ? 'page' : null"
                        class="block rounded-md px-2 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 aria-[current=page]:bg-slate-100 aria-[current=page]:text-slate-900"
                    >
                        {{ link.label }}
                    </Link>
                </nav>
            </aside>

            <div class="min-w-0 flex-1">
                <header class="border-b border-slate-200 bg-white px-6 py-3">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-slate-500">Signed in as {{ user?.name }}</p>
                        <button type="button" class="text-sm font-medium text-slate-600 hover:text-slate-900" @click="logout">Log out</button>
                    </div>

                    <!-- The sidebar is hidden on small screens, so keep navigation reachable there (WCAG 1.4.10). -->
                    <nav aria-label="Admin" class="mt-3 flex flex-wrap gap-x-4 gap-y-2 md:hidden">
                        <Link
                            v-for="link in navLinks"
                            :key="link.href"
                            :href="link.href"
                            :aria-current="isCurrent(link.href) ? 'page' : null"
                            class="py-1 text-sm font-medium text-slate-600 hover:text-slate-900 aria-[current=page]:text-slate-900 aria-[current=page]:underline"
                        >
                            {{ link.label }}
                        </Link>
                    </nav>
                </header>

                <main id="admin-main" tabindex="-1" class="p-6 focus:outline-none focus-visible:outline-none">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
