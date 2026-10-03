<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: { type: String, default: '#' },
    target: { type: String, default: null },
});

const newTab = computed(() => props.target && props.target !== '_self');
const plain = computed(() => newTab.value || /^(https?:)?\/\//.test(props.href));
</script>

<template>
    <a
        v-if="plain"
        :href="href"
        :target="newTab ? target : null"
        :rel="target === '_blank' ? 'noopener noreferrer' : null"
    >
        <slot />
    </a>
    <Link v-else :href="href">
        <slot />
    </Link>
</template>