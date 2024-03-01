module.exports = {
    env: {
        browser: true,
        es2021: true,
    },
    extends: [
        'standard',
        'plugin:editorconfig/all',
    ],
    parserOptions: {
        ecmaVersion: 'latest',
        sourceType: 'module',
    },
    plugins: [
        'editorconfig',
    ],
    rules: {
        'no-tabs': 0,
        'space-before-function-paren': ['error', { anonymous: 'always', named: 'never', asyncArrow: 'never' }],
        'padded-blocks': ['error', 'never'],
        'no-console': 0,
        'no-debugger': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
        'prefer-promise-reject-errors': 'off',
        'comma-dangle': ['error', 'always-multiline'],
        'no-irregular-whitespace': ['off', { skipTemplates: true }],
    },
}
