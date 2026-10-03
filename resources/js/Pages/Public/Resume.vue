
<script setup>
import { computed } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';

const props = defineProps({
    page: {
        type: Object,
        default: () => ({}),
    },
    resume: {
        type: Object,
        default: null,
    },
});

const resumePdf = computed(() => {
    if (!props.resume?.url) {
        return null;
    }

    const url = new URL(props.resume.url, window.location.origin);
    url.searchParams.set('v', props.resume.modified_at ?? '');

    return url.toString();
});
</script>

<template>
    <PublicLayout>
        <SeoHead
            :title="page?.title ?? 'Resume'"
            :meta-title="page?.meta_title ?? ''"
            :description="page?.meta_description ?? ''"
            :keywords="page?.meta_keywords ?? ''"
        />

        <div class="mx-auto max-w-5xl">
            <div
                class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
            >
                <div>
                    <h1 class="font-display text-3xl text-ink sm:text-4xl">
                        Resume
                    </h1>

                    <p class="mt-2 text-sm text-stone-500 sm:text-base">
                        Senior Full Stack Software Engineer · PHP &amp; Laravel Specialist
                    </p>
                </div>

                <div v-if="resumePdf" class="flex flex-wrap gap-3">
                    <a
                        :href="resumePdf"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="rounded-lg bg-stone-800 px-4 py-2 text-sm font-medium text-white hover:bg-stone-700"
                    >
                        Open PDF
                    </a>

                    <a
                        :href="resumePdf"
                        download
                        class="rounded-lg border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50"
                    >
                        Download PDF
                    </a>
                </div>
            </div>

            <template v-if="resumePdf">
                <div
                    class="overflow-hidden rounded-xl border border-stone-200 bg-stone-100 shadow-xl"
                >
                    <iframe
                        :src="resumePdf"
                        title="Resume PDF"
                        class="block h-[80vh] min-h-[800px] w-full"
                    ></iframe>
                </div>

                <div class="mt-4 text-center text-sm text-stone-500">
                    <p>
                        Having trouble viewing the resume?
                        <a
                            :href="resumePdf"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="font-medium text-blue-700 underline underline-offset-2 hover:text-blue-800"
                        >
                            Open the PDF in a new tab
                        </a>.
                    </p>
                </div>
            </template>

            <div
                v-else
                class="rounded-xl border border-stone-200 bg-white p-10 text-center shadow-sm"
            >
                <h2 class="text-lg font-semibold text-stone-900">
                    Resume currently unavailable
                </h2>

                <p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-stone-500">
                    A resume has not been uploaded yet. Please check back later.
                </p>
            </div>
        </div>
    </PublicLayout>
</template>