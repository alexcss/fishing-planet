// import { createRequire } from 'module'
//
// const require = createRequire(import.meta.url)
// const wpConfig = require('@wordpress/prettier-config')

const config = {
  plugins: ['@zackad/prettier-plugin-twig', 'prettier-plugin-tailwindcss', '@yikes2000/prettier-plugin-merge-extras'],
  overrides: [
    {
      files: '*.twig',
      options: {
        printWidth: 150,
        tabWidth: 2,
        useTabs: true,
        singleQuote: true,
        twigSingleQuote: false, // Twig-specific option
        twigAlwaysBreakObjects: false,
        twigFollowOfficialCodingStandards: true,
      },
    },
  ],
  // WordPress uses tabs, but you can customize
  printWidth: 150,
  jsxSingleQuote: false,
  singleQuote: true,
  semi: false,
  tabWidth: 2,
  useTabs: false,
  bracketSpacing: true,
  arrowParens: 'always',
  endOfLine: 'lf',
  quoteProps: 'as-needed',
  htmlWhitespaceSensitivity: 'css',
  proseWrap: 'preserve',
  alignObjectProperties: 'none',
  alignSingleProperty: false,
  mergeSimpleImports: false,
  preserveEolMarker: false,
  preserveFirstBlankLine: true,
  preserveLastBlankLine: true,
  returnParentheses: true,
  trailingComma: 'es5',
}

export default config
