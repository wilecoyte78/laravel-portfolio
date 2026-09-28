<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
    page: { type: Object, required: true },
});
</script>

<template>
    <PublicLayout>
        <Head :title="page.title" />
        <article>
            <div class="prose max-w-none prose-headings:font-display prose-headings:font-normal prose-headings:text-ink prose-a:text-ink prose-a:underline prose-a:decoration-brass prose-a:decoration-2 prose-a:underline-offset-4" v-html="page.content"></div>
        </article>
    </PublicLayout>
</template>
