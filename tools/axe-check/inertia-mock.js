import { h, reactive, defineComponent } from 'vue';
const pageState = reactive({ url: '/projects', props: { navigation: [
  { id: 1, label: 'Home', url: '/', children: [] },
  { id: 2, label: 'About', url: null, children: [{ id: 3, label: 'Resume', url: '/resume' }, { id: 4, label: 'GitHub', url: 'https://github.com', target: '_blank' }] },
  { id: 5, label: 'Projects', url: '/projects', children: [] }],
  flash: { success: 'Thanks, message sent.', error: null }, auth: { user: { name: 'Ben' } }, errors: {}, seo: {} } });
export const usePage = () => pageState;
export const Link = defineComponent({ props: ['href'], setup(p, { slots, attrs }) { return () => h('a', { ...attrs, href: p.href }, slots.default?.()); } });
export const Head = defineComponent({ setup() { return () => null; } });
export const router = { post() {}, delete() {}, on() {} };
export function useForm(d) { return reactive({ ...d, errors: { email: 'The email field must be a valid email address.' }, processing: false, post() {}, reset() {} }); }
