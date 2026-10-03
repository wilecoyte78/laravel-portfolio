import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { createSSRApp, h } from 'vue';
import { renderToString } from 'vue/server-renderer';
import { ZiggyVue } from 'ziggy-js';

// Only public pages are server-rendered (see $withoutSsr in
// HandleInertiaRequests). Admin pages call route() while rendering and
// rely on browser globals, so they stay client-rendered.
createServer(
    (page) =>
        createInertiaApp({
            page,
            render: renderToString,
            // Public pages build their full title in SeoHead; admin titles are used as-is.
            title: (title) => title || import.meta.env.VITE_APP_NAME,
            resolve: (name) => {
                const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
                return pages[`./Pages/${name}.vue`];
            },
            setup({ App, props, plugin }) {
                return createSSRApp({ render: () => h(App, props) })
                    .use(plugin)
                    .use(ZiggyVue);
            },
        }),
    { host: '127.0.0.1' },
);
