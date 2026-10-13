<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    href: { type: String, default: '#' },
    target: { type: String, default: null },
});

const page = usePage();

const newTab = computed(() => props.target && props.target !== '_self');
const plain = computed(() => newTab.value || /^(https?:)?\/\//.test(props.href));

// Tell assistive technology which link points at the page being viewed (WCAG 1.3.1).
const isCurrent = computed(() => {
    if (plain.value || !props.href || props.href === '#') return false;

    const currentPath = (page.url ?? '').split(/[?#]/)[0];

    return currentPath === props.href.split(/[?#]/)[0];
});
</script>

<template>
    <a
        v-if="plain"
        :href="href"
        :target="newTab ? target : null"
        :rel="target === '_blank' ? 'noopener noreferrer' : null"
    >
        <slot />
        <span v-if="newTab" class="sr-only"> (opens in a new tab)</span>
    </a>
    <Link v-else :href="href" :aria-current="isCurrent ? 'page' : null">
        <slot />
    </Link>
</template>
