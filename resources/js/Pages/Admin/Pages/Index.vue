<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    pages: { type: Array, required: true },
});

function destroy(page) {
    if (confirm(`Delete "${page.title}"? This cannot be undone.`)) {
        router.delete(route('admin.pages.destroy', page.id));
    }
}
</script>

<template>
    <AdminLayout>
        <Head title="Pages" />
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-900">Pages</h2>
            <Link :href="route('admin.pages.create')" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
                New page
            </Link>
        </div>

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-medium">Title</th>
                        <th class="px-4 py-3 font-medium">Slug</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="page in pages" :key="page.id">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ page.title }}</td>
                        <td class="px-4 py-3 text-slate-500">/{{ page.slug }}</td>
                        <td class="px-4 py-3">
                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="page.is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'"
                            >
                                {{ page.is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <Link :href="route('admin.pages.edit', page.id)" class="mr-3 text-slate-600 hover:text-slate-900">Edit</Link>
                            <button class="text-red-600 hover:text-red-800" @click="destroy(page)">Delete</button>
                        </td>
                    </tr>
                    <tr v-if="!pages.length">
                        <td colspan="4" class="px-4 py-8 text-center text-slate-400">No pages yet.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p class="mt-4 text-sm text-slate-500">
            New pages aren't visible on the site until you add them to the
            <Link :href="route('admin.navigation.index')" class="underline">navigation menu</Link>.
        </p>
    </AdminLayout>
</template>
