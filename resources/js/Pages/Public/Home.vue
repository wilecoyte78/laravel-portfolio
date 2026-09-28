<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    page: { type: Object, default: null },
});
</script>

<template>
    <PublicLayout>
        <Head />

        <article v-if="page">
            <div class="prose max-w-none prose-headings:font-display prose-headings:font-normal prose-headings:text-ink" v-html="page.content"></div>
        </article>

        <section v-else class="grid gap-12 md:grid-cols-[1.4fr_1fr] md:gap-16">
            <div>
                <p class="mb-3 text-sm text-stone-500">College Station, TX</p>
                <h1 class="font-display text-5xl leading-[1.05] text-ink sm:text-6xl">
                    Senior full stack engineer, decade deep in Laravel.
                </h1>
                <p class="mt-6 max-w-[46ch] text-lg leading-relaxed text-ink/80">
                    I build enterprise PHP applications — APIs, database architecture, framework
                    upgrades — and pair that with Vue and React on the frontend. Lately, most of
                    my work involves Claude Code as a genuine part of the process, not a novelty.
                </p>

                <p class="mt-8 max-w-[46ch] border-l-2 border-brass py-1 pl-4 text-base italic text-ink/70">
                    Before any of that, I trained as a chef. The Culinary Institute Lenôtre,
                    before Tomball College and a decade of PHP. Both disciplines reward the same
                    thing: getting the fundamentals right under pressure.
                </p>

                <nav class="mt-10 flex flex-wrap gap-x-8 gap-y-3 text-base">
                    <Link href="/bio" class="text-ink underline decoration-brass decoration-2 underline-offset-4 hover:text-brass">
                        Read my bio
                    </Link>
                    <Link href="/resume" class="text-ink underline decoration-brass decoration-2 underline-offset-4 hover:text-brass">
                        Full resume
                    </Link>
                    <Link href="/contact" class="text-ink underline decoration-brass decoration-2 underline-offset-4 hover:text-brass">
                        Get in touch
                    </Link>
                </nav>
            </div>

            <div class="border-t border-ink/10 pt-8 md:border-l md:border-t-0 md:pl-10 md:pt-0">
                <h2 class="font-display text-sm text-stone-500">Currently</h2>
                <ul class="mt-4 space-y-4 text-sm text-ink/80">
                    <li>
                        <span class="block text-ink">Senior Full Stack Developer</span>
                        <span class="text-stone-500">ComplianceHR — since May 2024</span>
                    </li>
                    <li>
                        <span class="block text-ink">PHP 8.x &amp; Laravel 6–11</span>
                        <span class="text-stone-500">Eloquent, REST APIs, GraphQL</span>
                    </li>
                    <li>
                        <span class="block text-ink">Vue.js &amp; React</span>
                        <span class="text-stone-500">frontend, when the backend needs a face</span>
                    </li>
                    <li>
                        <span class="block text-ink">AWS, Docker, CI/CD</span>
                        <span class="text-stone-500">Jenkins, GitHub Actions</span>
                    </li>
                </ul>
            </div>
        </section>
    </PublicLayout>
</template>
