# Flex Press - Modern WordPress Theme

A modern, high-performance WordPress theme built with Tailwind CSS, Vite, Twig templating engine, Timber, and Alpine.js. Flex Press combines the power of modern frontend tooling with WordPress development best practices, leveraging Timber for cleaner theme development and Alpine.js for lightweight
reactive interactions.

## ✨ Features

- 🚀 Built with Vite for lightning-fast development and production builds
- 🎨 Powered by Tailwind CSS for rapid, utility-first styling
- 🏗 Twig templating for clean, maintainable templates
- ⚡ Alpine.js for lightweight reactive JavaScript interactions
- 🔥 Modern JavaScript with ES6+ support
- 🛠 Development tools included (ESLint, Stylelint, PHP_CodeSniffer)
- 📦 Composer for PHP dependency management
- 🔄 Live reloading for development
- 🏗 Component-based architecture
- 📱 Fully responsive design
- 🚀 Optimized for performance

## 🚀 Getting Started

### Prerequisites

- Node.js 20+
- PHP 8.1+
- Composer
- WordPress 6.2+

### Installation

1. Clone the repository into your WordPress themes directory:
   ```bash
   git clone [your-repository-url] wp-content/themes/flex-press
   ```

2. Install Node.js dependencies:
   ```bash
   cd wp-content/themes/flex-press
   yarn install
   ```

3. Install PHP dependencies:
   ```bash
   composer install
   ```

## 🛠 Development

### Available Scripts

- `yarn dev` - Start development server with hot reload
- `yarn build` - Build assets for production
- `yarn lint` - Run all linters
- `yarn lintFix` - Fix linting issues automatically

## 🏗 Project Structure

```
flex-press/
├── dist/                   # Compiled assets (auto-generated)
├── inc/                    # PHP includes and theme functionality
│   ├── Gutenberg/          # Gutenberg block configurations
│   ├── Plugins/            # Plugin integrations and customizations
│   ├── Post_Type/          # Custom post type definitions
│   ├── Theme/              # Core theme functionality
│   └── Utils/              # Utility functions and helpers
├── page-templates/         # Custom page templates
├── src/                    # Frontend source files (JS/SCSS)
├── twigs/                  # Twig templates (Timber)
│   ├── components/         # Reusable UI components
│   ├── global/             # Global template parts (header, footer, etc.)
│   ├── partial/            # Template partials and snippets
│   ├── templates/          # Custom template overrides
│   ├── 404.twig           # 404 error page template
│   ├── base.twig          # Base template that others extend
│   └── index.twig         # Default template
├── .env.example           # Example environment configuration
├── .eslintrc.js           # ESLint configuration
├── composer.json          # PHP dependencies
├── functions.php          # Theme functions and setup
├── package.json           # Node.js dependencies and scripts
├── style.css              # Theme metadata
├── theme.json             # WordPress theme settings
└── vite.config.js         # Vite build configuration
```

## 📝 License

This theme is open-source software licensed under the MIT License. See the [LICENSE](LICENSE) file for more information.

## 👥 Credits

- **Developed by:** Oleksii Kaliuzhnyi & Andrii Khan
- **Author URI:** [https://alexcss.com/](https://alexcss.com/)
- **Version:** 1.0.0

## 🔗 Resources & Documentation

### Core Technologies

- [WordPress](https://wordpress.org/) - Content management system
- [Timber](https://timber.github.io/docs/) - WordPress plugin for cleaner theme development
- [Twig](https://twig.symfony.com/doc/) - Modern template engine for PHP
- [Tailwind CSS](https://tailwindcss.com/docs) - Utility-first CSS framework
- [Alpine.js](https://alpinejs.dev/) - Lightweight JavaScript framework for reactive interactions
- [Vite](https://vitejs.dev/guide/) - Next generation frontend build tool

### Development Tools

- [Composer](https://getcomposer.org/doc/) - PHP dependency manager
- [ESLint](https://eslint.org/docs/) - JavaScript linting utility
- [Stylelint](https://stylelint.io/) - CSS linting tool
- [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) - PHP coding standards

### WordPress Development

- [WordPress Developer Resources](https://developer.wordpress.org/) - Official WordPress development documentation
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/) - WordPress coding guidelines
- [Advanced Custom Fields](https://www.advancedcustomfields.com/resources/) - Custom field management plugin

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

---

🎉 Thank you for using Flex Press! If you have any questions or need assistance, please open an issue.
