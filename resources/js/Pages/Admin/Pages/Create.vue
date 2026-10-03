<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import WysiwygEditor from '@/Components/WysiwygEditor.vue';
import SeoFields from '@/Components/SeoFields.vue';

const form = useForm({
    title: '',
    slug: '',
    content: '',
    meta_title: '',
    meta_description: '',
    meta_keywords: '',
    is_published: false,
});

function submit() {
    form.post(route('admin.pages.store'));
}
</script>

<template>
    <AdminLayout>
        <Head title="New page" />
        <h2 class="mb-6 text-xl font-semibold text-slate-900">New page</h2>

        <form class="max-w-3xl space-y-5" @submit.prevent="submit">
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Title</label>
                <input v-model="form.title" type="text" required
                    class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
                <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    Slug <span class="font-normal text-slate-400">(leave blank to auto-generate)</span>
                </label>
                <input v-model="form.slug" type="text" placeholder="e.g. bio"
                    class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
                <p v-if="form.errors.slug" class="mt-1 text-sm text-red-600">{{ form.errors.slug }}</p>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Content</label>
                <WysiwygEditor v-model="form.content" />
            </div>

            <SeoFields :form="form" />

            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input v-model="form.is_published" type="checkbox" class="rounded border-slate-300" />
                Published
            </label>

            <button type="submit" :disabled="form.processing"
                class="rounded-md bg-slate-900 px-5 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50">
                Create page
            </button>
        </form>
    </AdminLayout>
</template>
