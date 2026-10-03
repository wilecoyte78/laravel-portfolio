<script setup>
import { ref, watch, onBeforeUnmount } from 'vue';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Image from '@tiptap/extension-image';

const props = defineProps({
    modelValue: { type: String, default: '' },
});
const emit = defineEmits(['update:modelValue']);

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit,
        Link.configure({
            openOnClick: false,
            HTMLAttributes: {
                // TipTap's Link extension defaults to target="_blank" and
                // rel="noopener noreferrer nofollow" on every link, which
                // clobbers internal links like /bio or /resume that should
                // stay in the same tab. Clear those defaults; add target
                // manually per-link via the HTML source toggle if needed.
                target: null,
                rel: null,
            },
        }),
        Image,
    ],
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML());
    },
    editorProps: {
        attributes: {
            class: 'prose max-w-none min-h-[16rem] px-4 py-3 focus:outline-none',
        },
    },
});

// Keep editor in sync if the parent resets modelValue (e.g. loading a page for edit)
watch(
    () => props.modelValue,
    (value) => {
        if (editor.value && !showHtmlSource.value && value !== editor.value.getHTML()) {
            editor.value.commands.setContent(value, false);
        }
    }
);

onBeforeUnmount(() => {
    editor.value?.destroy();
});

function setLink() {
    const url = window.prompt('URL');
    if (url === null) return;
    if (url === '') {
        editor.value.chain().focus().extendMarkRange('link').unsetLink().run();
        return;
    }
    editor.value.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
}

function addImage() {
    const url = window.prompt('Image URL');
    if (url) {
        editor.value.chain().focus().setImage({ src: url }).run();
    }
}

// --- Raw HTML source mode -------------------------------------------
// TipTap's toolbar has no "paste as HTML" option by default - pasting
// HTML text into the rendered editor just inserts it as literal escaped
// text. This toggle swaps to a plain textarea holding the raw HTML, so
// pasting a full block of markup (e.g. from a drafted page) actually
// works, then applies it back into the rendered editor on toggle-off.
const showHtmlSource = ref(false);
const htmlSource = ref('');

function toggleHtmlSource() {
    if (!showHtmlSource.value) {
        htmlSource.value = editor.value.getHTML();
        showHtmlSource.value = true;
    } else {
        editor.value.commands.setContent(htmlSource.value, true);
        showHtmlSource.value = false;
    }
}
</script>

<template>
    <div class="rounded-lg border border-slate-300 bg-white">
        <div v-if="editor" class="flex flex-wrap items-center gap-1 border-b border-slate-200 p-2">
            <button type="button" class="toolbar-btn" :disabled="showHtmlSource" :class="{ 'is-active': editor.isActive('bold') }" @click="editor.chain().focus().toggleBold().run()"><b>B</b></button>
            <button type="button" class="toolbar-btn" :disabled="showHtmlSource" :class="{ 'is-active': editor.isActive('italic') }" @click="editor.chain().focus().toggleItalic().run()"><i>I</i></button>
            <button type="button" class="toolbar-btn" :disabled="showHtmlSource" :class="{ 'is-active': editor.isActive('strike') }" @click="editor.chain().focus().toggleStrike().run()"><s>S</s></button>
            <span class="mx-1 h-5 w-px bg-slate-200"></span>
            <button type="button" class="toolbar-btn" :disabled="showHtmlSource" :class="{ 'is-active': editor.isActive('heading', { level: 2 }) }" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()">H2</button>
            <button type="button" class="toolbar-btn" :disabled="showHtmlSource" :class="{ 'is-active': editor.isActive('heading', { level: 3 }) }" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()">H3</button>
            <span class="mx-1 h-5 w-px bg-slate-200"></span>
            <button type="button" class="toolbar-btn" :disabled="showHtmlSource" :class="{ 'is-active': editor.isActive('bulletList') }" @click="editor.chain().focus().toggleBulletList().run()">• List</button>
            <button type="button" class="toolbar-btn" :disabled="showHtmlSource" :class="{ 'is-active': editor.isActive('orderedList') }" @click="editor.chain().focus().toggleOrderedList().run()">1. List</button>
            <button type="button" class="toolbar-btn" :disabled="showHtmlSource" :class="{ 'is-active': editor.isActive('blockquote') }" @click="editor.chain().focus().toggleBlockquote().run()">"</button>
            <span class="mx-1 h-5 w-px bg-slate-200"></span>
            <button type="button" class="toolbar-btn" :disabled="showHtmlSource" @click="setLink">Link</button>
            <button type="button" class="toolbar-btn" :disabled="showHtmlSource" @click="addImage">Image</button>
            <span class="mx-1 h-5 w-px bg-slate-200"></span>
            <button type="button" class="toolbar-btn" :disabled="showHtmlSource" @click="editor.chain().focus().undo().run()">Undo</button>
            <button type="button" class="toolbar-btn" :disabled="showHtmlSource" @click="editor.chain().focus().redo().run()">Redo</button>
            <span class="mx-1 h-5 w-px bg-slate-200"></span>
            <button type="button" class="toolbar-btn font-mono" :class="{ 'is-active': showHtmlSource }" @click="toggleHtmlSource">
                {{ showHtmlSource ? 'Apply HTML' : '</> Edit HTML' }}
            </button>
        </div>

        <textarea
            v-if="showHtmlSource"
            v-model="htmlSource"
            class="min-h-[16rem] w-full resize-y px-4 py-3 font-mono text-sm focus:outline-none"
            placeholder="Paste raw HTML here, then click &quot;Apply HTML&quot; above"
        ></textarea>
        <EditorContent v-else :editor="editor" />
    </div>
</template>

<style scoped>
.toolbar-btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8125rem;
    border-radius: 0.375rem;
    color: #334155;
}
.toolbar-btn:hover:not(:disabled) {
    background: #f1f5f9;
}
.toolbar-btn.is-active {
    background: #e2e8f0;
    color: #0f172a;
}
.toolbar-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}
</style>
