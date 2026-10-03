<script setup>
import { computed } from 'vue';
import { SITE_NAME, DEFAULT_DESCRIPTION } from '@/seo';

/**
 * SEO inputs for the page create/edit forms.
 * Expects an Inertia form containing: title, slug, meta_title,
 * meta_description and meta_keywords.
 */
const props = defineProps({
    form: { type: Object, required: true },
});

const TITLE_LIMIT = 60;
const DESCRIPTION_LIMIT = 160;

const previewTitle = computed(
    () => props.form.meta_title || (props.form.title ? `${props.form.title} - ${SITE_NAME}` : SITE_NAME),
);
const previewDescription = computed(() => props.form.meta_description || DEFAULT_DESCRIPTION);

function counterClass(length, limit) {
    return length > limit ? 'text-amber-600' : 'text-slate-400';
}
</script>

<template>
    <fieldset class="space-y-5 rounded-lg border border-slate-200 bg-white p-5">
        <legend class="px-2 text-sm font-semibold text-slate-900">SEO</legend>

        <div>
            <label class="mb-1 flex items-center justify-between text-sm font-medium text-slate-700" for="meta_title">
                <span>
                    SEO title
                    <span class="font-normal text-slate-400">(blank = page title + site name)</span>
                </span>
                <span class="text-xs font-normal" :class="counterClass((form.meta_title || '').length, TITLE_LIMIT)">
                    {{ (form.meta_title || '').length }}/{{ TITLE_LIMIT }}
                </span>
            </label>
            <input id="meta_title" v-model="form.meta_title" type="text" maxlength="255"
                class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
            <p v-if="form.errors.meta_title" class="mt-1 text-sm text-red-600">{{ form.errors.meta_title }}</p>
        </div>

        <div>
            <label class="mb-1 flex items-center justify-between text-sm font-medium text-slate-700" for="meta_description">
                <span>Meta description</span>
                <span class="text-xs font-normal" :class="counterClass((form.meta_description || '').length, DESCRIPTION_LIMIT)">
                    {{ (form.meta_description || '').length }}/{{ DESCRIPTION_LIMIT }}
                </span>
            </label>
            <textarea id="meta_description" v-model="form.meta_description" rows="3" maxlength="255"
                class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm"></textarea>
            <p v-if="form.errors.meta_description" class="mt-1 text-sm text-red-600">{{ form.errors.meta_description }}</p>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700" for="meta_keywords">
                Keywords <span class="font-normal text-slate-400">(comma separated)</span>
            </label>
            <input id="meta_keywords" v-model="form.meta_keywords" type="text" maxlength="255"
                placeholder="php, laravel, vue, senior developer"
                class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm" />
            <p v-if="form.errors.meta_keywords" class="mt-1 text-sm text-red-600">{{ form.errors.meta_keywords }}</p>
        </div>

        <div class="rounded-md bg-slate-50 p-4">
            <p class="mb-2 text-xs font-medium uppercase tracking-wide text-slate-400">Search result preview</p>
            <p class="truncate text-base text-blue-700">{{ previewTitle }}</p>
            <p class="text-xs text-emerald-700">/{{ form.slug || '' }}</p>
            <p class="mt-1 line-clamp-2 text-sm text-slate-600">{{ previewDescription }}</p>
        </div>
    </fieldset>
</template>
