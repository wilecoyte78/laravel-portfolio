<script setup>
import { computed } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

function logout() {
    router.post(route('logout'));
}

const navLinks = [
    { label: 'Dashboard', href: route('admin.dashboard') },
    { label: 'Pages', href: route('admin.pages.index') },
    { label: 'Navigation', href: route('admin.navigation.index') },
];
</script>

<template>
    <div class="min-h-screen bg-slate-50">
        <div class="flex">
            <aside class="hidden w-56 shrink-0 border-r border-slate-200 bg-white p-4 md:block">
                <div class="mb-6 px-2 text-sm font-semibold text-slate-900">Admin</div>
                <nav class="space-y-1">
                    <Link
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        class="block rounded-md px-2 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900"
                    >
                        {{ link.label }}
                    </Link>
                </nav>
            </aside>

            <div class="flex-1">
                <header class="flex items-center justify-between border-b border-slate-200 bg-white px-6 py-3">
                    <h1 class="text-sm text-slate-500">Signed in as {{ user?.name }}</h1>
                    <button class="text-sm font-medium text-slate-600 hover:text-slate-900" @click="logout">Log out</button>
                </header>

                <main class="p-6">
                    <div v-if="flashSuccess" class="mb-4 rounded-md bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        {{ flashSuccess }}
                    </div>
                    <div v-if="flashError" class="mb-4 rounded-md bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ flashError }}
                    </div>
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
