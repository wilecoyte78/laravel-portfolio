<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    table: { type: String, required: true },
    columns: { type: Array, required: true },
    rows: { type: Array, required: true },
    pagination: { type: Object, required: true },
});

function pageHref(page) {
    return route('admin.database.show', { table: props.table, page });
}

function display(value) {
    if (typeof value === 'boolean') return value ? 'true' : 'false';
    return String(value);
}
</script>

<template>
    <AdminLayout>
        <Head :title="`Database: ${table}`" />

        <div class="mb-6">
            <Link :href="route('admin.database.index')" class="text-sm text-slate-500 hover:text-slate-900">
                ← All tables
            </Link>
            <h2 class="mt-2 text-xl font-semibold text-slate-900">{{ table }}</h2>
            <p class="mt-1 text-sm text-slate-500">
                <template v-if="pagination.total > 0">
                    Showing {{ pagination.from }}–{{ pagination.to }} of {{ pagination.total.toLocaleString() }} rows
                </template>
                <template v-else>No rows</template>
                · read-only
            </p>
        </div>

        <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left">
                    <tr>
                        <th v-for="column in columns" :key="column.name" class="whitespace-nowrap px-4 py-3">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-600">
                                {{ column.name }}
                                <span v-if="column.masked" class="ml-1 rounded bg-amber-100 px-1.5 py-0.5 text-[10px] normal-case text-amber-800">
                                    masked
                                </span>
                            </div>
                            <div class="text-xs font-normal normal-case text-slate-400">{{ column.type }}</div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="(row, index) in rows" :key="index" class="align-top hover:bg-slate-50">
                        <td v-for="column in columns" :key="column.name" class="max-w-xs px-4 py-2 text-slate-700">
                            <span v-if="row[column.name] === null" class="italic text-slate-400">NULL</span>
                            <span v-else-if="column.masked" class="text-slate-400">{{ row[column.name] }}</span>
                            <span v-else class="break-words">{{ display(row[column.name]) }}</span>
                        </td>
                    </tr>
                    <tr v-if="rows.length === 0">
                        <td :colspan="columns.length" class="px-4 py-6 text-center text-slate-400">
                            This table is empty.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="pagination.last_page > 1" class="mt-4 flex items-center justify-between text-sm">
            <Link
                v-if="pagination.current_page > 1"
                :href="pageHref(pagination.current_page - 1)"
                preserve-scroll
                class="rounded-md border border-slate-200 bg-white px-3 py-1.5 font-medium text-slate-700 hover:bg-slate-50"
            >
                ← Previous
            </Link>
            <span v-else class="px-3 py-1.5 text-slate-300">← Previous</span>

            <span class="text-slate-500">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>

            <Link
                v-if="pagination.current_page < pagination.last_page"
                :href="pageHref(pagination.current_page + 1)"
                preserve-scroll
                class="rounded-md border border-slate-200 bg-white px-3 py-1.5 font-medium text-slate-700 hover:bg-slate-50"
            >
                Next →
            </Link>
            <span v-else class="px-3 py-1.5 text-slate-300">Next →</span>
        </div>
    </AdminLayout>
</template>
