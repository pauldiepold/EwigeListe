module.exports = {
    prefix: 'tw-',
    purge: {
        enabled: false,
    },
    theme: {
        extend: {
            colors: {
                gray: {
                    200: '#f3f3f3',
                    300: '#e4e4e4',
                    400: '#d1d1d1',
                    500: '#afafaf',
                    600: '#919191',
                    700: '#6b6b6b',
                    800: '#424242',
                    900: '#2b2b2b',
                },
                orange: {
                    dark: 'rgb(176, 74, 67)',
                    default: '#D16341',
                    lighter: 'rgba(209, 99, 65, 0.18)',
                },
                blue: {
                    darker: '#21295C',
                    dark: 'rgb(5, 75, 109)',
                    default: '#065A82',
                    light: '#0692c6',
                    lighter: '#92d2ff',
                    lightest: '#cbe7f7',
                },
            },
            spacing: {
                '7': '1.75rem',
            },
            maxWidth: {
                '6xs': '4rem',
                '5xs': '6rem',
                '4xs': '8rem',
                '3xs': '12rem',
                '2xs': '16rem',
            },
            minWidth: {
                '5xs': '6rem',
                '6xs': '4rem',
                '7xs': '3rem',
                '8xs': '2rem',
            },
            gridTemplateColumns: {
                'livegame': 'minmax(max-content, max-content) minmax(0, 1fr) minmax(max-content, max-content)',
            },
            gridTemplateRows: {
                'livegame': 'minmax(max-content, max-content) minmax(0, 1fr) minmax(max-content, max-content)',
                'livegame_lg': 'minmax(max-content, max-content) minmax(20em, 1fr) minmax(max-content, max-content) minmax(max-content, max-content)',
            },
            borderWidth: {
                '3': '3px',
            }
        },
    },
    variants: {
        borderStyle: ['responsive', 'focus'],
        borderWidth: ['responsive', 'focus'],
    },
}
