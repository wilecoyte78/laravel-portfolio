<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import CalendlyEmbed from '@/Components/CalendlyEmbed.vue';

defineProps({
    calendlyUrl: { type: String, default: null },
});

const inertiaPage = usePage();

const form = useForm({
    name: '',
    email: '',
    message: '',
    website: '', // honeypot - stays empty for real visitors
});

function submit() {
    form.post(route('contact.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset('name', 'email', 'message'),
    });
}
</script>

<template>
    <PublicLayout>
        <SeoHead
            :title="inertiaPage.props.page?.title ?? 'Contact'"
            :meta-title="inertiaPage.props.page?.meta_title ?? ''"
            :description="inertiaPage.props.page?.meta_description ?? ''"
            :keywords="inertiaPage.props.page?.meta_keywords ?? ''"
        />


        <div class="grid gap-12 md:grid-cols-[1fr_1.2fr] md:gap-16">
            <div>
                <h1 class="font-display text-4xl text-ink">Let's talk</h1>
                <p class="mt-4 max-w-[38ch] text-ink/70">
                    Hiring, consulting, or just have a question about how this site was built —
                    send a message and I'll get back to you.
                </p>
                <section v-if="calendlyUrl" class="mt-20 border-t border-ink/10 pt-12">
                    <h2 class="font-display text-3xl text-ink">Book a call</h2>
                    <p class="mt-3 max-w-[48ch] text-ink/70">
                        Prefer to talk it through? Pick a time that works for you.
                    </p>
                    <div class="mt-8">
                        <CalendlyEmbed :url="calendlyUrl" mode="popup" />
                    </div>
                </section>
            </div>

            <div>
                <!-- Live regions stay mounted so screen readers announce new messages (WCAG 4.1.3). -->
                <div role="status">
                    <p v-if="inertiaPage.props.flash?.success" class="mb-4 rounded-md bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        {{ inertiaPage.props.flash.success }}
                    </p>
                </div>
                <div role="alert">
                    <p v-if="inertiaPage.props.flash?.error" class="mb-4 rounded-md bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ inertiaPage.props.flash.error }}
                    </p>
                </div>

                <form class="space-y-5" @submit.prevent="submit">
                    <p class="text-sm text-ink/70">All fields are required.</p>

                    <div>
                        <label for="contact-name" class="mb-1 block text-sm font-medium text-ink/80">Name</label>
                        <input id="contact-name" v-model="form.name" type="text" required autocomplete="name"
                            :aria-invalid="form.errors.name ? 'true' : null"
                            :aria-describedby="form.errors.name ? 'contact-name-error' : null"
                            class="w-full border-b border-ink/50 bg-transparent py-2 text-sm focus:border-brass-dark" />
                        <p v-if="form.errors.name" id="contact-name-error" class="mt-1 text-sm text-red-700">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label for="contact-email" class="mb-1 block text-sm font-medium text-ink/80">Email</label>
                        <input id="contact-email" v-model="form.email" type="email" required autocomplete="email"
                            :aria-invalid="form.errors.email ? 'true' : null"
                            :aria-describedby="form.errors.email ? 'contact-email-error' : null"
                            class="w-full border-b border-ink/50 bg-transparent py-2 text-sm focus:border-brass-dark" />
                        <p v-if="form.errors.email" id="contact-email-error" class="mt-1 text-sm text-red-700">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label for="contact-message" class="mb-1 block text-sm font-medium text-ink/80">Message</label>
                        <textarea id="contact-message" v-model="form.message" required rows="5"
                            :aria-invalid="form.errors.message ? 'true' : null"
                            :aria-describedby="form.errors.message ? 'contact-message-error' : null"
                            class="w-full border-b border-ink/50 bg-transparent py-2 text-sm focus:border-brass-dark"></textarea>
                        <p v-if="form.errors.message" id="contact-message-error" class="mt-1 text-sm text-red-700">{{ form.errors.message }}</p>
                    </div>

                    <!-- Honeypot field: hidden from real users, bots often fill every input they find -->
                    <div class="hidden" aria-hidden="true">
                        <label for="contact-website">Website</label>
                        <input id="contact-website" v-model="form.website" type="text" tabindex="-1" autocomplete="off" />
                    </div>

                    <button type="submit" :disabled="form.processing"
                        class="rounded-md bg-ink px-5 py-2 text-sm font-medium text-white hover:bg-ink/80 disabled:opacity-50">
                        Send message
                    </button>
                </form>
            </div>
        </div>
    </PublicLayout>
</template>
