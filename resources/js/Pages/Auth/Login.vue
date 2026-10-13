<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Admin sign in" />
    <main class="flex min-h-screen items-center justify-center bg-slate-50 px-4">
        <div class="w-full max-w-sm rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
            <h1 class="mb-6 text-xl font-semibold text-slate-900">Admin sign in</h1>

            <form class="space-y-4" @submit.prevent="submit">
                <p v-if="form.errors.email" id="login-error" role="alert" class="rounded-md bg-red-50 px-3 py-2 text-sm text-red-700">
                    {{ form.errors.email }}
                </p>

                <div>
                    <label for="login-email" class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                    <input id="login-email" v-model="form.email" type="email" required autofocus autocomplete="username"
                        :aria-invalid="form.errors.email ? 'true' : null"
                        :aria-describedby="form.errors.email ? 'login-error' : null"
                        class="w-full rounded-md border border-slate-500 px-3 py-2 text-sm" />
                </div>

                <div>
                    <label for="login-password" class="mb-1 block text-sm font-medium text-slate-700">Password</label>
                    <input id="login-password" v-model="form.password" type="password" required autocomplete="current-password"
                        class="w-full rounded-md border border-slate-500 px-3 py-2 text-sm" />
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input v-model="form.remember" type="checkbox" class="rounded border-slate-500" />
                    Remember me
                </label>

                <button type="submit" :disabled="form.processing"
                    class="w-full rounded-md bg-slate-900 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50">
                    Sign in
                </button>
            </form>
        </div>
    </main>
</template>
