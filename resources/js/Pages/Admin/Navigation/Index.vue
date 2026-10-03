<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { toast } from 'vue3-toastify';

const showSuccess = () => {
    toast.success("Operation successful!", {
        autoClose: 3000,
        position: 'toast-top-right',
    });
};

const showError = () => {
    toast.error("Something went wrong.");
};

const props = defineProps({
    tree: { type: Array, required: true },
    pages: { type: Array, required: true },
    targets: { type: Array, required: true },
});

const currentTargetLabel = computed(() => {
    const match = props.targets.find(t => t.value === form.target);

    return match ? match.label : '';
});

const showModal = ref(false);
const modalMode = ref('add'); // 'add' | 'edit'
const editingItem = ref(null);

const form = useForm({
    label: '',
    page_id: '',
    parent_id: null,
    external_url: '',
    target: '_self',
});

function openAddForm(parentId = null) {
    modalMode.value = 'add';
    editingItem.value = null;
    form.reset();
    form.parent_id = parentId;
    showModal.value = true;
}

function openEditForm(item) {
    modalMode.value = 'edit';
    editingItem.value = item;
    form.reset();
    form.label = item.label;
    form.page_id = item.page_id ?? '';
    form.external_url = item.external_url ?? '';
    form.target = item.target;
    showModal.value = true;
}

function getTargetLabel(targetValue) {
    const match = props.targets.find(t => t.value === targetValue);
    return match ? match.label : targetValue; 
    // Falls back to the raw value (e.g. "_blank") if no pretty label match is found
}

function submit() {
    if (modalMode.value === 'edit') {
        form.put(route('admin.navigation.update', editingItem.value.id), {
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            },
        });
    } else {
        form.post(route('admin.navigation.store'), {
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            },
        });
    }
}

function destroy(item) {
    const warn = item.children?.length
        ? `Delete "${item.label}" and its ${item.children.length} sub-item(s)?`
        : `Delete "${item.label}"?`;
    if (confirm(warn)) {
        router.delete(route('admin.navigation.destroy', item.id));
    }
}

function move(item, direction) {
    // Simple up/down re-order within the same parent level.
    const siblings = findSiblings(props.tree, item.parent_id);
    const index = siblings.findIndex((s) => s.id === item.id);
    const swapWith = siblings[index + direction];
    if (!swapWith) return;

    router.post(route('admin.navigation.reorder'), {
        items: [
            { id: item.id, parent_id: item.parent_id, sort_order: swapWith.sort_order },
            { id: swapWith.id, parent_id: swapWith.parent_id, sort_order: item.sort_order },
        ],
    }, { preserveScroll: true });
}

function findSiblings(nodes, parentId) {
    if (parentId === null) return nodes;
    for (const node of nodes) {
        if (node.id === parentId) return node.children;
        const found = findSiblings(node.children, parentId);
        if (found) return found;
    }
    return [];
}
</script>

<template>
    <AdminLayout>
        <Head title="Navigation" />
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-900">Navigation</h2>
            <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700" @click="openAddForm(null)">
                Add top-level item
            </button>
        </div>

        <p class="mb-4 max-w-2xl text-sm text-slate-500">
            Top-level items can be a plain link (e.g. "Home") or a dropdown header with no page of
            its own (e.g. "About" containing "Bio" and "Resume"). Leave "Page" empty when adding an
            item that should only act as a dropdown label.
        </p>

        <div class="space-y-3">
            <div v-for="item in tree" :key="item.id" class="rounded-lg border border-slate-200 bg-white p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="font-medium text-slate-900">{{ item.label }}</span>
                        <span class="ml-2 text-xs text-slate-400">
                            {{ item.page ? '/' + item.page.slug : (item.external_url || 'no link — dropdown header') }}
                        </span>
                        <span class="ml-2 text-xs text-slate-400">
                            target="{{ item.target }}": {{ getTargetLabel(item.target) }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <button class="text-slate-400 hover:text-slate-700" @click="move(item, -1)">↑</button>
                        <button class="text-slate-400 hover:text-slate-700" @click="move(item, 1)">↓</button>
                        <button class="text-slate-600 hover:text-slate-900" @click="openEditForm(item)">Edit</button>
                        <button class="text-slate-600 hover:text-slate-900" @click="openAddForm(item.id)">+ sub-item</button>
                        <button class="text-red-600 hover:text-red-800" @click="destroy(item)">Delete</button>
                    </div>
                </div>

                <div v-if="item.children?.length" class="mt-3 ml-6 space-y-2 border-l border-slate-100 pl-4">
                    <div v-for="child in item.children" :key="child.id" class="flex items-center justify-between text-sm">
                        <div>
                            <span class="text-slate-800">{{ child.label }}</span>
                            <span class="ml-2 text-xs text-slate-400">
                                {{ child.page ? '/' + child.page.slug : (child.external_url || '—') }}
                            </span>
                            <span class="ml-2 text-xs text-slate-400">
                                target="{{ child.target }}": {{ getTargetLabel(child.target) }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <button class="text-slate-400 hover:text-slate-700" @click="move(child, -1)">↑</button>
                            <button class="text-slate-400 hover:text-slate-700" @click="move(child, 1)">↓</button>
                            <button class="text-slate-600 hover:text-slate-900" @click="openEditForm(child)">Edit</button>
                            <button class="text-red-600 hover:text-red-800" @click="destroy(child)">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="!tree.length" class="rounded-lg border border-dashed border-slate-300 p-8 text-center text-slate-400">
                No navigation items yet.
            </div>
        </div>

        <!-- Add / Edit item modal -->
        <div v-if="showModal" class="fixed inset-0 z-30 flex items-center justify-center bg-black/30 p-4">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                <h3 class="mb-4 text-lg font-semibold text-slate-900">
                    {{ modalMode === 'edit' ? `Edit "${editingItem?.label}"` : (form.parent_id ? 'Add sub-item' : 'Add top-level item') }}
                </h3>
                <form class="space-y-4" @submit.prevent="submit">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Label</label>
                        <input v-model="form.label" type="text" required placeholder="e.g. Bio"
                            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
                        <p v-if="form.errors.label" class="mt-1 text-sm text-red-600">{{ form.errors.label }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Page <span class="font-normal text-slate-400">(leave empty for a dropdown header)</span>
                        </label>
                        <select v-model="form.page_id" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                            <option value="">— No page (header only) —</option>
                            <option v-for="page in pages" :key="page.id" :value="page.id">{{ page.title }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Or external URL <span class="font-normal text-slate-400">(optional)</span>
                        </label>
                        <input v-model="form.external_url" type="text" placeholder="https://..."
                            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Or Target <span class="font-normal text-slate-400">(optional)</span>
                        </label>
                        <select v-model="form.target" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                            <option v-for="target in targets" :key="target.value" :value="target.value">
                                {{ target.value }}
                            </option>
                        </select>
                        <!-- Dynamic helper text that updates automatically -->
                        <p v-if="currentTargetLabel" id="target-help" class="mt-1.5 text-xs text-slate-500 italic">
                            Description: {{ currentTargetLabel }}
                        </p>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" class="text-sm text-slate-500" @click="showModal = false">Cancel</button>
                        <button type="submit" :disabled="form.processing"
                            class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50">
                            {{ modalMode === 'edit' ? 'Save changes' : 'Add' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
