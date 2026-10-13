# Accessibility checks for laravel-portfolio

Tools used to verify the WCAG 2.2 AA work in
[wilecoyte78/laravel-portfolio PR #2](https://github.com/wilecoyte78/laravel-portfolio/pull/2).

## axe page check

Renders the public Vue pages (Projects, Contact, Page, Home, Resume) to HTML and runs
[axe-core](https://github.com/dequelabs/axe-core) against WCAG 2.0/2.1/2.2 A + AA and
best-practice rules.

```bash
cd tools/axe-check
npm install                                  # once
(cd /path/to/laravel-portfolio && npm ci)    # the target checkout needs its own deps
node run.mjs /path/to/laravel-portfolio      # add --json for machine-readable output
```

Exit code `0` means no violations, `1` means violations were found, `2` means a setup error.

To compare against main, check out `main` in the repo, run it again, and diff the results.
At the time of PR #2, main had 5 violations and the PR branch had 0.

- `inertia-mock.js` stands in for `@inertiajs/vue3` (fake navigation, flash message, and a
  form error, so error states get checked too). Edit it to try other states.
- Add new public pages to `PAGES` in `run.mjs`.
- Color contrast is skipped because jsdom has no layout engine. Use `contrast.py` for that.

## Palette contrast

```bash
python3 tools/axe-check/contrast.py
```

Checks the Tailwind palette pairs (text 4.5:1, borders and focus outline 3:1). Update
`PAIRS` whenever `tailwind.config.js` changes.

## Not covered by these scripts

- Admin pages (they need auth/route props). Review them by hand or add them to `PAGES`
  with mock props.
- Keyboard behavior: focus moving after navigation, Escape on menus, and the admin
  Navigation dialog's focus trap.
- Third-party content (the Calendly popup and the PDF viewer) and admin-written page content.
