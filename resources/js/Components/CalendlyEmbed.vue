<script>
// Module scope: shared by every instance so the widget loads once per page
// view, no matter how many times Inertia remounts the component.
const WIDGET_JS = 'https://assets.calendly.com/assets/external/widget.js';
const WIDGET_CSS = 'https://assets.calendly.com/assets/external/widget.css';
let loader = null;

function loadCalendly() {
    if (window.Calendly) return Promise.resolve(window.Calendly);
    if (loader) return loader;

    loader = new Promise((resolve, reject) => {
        if (!document.getElementById('calendly-widget-css')) {
            const link = document.createElement('link');
            link.id = 'calendly-widget-css';
            link.rel = 'stylesheet';
            link.href = WIDGET_CSS;
            document.head.appendChild(link);
        }

        const script = document.createElement('script');
        script.src = WIDGET_JS;
        script.async = true;
        script.onload = () => resolve(window.Calendly);
        script.onerror = () => {
            loader = null; // allow a retry on the next mount
            reject(new Error('Failed to load Calendly widget'));
        };
        document.head.appendChild(script);
    });

    return loader;
}
</script>

<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    url: { type: String, required: true },
    mode: { type: String, default: 'inline' }, // 'inline' | 'popup'
    height: { type: Number, default: 700 },
    buttonText: { type: String, default: 'Schedule a call' },
    prefill: { type: Object, default: () => ({}) }, // { name, email }
});

const emit = defineEmits(['scheduled']);

const container = ref(null);
const failed = ref(false);

function onMessage(e) {
    if (e.origin !== 'https://calendly.com') return;
    if (e.data?.event === 'calendly.event_scheduled') {
        emit('scheduled', e.data.payload);
    }
}

function openPopup() {
    window.Calendly?.initPopupWidget({ url: props.url, prefill: props.prefill });
}

onMounted(async () => {
    window.addEventListener('message', onMessage);

    try {
        const Calendly = await loadCalendly();

        if (props.mode === 'inline' && container.value) {
            Calendly.initInlineWidget({
                url: props.url,
                parentElement: container.value,
                prefill: props.prefill,
            });
        }
    } catch {
        failed.value = true;
    }
});

onBeforeUnmount(() => {
    window.removeEventListener('message', onMessage);
    if (container.value) container.value.innerHTML = '';
});
</script>

<template>
    <div v-if="mode === 'inline'">
        <!-- Calendly needs an explicit height and a min-width of 320px. -->
        <div ref="container" :style="{ minWidth: '320px', height: height + 'px' }" />
        <p v-if="failed" class="mt-3 text-sm text-ink/70">
            The scheduler couldn't load.
            <a :href="url" target="_blank" rel="noopener" class="underline">Open it in a new tab</a>.
        </p>
    </div>

    <button v-else type="button" @click="openPopup"
        class="rounded-md bg-ink px-5 py-2 text-sm font-medium text-white hover:bg-ink/80">
        {{ buttonText }}
    </button>
</template>
