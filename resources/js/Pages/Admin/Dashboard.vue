<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    stats: { type: Object, required: true },
    gmailConnected: { type: Boolean, default: false },
});
</script>

<template>
    <AdminLayout>
        <Head title="Dashboard" />
        <h2 class="mb-6 text-xl font-semibold text-slate-900">Dashboard</h2>
        <div class="grid grid-cols-2 gap-4 sm:max-w-md">
            <div class="rounded-lg border border-slate-200 bg-white p-5">
                <div class="text-2xl font-bold text-slate-900">{{ stats.total_pages }}</div>
                <div class="text-sm text-slate-500">Total pages</div>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5">
                <div class="text-2xl font-bold text-slate-900">{{ stats.published_pages }}</div>
                <div class="text-sm text-slate-500">Published</div>
            </div>
        </div>

        <div class="mt-8 max-w-md rounded-lg border border-slate-200 bg-white p-5">
            <h3 class="mb-2 text-sm font-semibold text-slate-900">Contact form (Gmail)</h3>
            <p v-if="gmailConnected" class="text-sm text-emerald-700">
                ✓ Connected — contact form messages will be emailed via Gmail.
            </p>
            <p v-else class="text-sm text-slate-500">
                Not connected yet. Connect a Gmail account so the contact form can send you messages.
            </p>
            <a :href="route('admin.gmail.connect')"
                class="mt-3 inline-block rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
                {{ gmailConnected ? 'Reconnect Gmail' : 'Connect Gmail' }}
            </a>
        </div>
    </AdminLayout>
</template>
