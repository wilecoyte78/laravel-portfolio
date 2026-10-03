<script setup>
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { SITE_NAME, DEFAULT_DESCRIPTION } from '@/seo';

/**
 * Renders <title>, meta description/keywords, Open Graph tags and the
 * canonical link for a public page.
 *
 * Title rules:
 *   1. metaTitle (the "SEO title" from the admin) is used exactly as entered.
 *   2. Otherwise "{title} - {APP_NAME}".
 *   3. With neither, just the site name (used for the home page).
 */
const props = defineProps({
    title: { type: String, default: '' },
    metaTitle: { type: String, default: null },
    description: { type: String, default: null },
    keywords: { type: String, default: null },
});

const page = usePage();

const fullTitle = computed(() => {
    if (props.metaTitle) return props.metaTitle;
    if (props.title) return `${props.title} - ${SITE_NAME}`;
    return SITE_NAME;
});

const desc = computed(() => props.description || DEFAULT_DESCRIPTION);
const url = computed(() => page.props.seo?.url ?? '');
</script>

<template>
    <Head>
        <title>{{ fullTitle }}</title>
        <meta head-key="description" name="description" :content="desc" />
        <meta v-if="keywords" head-key="keywords" name="keywords" :content="keywords" />
        <meta head-key="og:title" property="og:title" :content="fullTitle" />
        <meta head-key="og:description" property="og:description" :content="desc" />
        <meta head-key="og:type" property="og:type" content="website" />
        <meta v-if="url" head-key="og:url" property="og:url" :content="url" />
        <link v-if="url" head-key="canonical" rel="canonical" :href="url" />
    </Head>
</template>
