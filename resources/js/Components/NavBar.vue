<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import NavLink from '@/Components/NavLink.vue';

defineProps({
    items: { type: Array, default: () => [] },
});

// Desktop dropdowns
const navRef = ref(null);
const openId = ref(null);

function toggle(id) {
    openId.value = openId.value === id ? null : id;
}
function close() {
    openId.value = null;
}

// Hover only applies to a real mouse, not touch or pen
function onItemEnter(e, id) {
    if (e.pointerType === 'mouse') openId.value = id;
}
function onItemLeave(e) {
    if (e.pointerType === 'mouse') close();
}

// Close when clicking/tapping anywhere outside the nav
function onDocPointerDown(e) {
    if (navRef.value && !navRef.value.contains(e.target)) close();
}
function onKeydown(e) {
    if (e.key === 'Escape') close();
}

onMounted(() => {
    document.addEventListener('pointerdown', onDocPointerDown);
    document.addEventListener('keydown', onKeydown);
});
onBeforeUnmount(() => {
    document.removeEventListener('pointerdown', onDocPointerDown);
    document.removeEventListener('keydown', onKeydown);
});

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
    <div v-if="items.length > 0">
        <!-- Desktop nav (hidden below md breakpoint) -->
        <nav ref="navRef" class="hidden items-center gap-6 md:flex">
            <template v-for="item in items" :key="item.id">
                <div
                    v-if="item.children && item.children.length"
                    class="relative"
                    @pointerenter="onItemEnter($event, item.id)"
                    @pointerleave="onItemLeave($event)"
                >
                    <button
                        type="button"
                        class="flex items-center gap-1 text-sm font-medium text-ink/70 hover:text-ink"
                        :aria-expanded="openId === item.id"
                        @click="toggle(item.id)"
                    >
                        {{ item.label }}
                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- pt-2 instead of mt-2: padding is part of this element, so hover stays continuous -->
                    <div
                        v-show="openId === item.id"
                        class="absolute left-0 top-full z-20 min-w-[10rem] pt-2"
                    >
                        <div class="rounded-lg border border-ink/10 bg-paper py-1 shadow-lg">
                            <NavLink
                                v-if="item.url"
                                :href="item.url"
                                :target="item.target && item.target !== '_self' ? item.target : null"
                                class="block px-4 py-2 text-sm text-ink/80 hover:bg-ink/5"
                                @click="close"
                            >
                                {{ item.label }}
                            </NavLink>
                            <NavLink
                                v-for="child in item.children"
                                :key="child.id"
                                :href="child.url || '#'"
                                :target="child.target && child.target !== '_self' ? child.target : null"
                                class="block px-4 py-2 text-sm text-ink/80 hover:bg-ink/5"
                                @click="close"
                            >
                                {{ child.label }}
                            </NavLink>
                        </div>
                    </div>
                </div>

                <NavLink
                    v-else
                    :href="item.url || '#'"
                    :target="item.target && item.target !== '_self' ? item.target : null"
                    class="text-sm font-medium text-ink/70 hover:text-ink"
                >
                    {{ item.label }}
                </NavLink>
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
                        <NavLink
                            v-if="item.url"
                            :href="item.url"
                            :target="item.target && item.target !== '_self' ? item.target : null"
                            class="block py-2 text-sm text-ink/70"
                            @click="closeMobileMenu"
                        >
                            {{ item.label }}
                        </NavLink>
                        <NavLink
                            v-for="child in item.children"
                            :key="child.id"
                            :href="child.url || '#'"
                            :target="child.target && child.target !== '_self' ? child.target : null"
                            class="block py-2 text-sm text-ink/70"
                            @click="closeMobileMenu"
                        >
                            {{ child.label }}
                        </NavLink>
                    </div>
                </div>

                <NavLink
                    v-else
                    :href="item.url || '#'"
                    :target="item.target && item.target !== '_self' ? item.target : null"
                    class="block py-2 text-sm font-medium text-ink/70"
                    @click="closeMobileMenu"
                >
                    {{ item.label }}
                </NavLink>
            </template>
        </div>
    </div>
</template>