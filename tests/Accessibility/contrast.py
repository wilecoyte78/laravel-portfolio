"""WCAG contrast checks for the laravel-portfolio color palette.

Usage: python3 tests/Accessibility/contrast.py
Text needs 4.5:1 (3:1 for large text); UI parts like input borders need 3:1.
Edit PAIRS when the Tailwind palette (tailwind.config.js) changes.
"""

def luminance(hex_color):
    h = hex_color.lstrip('#')
    channels = [int(h[i:i + 2], 16) / 255 for i in (0, 2, 4)]
    channels = [c / 12.92 if c <= 0.03928 else ((c + 0.055) / 1.055) ** 2.4 for c in channels]
    return 0.2126 * channels[0] + 0.7152 * channels[1] + 0.0722 * channels[2]


def blend(fg, bg, alpha):
    """Color of `fg` at `alpha` opacity over `bg` (for Tailwind classes like ink/50)."""
    f = [int(fg[i:i + 2], 16) for i in (1, 3, 5)]
    b = [int(bg[i:i + 2], 16) for i in (1, 3, 5)]
    return '#' + ''.join('%02x' % round(alpha * x + (1 - alpha) * y) for x, y in zip(f, b))


def ratio(a, b):
    hi, lo = sorted([luminance(a), luminance(b)], reverse=True)
    return (hi + 0.05) / (lo + 0.05)


INK, PAPER, WHITE = '#14171F', '#F7F5F0', '#FFFFFF'

# (label, foreground, background, required ratio or None for decorative-only colors)
PAIRS = [
    ('brass-dark text on paper', '#86602A', PAPER, 4.5),
    ('brass on paper - decorative only, never text', '#B8863F', PAPER, None),
    ('stone-500 text on paper', '#626976', PAPER, 4.5),
    ('stone-600 text on paper', '#565D6B', PAPER, 4.5),
    ('ink/70 text on paper', blend(INK, PAPER, 0.7), PAPER, 4.5),
    ('white text on ink button', WHITE, INK, 4.5),
    ('ink/50 input border on paper', blend(INK, PAPER, 0.5), PAPER, 3.0),
    ('red-700 error text on paper', '#b91c1c', PAPER, 4.5),
    ('focus outline (brass-dark) on white', '#86602A', WHITE, 3.0),
    ('slate-500 text on white (admin)', '#64748b', WHITE, 4.5),
    ('slate-500 input border on white (admin)', '#64748b', WHITE, 3.0),
]

if __name__ == '__main__':
    failures = 0
    for label, fg, bg, need in PAIRS:
        r = ratio(fg, bg)
        if need is None:
            print(f"INFO  {r:5.2f}:1 (no minimum)       {label}")
            continue
        ok = r >= need
        failures += not ok
        print(f"{'PASS' if ok else 'FAIL'}  {r:5.2f}:1 (needs {need}:1)  {label}")
    raise SystemExit(1 if failures else 0)
