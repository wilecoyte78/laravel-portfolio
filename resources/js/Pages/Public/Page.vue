<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';

defineProps({
    page: { type: Object, required: true },
});
</script>

<template>
    <PublicLayout>
        <SeoHead
            :title="page.title"
            :meta-title="page.meta_title"
            :description="page.meta_description"
            :keywords="page.meta_keywords"
        />
        <article>
            <div class="prose max-w-none prose-headings:font-display prose-headings:font-normal prose-headings:text-ink prose-a:text-ink prose-a:underline prose-a:decoration-brass prose-a:decoration-2 prose-a:underline-offset-4" v-html="page.content"></div>
        </article>
    </PublicLayout>
</template>
