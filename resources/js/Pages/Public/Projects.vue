<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';

const projects = [
    {
        number: '01',
        category: 'Personal project',
        title: 'Laravel Portfolio Platform',
        summary:
            'A custom portfolio built as a full-stack application, with a public-facing site and an authenticated admin area for managing content and professional materials.',
        highlights: [
            'Laravel + Inertia.js + Vue 3',
            'Admin-managed pages and navigation',
            'SEO metadata and resume management',
            'Production deployment workflow',
        ],
        technologies: ['PHP', 'Laravel', 'Inertia.js', 'Vue 3', 'MySQL', 'Tailwind CSS'],
        link: 'https://github.com/wilecoyte78/laravel-portfolio',
        linkLabel: 'View source on GitHub',
    },
    {
        number: '02',
        category: 'Enterprise SaaS · Professional experience',
        title: 'Application Modernization',
        summary:
            'Led a staged modernization of a production application, upgrading PHP 7.4 to PHP 8.3 and Laravel 8 to Laravel 11 while prioritizing compatibility, maintainability, and service continuity.',
        highlights: [
            'Managed framework and dependency compatibility changes',
            'Refactored legacy code into clearer classes and namespaces',
            'Improved model and naming consistency',
            'Used automated testing and incremental deployments to reduce release risk',
        ],
        technologies: ['PHP 8.3', 'Laravel 11', 'MySQL', 'PHPUnit', 'Git'],
        link: null,
        linkLabel: null,
    },
    {
        number: '03',
        category: 'Independent project',
        title: 'ForgeCMS',
        summary:
            'An installable content management system in development, designed around multi-tenant administration, extensible content tools, and a modern Laravel and Vue stack.',
        highlights: [
            'Role-based administration concepts for platform and tenant users',
            'Content editing and site configuration',
            'Modular direction for navigation, forms, and media management',
            'Laravel, Vue, Tailwind CSS, and PostgreSQL',
        ],
        technologies: ['Laravel', 'Vue 3', 'Tailwind CSS', 'PostgreSQL', 'Tiptap'],
        link: null,
        linkLabel: null,
    },
];

const focusAreas = [
    {
        title: 'Backend engineering',
        description: 'Laravel application design, APIs, data modeling, and maintainable PHP systems.',
    },
    {
        title: 'Modernization',
        description: 'Incremental upgrades, legacy refactoring, compatibility work, and safer releases.',
    },
    {
        title: 'Full-stack delivery',
        description: 'Connecting backend behavior to accessible, responsive interfaces and useful workflows.',
    },
];
</script>

<template>
    <PublicLayout>
        <SeoHead
            title="Projects"
            meta-title="Selected Projects | Senior PHP & Laravel Engineer"
            description="Selected software engineering projects covering Laravel application development, enterprise modernization, SaaS architecture, and full-stack delivery."
            keywords="Laravel projects, PHP software engineer, application modernization, full-stack development, SaaS"
        />

        <section aria-labelledby="projects-heading">
            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-brass">
                Selected work
            </p>
            <div class="max-w-3xl">
                <h1
                    id="projects-heading"
                    class="font-display text-4xl font-normal leading-tight text-ink sm:text-5xl"
                >
                    Engineering work, explained.
                </h1>
                <p class="mt-5 max-w-2xl text-base leading-7 text-stone-600 sm:text-lg">
                    A selection of applications and engineering initiatives that reflect how I approach
                    building software: understand the problem, make thoughtful technical decisions,
                    and deliver systems that are easier to maintain and evolve.
                </p>
            </div>

            <div class="mt-10 grid gap-5 sm:grid-cols-3">
                <article
                    v-for="item in focusAreas"
                    :key="item.title"
                    class="border-t-2 border-brass pt-4"
                >
                    <h2 class="font-display text-lg text-ink">{{ item.title }}</h2>
                    <p class="mt-2 text-sm leading-6 text-stone-600">{{ item.description }}</p>
                </article>
            </div>
        </section>

        <section aria-labelledby="work-heading" class="mt-16">
            <div class="mb-6 flex items-end justify-between gap-4 border-b border-ink/10 pb-4">
                <div>
                    <h2 id="work-heading" class="font-display text-2xl text-ink sm:text-3xl">
                        Featured projects
                    </h2>
                    <p class="mt-2 text-sm text-stone-500">
                        Personal builds and selected professional engineering work.
                    </p>
                </div>
                <span class="hidden text-sm text-stone-400 sm:inline">01 — 03</span>
            </div>

            <div class="space-y-6">
                <article
                    v-for="project in projects"
                    :key="project.number"
                    class="rounded-xl border border-ink/10 bg-white/60 p-5 sm:p-7"
                >
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:gap-6">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-ink text-sm font-semibold text-white">
                            {{ project.number }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-brass">
                                {{ project.category }}
                            </p>
                            <h3 class="mt-2 font-display text-2xl text-ink">
                                {{ project.title }}
                            </h3>
                            <p class="mt-3 max-w-3xl text-sm leading-7 text-stone-600 sm:text-base">
                                {{ project.summary }}
                            </p>

                            <ul class="mt-5 grid gap-x-8 gap-y-2 sm:grid-cols-2">
                                <li
                                    v-for="highlight in project.highlights"
                                    :key="highlight"
                                    class="flex items-start gap-2 text-sm leading-6 text-stone-700"
                                >
                                    <span aria-hidden="true" class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-brass"></span>
                                    <span>{{ highlight }}</span>
                                </li>
                            </ul>

                            <div class="mt-5 flex flex-wrap gap-2">
                                <span
                                    v-for="technology in project.technologies"
                                    :key="technology"
                                    class="rounded-full border border-ink/10 bg-paper px-3 py-1 text-xs text-stone-600"
                                >
                                    {{ technology }}
                                </span>
                            </div>

                            <a
                                v-if="project.link"
                                :href="project.link"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-ink underline decoration-brass decoration-2 underline-offset-4 hover:text-brass"
                            >
                                {{ project.linkLabel }}
                                <span aria-hidden="true">↗</span>
                            </a>
                            <p v-else class="mt-6 text-xs text-stone-500">
                                {{ project.category.includes('Professional') ? 'Details are generalized to respect employer confidentiality.' : 'Independent project in active development.' }}
                            </p>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <section class="mt-16 border-t border-ink/10 pt-8" aria-labelledby="contact-heading">
            <h2 id="contact-heading" class="font-display text-2xl text-ink">
                Have a challenging project in mind?
            </h2>
            <p class="mt-3 max-w-2xl text-sm leading-7 text-stone-600 sm:text-base">
                I'm interested in engineering work involving robust backend systems, application
                modernization, and thoughtful full-stack development.
            </p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a
                    href="/contact"
                    class="inline-flex items-center rounded-lg bg-ink px-5 py-3 text-sm font-semibold text-white transition hover:bg-ink/80"
                >
                    Get in touch
                </a>
                <a
                    href="/resume"
                    class="inline-flex items-center rounded-lg border border-ink/15 px-5 py-3 text-sm font-semibold text-ink transition hover:bg-ink/5"
                >
                    View resume
                </a>
            </div>
        </section>
    </PublicLayout>
</template>