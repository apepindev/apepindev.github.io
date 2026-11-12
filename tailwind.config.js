const defaults = require('tailwindcss/defaultTheme');

// Accent colour for highlights, hovers and text selection, in light and dark mode.
const highlight = '#D9F24A';

module.exports = {
    darkMode: 'class',
    content: require('fast-glob').sync([
        'source/**/*.html',
        'source/**/*.md',
        'source/**/*.js',
        'source/**/*.php',
        'source/**/*.vue',
    ]),
    options: {
        safelist: [/language/, /hljs/, /mce/],
    },
    theme: {
        extend: {
            // Values come from the CSS variables in base.css, so dark mode swaps them in one place.
            colors: {
                paper: 'var(--paper)',
                ink: 'var(--ink)',
                muted: 'var(--muted)',
                faint: 'var(--faint)',
                rule: 'var(--rule)',
                hl: 'var(--hl)',
                highlight,
            },
            fontFamily: {
                sans: ['"Schibsted Grotesk"', ...defaults.fontFamily.sans],
                mono: ['"Martian Mono"', ...defaults.fontFamily.mono],
            },
            maxWidth: {
                sheet: '75rem',
                prose: '68ch',
            },
        },
    },
};
