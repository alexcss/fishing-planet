// eslint.config.js
import globals from 'globals'
import js from '@eslint/js'
import prettier from 'eslint-plugin-prettier'
import prettierConfig from 'eslint-config-prettier'

export default [
  js.configs.recommended,
  {
    files: ['**/*.js', '**/*.mjs', '**/*.jsx'],
    plugins: {
      prettier,
    },
    rules: {
      'prettier/prettier': 'warn',
      'no-console': 'warn',
      'no-unused-vars': 'warn',
    },
    languageOptions: {
      ecmaVersion: 2024,
      sourceType: 'module',
      globals: {
        ...globals.browser,
        console: 'readonly',
        process: 'readonly',
      },
    },
  },
  prettierConfig,
]
