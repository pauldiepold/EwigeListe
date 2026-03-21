import pluginVue from 'eslint-plugin-vue';
import tseslint from '@typescript-eslint/eslint-plugin';
import parser from '@typescript-eslint/parser';
import vueParser from 'vue-eslint-parser';
import skipFormatting from 'eslint-config-prettier';

export default [
    ...pluginVue.configs['flat/essential'],
    {
        files: ['resources/js/inertia/**/*.{ts,vue}'],
        languageOptions: {
            parser: vueParser,
            parserOptions: {
                parser,
                ecmaVersion: 'latest',
                sourceType: 'module',
                extraFileExtensions: ['.vue'],
            },
        },
        plugins: {
            '@typescript-eslint': tseslint,
        },
        rules: {
            ...tseslint.configs.recommended.rules,
            'vue/multi-word-component-names': 'off',
        },
    },
    {
        ignores: [
            'resources/js/components/**',
            'resources/js/lib/**',
            'resources/js/scripts/**',
            'resources/js/app.js',
            'resources/js/bootstrap.js',
            'node_modules/**',
            'public/**',
        ],
    },
    skipFormatting,
];
