import globals from 'globals';
import path from 'node:path';
import {fileURLToPath} from 'node:url';
import js from '@eslint/js';
import {FlatCompat} from '@eslint/eslintrc';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const compat = new FlatCompat({
    baseDirectory: __dirname,
    recommendedConfig: js.configs.recommended,
    allConfig: js.configs.all,
});

export default [{
    ignores: ['**/vendor/*.js'],
}, ...compat.extends('eslint:recommended'), {
    languageOptions: {
        globals: {
            ...globals.browser,
        },

        ecmaVersion: 'latest',
        sourceType: 'module',
    },

    rules: {
        'array-bracket-spacing': ['error', 'never'],
        'brace-style': 'error',
        camelcase: 'off',
        'comma-dangle': ['error', 'always-multiline'],

        'comma-spacing': ['error', {
            before: false,
            after: true,
        }],

        'default-case': 'error',
        eqeqeq: 'error',

        indent: ['error', 4, {
            SwitchCase: 1,
        }],

        'key-spacing': ['error', {
            beforeColon: false,
            afterColon: true,
        }],

        'no-multiple-empty-lines': 'error',
        'no-prototype-builtins': 'off',
        'no-trailing-spaces': 'error',
        'no-unused-vars': 'off',
        'no-undef': 'off',
        'no-var': 'error',
        'object-curly-spacing': ['error', 'never'],
        'padded-blocks': ['error', 'never'],
        'prefer-const': 'error',
        quotes: ['error', 'single'],
        semi: 'error',
        'space-before-function-paren': ['error', 'never'],
        'space-infix-ops': 'error',
        'space-in-parens': ['error', 'never'],
        yoda: 'error',
    },
}];