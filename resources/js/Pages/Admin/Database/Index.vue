<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    tables: { type: Array, required: true },
});
</script>

<template>
    <AdminLayout>
        <Head title="Database" />

        <div class="mb-6">
            <h2 class="text-xl font-semibold text-slate-900">Database</h2>
            <p class="mt-1 text-sm text-slate-500">
                Read-only view of table contents. Secret-looking columns are masked.
            </p>
        </div>

        <div class="max-w-2xl overflow-hidden rounded-lg border border-slate-200 bg-white">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Table</th>
                        <th class="px-4 py-3 text-right">Rows</th>
                        <th class="px-4 py-3 text-right">Columns</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="table in tables" :key="table.name" class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <Link
                                :href="route('admin.database.show', table.name)"
                                class="font-medium text-slate-900 hover:underline"
                            >
                                {{ table.name }}
                            </Link>
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums text-slate-600">
                            {{ table.rows.toLocaleString() }}
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums text-slate-600">
                            {{ table.columns }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
