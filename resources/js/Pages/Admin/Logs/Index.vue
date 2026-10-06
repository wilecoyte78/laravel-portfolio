<script setup>
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    files: { type: Array, required: true },
    currentFile: { type: String, default: null },
    levels: { type: Array, required: true },
    currentLevel: { type: String, default: null },
    total: { type: Number, default: 0 },
    logs: { type: Array, default: () => [] },
});

// Keep the other filter when one changes; drop empty values from the URL.
function applyFilters(changes) {
    const query = { file: props.currentFile, level: props.currentLevel, ...changes };
    Object.keys(query).forEach((key) => !query[key] && delete query[key]);

    router.get(route('admin.logs.index'), query, { preserveState: true, replace: true });
}
</script>

<template>
    <AdminLayout>
        <Head title="Logs" />

        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Logs</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Showing {{ logs.length }} of {{ total }} entries
                </p>
            </div>

            <div class="flex gap-2">
                <select
                    :value="currentLevel ?? ''"
                    class="rounded-md border border-slate-300 px-3 py-1.5 text-sm capitalize"
                    @change="applyFilters({ level: $event.target.value })"
                >
                    <option value="">All levels</option>
                    <option v-for="level in levels" :key="level" :value="level">{{ level }}</option>
                </select>

                <select
                    :value="currentFile"
                    class="rounded-md border border-slate-300 px-3 py-1.5 text-sm"
                    @change="applyFilters({ file: $event.target.value })"
                >
                    <option v-for="file in files" :key="file" :value="file">{{ file }}</option>
                </select>
            </div>
</div>
        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Level</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Message</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="(log, i) in logs" :key="i" class="align-top">
                        <td class="px-4 py-3 font-medium uppercase">{{ log.level }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ log.date }}</td>
                        <td class="px-4 py-3">
                            <div class="break-words">{{ log.text }}</div>
                            <details v-if="log.stack" class="mt-1">
                                <summary class="cursor-pointer text-xs text-slate-500">Stack trace</summary>
                                <pre class="mt-1 overflow-x-auto text-xs text-slate-600">{{ log.stack }}</pre>
                            </details>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>