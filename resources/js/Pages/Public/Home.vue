<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';

defineProps({
    page: { type: Object, default: null },
});
</script>

<template>
    <PublicLayout>
        <SeoHead
            :title="page?.title"
            :meta-title="page?.meta_title"
            :description="page?.meta_description"
            :keywords="page?.meta_keywords"
        />

        <article v-if="page">
            <div class="prose max-w-none prose-headings:font-display prose-headings:font-normal prose-headings:text-ink" v-html="page.content"></div>
        </article>
    </PublicLayout>
</template>
