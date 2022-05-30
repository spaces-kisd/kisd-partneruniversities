module.exports = {
    env: {
        browser: true,
    },
    extends: [
        'plugin:vue/essential',
        'plugin:vue/strongly-recommended'
    ],
    parserOptions: {
        // parser: '@babel/eslint-parser',
        ecmaVersion: 12,
        sourceType: 'module'
    },
    plugins: [
        'vue',
    ],
    rules: {
    }
}
