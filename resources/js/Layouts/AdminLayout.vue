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

function logout() {
    router.post(route('logout'));
}

const navLinks = [
    { label: 'Dashboard', href: route('admin.dashboard') },
    { label: 'Pages', href: route('admin.pages.index') },
    { label: 'Navigation', href: route('admin.navigation.index') },
    { label: 'Resume', href: route('admin.resume.index') },
    { label: 'Database', href: route('admin.database.index') },
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
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
