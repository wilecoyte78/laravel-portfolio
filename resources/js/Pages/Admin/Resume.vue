<script setup>
import { computed, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    resume: {
        type: Object,
        default: null,
    },
});

const fileInput = ref(null);

const form = useForm({
    resume: null,
});

const selectedFilename = computed(() => {
    return form.resume?.name ?? null;
});

const formattedSize = computed(() => {
    if (!props.resume?.size) {
        return null;
    }

    const bytes = props.resume.size;

    if (bytes < 1024) {
        return `${bytes} B`;
    }

    if (bytes < 1024 * 1024) {
        return `${(bytes / 1024).toFixed(1)} KB`;
    }

    return `${(bytes / (1024 * 1024)).toFixed(2)} MB`;
});

const formattedDate = computed(() => {
    if (!props.resume?.modified_at) {
        return null;
    }

    return new Date(props.resume.modified_at * 1000).toLocaleString();
});

const resumeUrl = computed(() => {
    if (!props.resume) {
        return null;
    }

    return `${props.resume.url}?v=${props.resume.modified_at}`;
});

function chooseFile() {
    fileInput.value?.click();
}

function handleFileChange(event) {
    const file = event.target.files?.[0] ?? null;

    form.resume = file;
}

function uploadResume() {
    if (!form.resume) {
        return;
    }

    const message = props.resume
        ? 'This will replace the current resume PDF. Continue?'
        : 'Upload this resume PDF?';

    if (!window.confirm(message)) {
        return;
    }

    form.post(route('admin.resume.update'), {
        forceFormData: true,
        preserveScroll: true,

        onSuccess: () => {
            form.reset();

            if (fileInput.value) {
                fileInput.value.value = '';
            }
        },
    });
}
</script>

<template>
    <AdminLayout>
        <Head title="Resume" />

        <div class="max-w-5xl">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-slate-900">
                    Resume
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Upload and manage the PDF displayed on the public resume page.
                </p>
            </div>

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
                <!-- Current Resume -->
                <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">
                                Current Resume
                            </h3>

                            <p
                                v-if="resume"
                                class="mt-1 text-xs text-slate-500"
                            >
                                {{ resume.filename }}
                            </p>

                            <p
                                v-else
                                class="mt-1 text-xs text-slate-500"
                            >
                                No resume has been uploaded yet.
                            </p>
                        </div>

                        <a
                            v-if="resumeUrl"
                            :href="resumeUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="rounded-md border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50"
                        >
                            Open PDF
                        </a>
                    </div>

                    <div
                        v-if="resumeUrl"
                        class="bg-slate-100"
                    >
                        <iframe
                            :src="resumeUrl"
                            title="Current Resume PDF"
                            class="block h-[75vh] min-h-[700px] w-full"
                        />
                    </div>

                    <div
                        v-else
                        class="flex min-h-[500px] items-center justify-center p-8 text-center"
                    >
                        <div>
                            <div class="text-sm font-medium text-slate-700">
                                No resume uploaded
                            </div>

                            <p class="mt-1 text-sm text-slate-500">
                                Upload a PDF using the form.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Upload -->
                <div class="h-fit rounded-lg border border-slate-200 bg-white p-5">
                    <h3 class="text-sm font-semibold text-slate-900">
                        Upload Resume
                    </h3>

                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Upload a PDF to create or replace the current resume.
                    </p>

                    <div
                        v-if="resume"
                        class="mt-4 rounded-md bg-slate-50 p-4"
                    >
                        <div class="text-xs font-medium uppercase tracking-wide text-slate-500">
                            Current File
                        </div>

                        <div class="mt-1 break-all text-sm font-medium text-slate-900">
                            {{ resume.filename }}
                        </div>

                        <div class="mt-2 space-y-1 text-xs text-slate-500">
                            <div v-if="formattedSize">
                                Size: {{ formattedSize }}
                            </div>

                            <div v-if="formattedDate">
                                Updated: {{ formattedDate }}
                            </div>
                        </div>
                    </div>

                    <input
                        ref="fileInput"
                        type="file"
                        accept="application/pdf,.pdf"
                        class="hidden"
                        @change="handleFileChange"
                    />

                    <button
                        type="button"
                        class="mt-5 w-full rounded-md border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                        @click="chooseFile"
                    >
                        Choose PDF
                    </button>

                    <div
                        v-if="selectedFilename"
                        class="mt-3 rounded-md border border-slate-200 bg-slate-50 p-3"
                    >
                        <div class="text-xs text-slate-500">
                            Selected File
                        </div>

                        <div class="mt-1 break-all text-sm font-medium text-slate-900">
                            {{ selectedFilename }}
                        </div>
                    </div>

                    <button
                        type="button"
                        class="mt-4 w-full rounded-md bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="!form.resume || form.processing"
                        @click="uploadResume"
                    >
                        {{ form.processing ? 'Uploading...' : 'Upload & Replace Resume' }}
                    </button>

                    <p class="mt-3 text-xs leading-5 text-slate-500">
                        PDF files only. Maximum file size: 10 MB.
                    </p>

                    <div
                        v-if="form.errors.resume"
                        class="mt-3 rounded-md bg-red-50 p-3 text-sm text-red-700"
                    >
                        {{ form.errors.resume }}
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>