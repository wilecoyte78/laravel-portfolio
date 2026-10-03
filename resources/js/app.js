import { createSSRApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from 'ziggy-js';
import 'vue3-toastify/dist/index.css';

createInertiaApp({
    // Public pages build their full title in SeoHead; admin titles are used as-is.
    title: (title) => title || import.meta.env.VITE_APP_NAME || 'Laravel',
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        // createSSRApp hydrates server-rendered HTML when present and
        // behaves like createApp when the page was rendered client-side only.
        createSSRApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
});
