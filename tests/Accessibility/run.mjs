// axe-core accessibility check for the public pages (WCAG 2.2 AA).
//
// Usage (from the repository root):
//   npm ci                                   # app dependencies (Vite, Vue)
//   npm ci --prefix tests/Accessibility      # checker dependencies (axe-core, jsdom)
//   node tests/Accessibility/run.mjs [--json]
//   python3 tests/Accessibility/contrast.py  # palette contrast
//
// The checker keeps its own package.json so the application's dependencies
// are unchanged. Pass a path to check a different checkout of this repo.
// Each public page is rendered to HTML with Vue's server renderer, with
// @inertiajs/vue3 swapped for a small mock (inertia-mock.js), then checked
// with axe-core in jsdom against WCAG 2.0/2.1/2.2 A + AA and best-practice rules.
//
// Limits: jsdom has no layout engine, so color-contrast is disabled here
// (use contrast.py for palette checks). Admin pages and client-only behavior
// (focus management, Escape handling, dialogs) still need a manual keyboard pass.
//
// Exit code: 0 = no violations, 1 = violations found, 2 = setup error.

import fs from 'node:fs';
import path from 'node:path';
import { createRequire } from 'node:module';
import { pathToFileURL, fileURLToPath } from 'node:url';

const here = path.dirname(fileURLToPath(import.meta.url));
const args = process.argv.slice(2);
const asJson = args.includes('--json');
const repo = path.resolve(args.find((a) => !a.startsWith('--')) ?? path.join(here, '../..'));

if (!fs.existsSync(path.join(repo, 'resources/js/Pages/Public'))) {
    console.error(`Not a laravel-portfolio checkout: ${repo}`);
    process.exit(2);
}

const repoRequire = createRequire(path.join(repo, 'package.json'));
// CommonJS packages arrive as `{ default: exports }`; unwrap so named exports work.
const fromRepo = async (id) => {
    const mod = await import(pathToFileURL(repoRequire.resolve(id)).href);
    return mod.default && Object.keys(mod).length === 1 ? mod.default : mod;
};
// Load Vite's ESM entry directly (its CommonJS API is deprecated).
const viteEsm = () => import(pathToFileURL(
    path.join(path.dirname(repoRequire.resolve('vite/package.json')), 'dist/node/index.js'),
).href);

// Pages to render and the props each one receives. Add new public pages here.
const PAGES = {
    Projects: {},
    Contact: { calendlyUrl: 'https://calendly.com/example' },
    Page: { page: { title: 'Bio', content: '<h2>About</h2><p>Hello.</p>' } },
    Home: { page: { title: 'Home', content: '<h2>Welcome</h2><p>Hello.</p>' } },
    Resume: { resume: null },
};

const RULESETS = ['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa', 'best-practice'];

// The mock must sit inside the checked repo so its `import 'vue'` resolves to
// the same Vue instance the components use. When checking another checkout,
// a temporary copy is placed there and removed again in `finally`.
const bundledMock = path.join(here, 'inertia-mock.js');
const mockInsideRepo = !path.relative(repo, bundledMock).startsWith('..');
const mockPath = mockInsideRepo ? bundledMock : path.join(repo, '.axe-inertia-mock.js');
if (!mockInsideRepo) fs.copyFileSync(bundledMock, mockPath);

globalThis.route = (name) => `/${name}`;

let server;
let exitCode = 0;
const report = {};

try {
    const { createServer } = await viteEsm();
    const pluginVue = await import(pathToFileURL(repoRequire.resolve('@vitejs/plugin-vue')).href);
    const vue = pluginVue.default?.default ?? pluginVue.default ?? pluginVue;
    const { createSSRApp, h } = await fromRepo('vue');
    const { renderToString } = await fromRepo('vue/server-renderer');
    const { JSDOM } = await import('jsdom');
    const axeSource = fs.readFileSync(createRequire(import.meta.url).resolve('axe-core/axe.min.js'), 'utf8');

    server = await createServer({
        root: repo,
        configFile: false,
        logLevel: 'error',
        plugins: [vue()],
        resolve: { alias: { '@inertiajs/vue3': mockPath, '@': path.join(repo, 'resources/js') } },
        server: { middlewareMode: true },
        appType: 'custom',
    });

    for (const [name, props] of Object.entries(PAGES)) {
        const file = `/resources/js/Pages/Public/${name}.vue`;
        if (!fs.existsSync(path.join(repo, file))) continue;

        const mod = await server.ssrLoadModule(file);
        const html = await renderToString(createSSRApp({ render: () => h(mod.default, props) }));
        const dom = new JSDOM(
            `<!DOCTYPE html><html lang="en"><head><title>${name}</title></head><body>${html}</body></html>`,
            { runScripts: 'outside-only' },
        );
        dom.window.eval(axeSource);

        const result = await dom.window.axe.run(dom.window.document, {
            runOnly: RULESETS,
            rules: { 'color-contrast': { enabled: false } },
        });

        report[name] = result.violations.map((v) => ({
            id: v.id,
            impact: v.impact,
            help: v.help,
            helpUrl: v.helpUrl,
            targets: v.nodes.map((n) => n.target.join(' ')),
        }));
        if (result.violations.length) exitCode = 1;

        if (!asJson) {
            console.log(`${name}: ${result.violations.length} violations, ${result.passes.length} passing rules`);
            for (const v of report[name]) {
                console.log(`  - [${v.impact}] ${v.id}: ${v.help}`);
                console.log(`    ${v.targets.slice(0, 3).join(' | ')}`);
                console.log(`    ${v.helpUrl}`);
            }
        }
    }

    if (asJson) console.log(JSON.stringify(report, null, 2));
} catch (error) {
    console.error(error);
    exitCode = 2;
} finally {
    await server?.close();
    if (!mockInsideRepo) fs.rmSync(mockPath, { force: true });
}

process.exit(exitCode);
