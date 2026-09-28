<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    items: { type: Array, default: () => [] },
});

// Desktop hover dropdowns
const openId = ref(null);
function toggle(id) {
    openId.value = openId.value === id ? null : id;
}
function close() {
    openId.value = null;
}

// Mobile menu
const mobileMenuOpen = ref(false);
const mobileOpenId = ref(null);

function toggleMobileMenu() {
    mobileMenuOpen.value = !mobileMenuOpen.value;
    mobileOpenId.value = null;
}
function toggleMobileItem(id) {
    mobileOpenId.value = mobileOpenId.value === id ? null : id;
}
function closeMobileMenu() {
    mobileMenuOpen.value = false;
    mobileOpenId.value = null;
}
</script>

<template>
    <div>
        <!-- Desktop nav (hidden below md breakpoint) -->
        <nav class="hidden items-center gap-6 md:flex" @mouseleave="close">
            <template v-for="item in items" :key="item.id">
                <div v-if="item.children && item.children.length" class="relative">
                    <button
                        type="button"
                        class="flex items-center gap-1 text-sm font-medium text-ink/70 hover:text-ink"
                        @click="toggle(item.id)"
                        @mouseenter="openId = item.id"
                    >
                        {{ item.label }}
                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div
                        v-show="openId === item.id"
                        class="absolute left-0 top-full z-20 mt-2 min-w-[10rem] rounded-lg border border-ink/10 bg-paper py-1 shadow-lg"
                    >
                        <Link
                            v-if="item.url"
                            :href="item.url"
                            class="block px-4 py-2 text-sm text-ink/80 hover:bg-ink/5"
                            @click="close"
                        >
                            {{ item.label }} overview
                        </Link>
                        <Link
                            v-for="child in item.children"
                            :key="child.id"
                            :href="child.url || '#'"
                            class="block px-4 py-2 text-sm text-ink/80 hover:bg-ink/5"
                            @click="close"
                        >
                            {{ child.label }}
                        </Link>
                    </div>
                </div>

                <Link
                    v-else
                    :href="item.url || '#'"
                    class="text-sm font-medium text-ink/70 hover:text-ink"
                >
                    {{ item.label }}
                </Link>
            </template>
        </nav>

        <!-- Mobile hamburger toggle (hidden at md and above) -->
        <button
            type="button"
            class="inline-flex items-center justify-center rounded-md p-2 text-ink/70 hover:bg-ink/5 md:hidden"
            :aria-expanded="mobileMenuOpen"
            aria-label="Toggle menu"
            @click="toggleMobileMenu"
        >
            <svg v-if="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
            </svg>
            <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Mobile menu panel -->
        <div
            v-if="mobileMenuOpen"
            class="absolute inset-x-0 top-full z-30 border-b border-ink/10 bg-paper px-6 py-4 shadow-lg md:hidden"
        >
            <template v-for="item in items" :key="item.id">
                <div v-if="item.children && item.children.length" class="py-1">
                    <button
                        type="button"
                        class="flex w-full items-center justify-between py-2 text-left text-sm font-medium text-ink/70"
                        @click="toggleMobileItem(item.id)"
                    >
                        {{ item.label }}
                        <svg
                            class="h-4 w-4 transition-transform"
                            :class="{ 'rotate-180': mobileOpenId === item.id }"
                            viewBox="0 0 20 20" fill="currentColor"
                        >
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div v-show="mobileOpenId === item.id" class="ml-3 border-l border-ink/10 pl-3">
                        <Link
                            v-if="item.url"
                            :href="item.url"
                            class="block py-2 text-sm text-ink/70"
                            @click="closeMobileMenu"
                        >
                            {{ item.label }} overview
                        </Link>
                        <Link
                            v-for="child in item.children"
                            :key="child.id"
                            :href="child.url || '#'"
                            class="block py-2 text-sm text-ink/70"
                            @click="closeMobileMenu"
                        >
                            {{ child.label }}
                        </Link>
                    </div>
                </div>

                <Link
                    v-else
                    :href="item.url || '#'"
                    class="block py-2 text-sm font-medium text-ink/70"
                    @click="closeMobileMenu"
                >
                    {{ item.label }}
                </Link>
            </template>
        </div>
    </div>
</template>
