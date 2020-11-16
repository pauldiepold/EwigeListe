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
            boxShadow: {
                green: '0 0 6px 3px rgb(88, 169, 117)',
                xs: '0 0 0 1px rgba(0, 0, 0, 0.05)',
                sm: '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
                default: '0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)',
                md: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                lg: '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
                xl: '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
                '2xl': '0 25px 50px -12px rgba(0, 0, 0, 0.25)',
                inner: 'inset 0 2px 4px 0 rgba(0, 0, 0, 0.06)',
                outline: '0 0 0 3px rgba(66, 153, 225, 0.5)',
                none: 'none',
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
