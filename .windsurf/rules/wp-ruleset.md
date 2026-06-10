---
trigger: always_on
description: 
globs: 
---

# Windsurf AI Rules - WordPress Theme Development

## Project Overview
This is a modern WordPress theme development project using:
- **Timber** for WordPress and Twig integration
- **ACF (Advanced Custom Fields)** for custom field management
- **Tailwind CSS 4** for utility-first styling
- **Twig** templating engine (via Timber)
- **Alpine.js** for lightweight reactive JavaScript functionality
- **Vite** for modern build tooling and asset bundling

## Directory Structure Rules
```
/pingle/
├── src/
│   ├── css/
│   │   ├── app.css (Tailwind entry point)
│   │   └── theme.css (Tailwind global styles)
│   ├── js/
│   │   ├── app.js
│   │   └── components/
│   └── images/
├── dist/ (Vite build output)
├── twigs/
│   ├── base.twig
│   ├── index.twig
│   ├── single.twig
│   ├── page.twig
│   ├── archive.twig
│   ├── components/
│   ├── partials/
│   └── global/
│       ├── header.twig
│       ├── footer.twig
│       └── offcanvas.twig
├── inc/ (PHP theme includes)
├── functions.php
├── style.css (WordPress theme header)
├── vite.config.js
├── package.json
└── composer.json
```

## WordPress Theme Development Rules

### 1. Timber Integration Rules
- Extend `Timber\Site` class for global site configuration
- Use `Timber::render()` or `Timber::compile()` in PHP template files
- Set up Timber context with global data (menus, options, etc.)
- Configure Timber locations for template files
- Use Timber's built-in functions for WordPress data (posts, users, terms)
- Implement custom Timber classes for complex data structures
- Use Timber's caching mechanisms for performance optimization

### 2. Functions.php Structure
- Enqueue Vite-built assets properly
- Register ACF field groups only programmatically
- Use proper WordPress hooks and filters
- Follow WordPress coding standards (WPCS)

### 3. ACF Integration Rules
- Store field groups in `inc/Plugins/Acf` for version control
- Use semantic field names with prefixes (e.g., `hero_title`, `section_content`)
- Create reusable field groups for common components
- Use ACF Blocks for Gutenberg integration when appropriate
- Validate and sanitize ACF data in templates

### 4. Timber + Twig Template Rules
- Use `.twig` extension for all template files
- Follow Twig naming conventions (snake_case for variables)
- Create reusable components in `/twigs/components/`
- Implement proper context passing from PHP to Twig
- Use Twig filters for data formatting and escaping

### 5. Timber + Twig Template Rules
- Use `.twig` extension for all Timber template files
- Follow WordPress template hierarchy with Timber conventions
- Use Timber context variables (`site`, `posts`, `post`, etc.)
- Follow Twig naming conventions (snake_case for variables)
- Create reusable components in `/twigs/components/`
- Use Twig macros for repetitive HTML structures
- Implement proper context passing from PHP to Twig via Timber
- Use Timber's built-in filters and functions (`wpautop`, `excerpt`, etc.)
- Access WordPress functions via `fn()` in Twig templates
- Use `twigs/components/Image.twig` for responsive image handling

### 6. Tailwind CSS Rules
- Use Tailwind 4 utility classes primarily
- Use `@apply` directive for component classes
- Use responsive prefixes consistently (mobile-first approach)
- Never use custom css classes in css, only Tailwind 4 in html layout

### 7. Alpine.js Integration Rules
- Use Alpine.js for interactive components (modals, dropdowns, tabs, etc.)
- Keep Alpine directives in Twig templates, not separate JS files
- Use `x-data` for component state management
- Use `x-show`, `x-if` for conditional rendering
- Implement `x-transition` for smooth animations
- Use `x-on` for event handling (click, scroll, resize, etc.)
- Leverage `x-bind` for dynamic attributes and classes
- Use `x-model` for two-way data binding with forms
- Create reusable Alpine components with `Alpine.data()`
- Use `x-init` for component initialization logic
- Implement `x-ref` for direct DOM element access when needed

## Code Style and Standards

### PHP + Timber Rules
- Follow WordPress Coding Standards
- Use Timber's escaping functions and built-in security features
- Implement proper nonce verification for forms
- Use Timber context instead of global WordPress variables when possible
- Create custom Timber classes for complex data structures
- Use Timber's query methods instead of direct WordPress queries
- Implement proper error handling with Timber's fallback systems
- Comment complex logic and document custom Timber classes

### JavaScript Rules (Vanilla JS + Alpine.js)
- Use Alpine.js for reactive DOM interactions
- Keep vanilla JavaScript for WordPress API calls and complex logic
- Use ES6+ syntax for vanilla JavaScript modules
- Implement proper event listeners cleanup for vanilla JS
- Use Alpine's `$dispatch` and `$listen` for component communication
- Handle async operations with promises/async-await in vanilla JS
- Follow WordPress JavaScript coding standards when interacting with WP APIs
- Follow mobile-first responsive design
- Use semantic class names for custom components
- Group related utilities logically
- Use Tailwind's spacing scale consistently
- Implement accessibility-friendly color contrasts

## File Naming Conventions
- PHP files: `kebab-case.php`
- Twig templates: `kebab-case.twig` (following Timber conventions)
- Timber classes: `PascalCase.php` (e.g., `CustomPost.php`, `SiteSetup.php`)
- CSS files: `kebab-case.css`
- JavaScript files: `kebab-case.js`
- ACF field groups: descriptive names matching their purpose

## Performance and Security Rules
- Implement lazy loading for images
- Use Vite's code splitting for JavaScript
- Sanitize all user inputs
- Use WordPress nonces for form submissions
- Implement proper caching headers

## Development Workflow Rules
- Use version control (Git) with meaningful commit messages
- Set up different environments (development, staging, production)
- Use Vite's development server for hot reloading
- Test across different browsers and devices
- Validate HTML, CSS, and JavaScript
- Test with real content, not just placeholder text

## WordPress + Timber Integration Rules
- Use Timber's template hierarchy system instead of traditional PHP templates
- Support WordPress features through Timber context (menus, widgets, customizer)
- Implement proper post thumbnail support using `TimberImage`
- Use Timber's query methods instead of direct database queries
- Support WordPress multisite with Timber's site switching
- Implement proper internationalization (i18n) in Twig templates
- Follow WordPress accessibility guidelines with Timber's built-in features
- Use Timber's built-in WordPress functions and filters

## Timber-Specific Best Practices

### Timber Context Management
- Set up global context in a centralized location (e.g., `inc/timber-setup.php`)
- Add site-wide data to context (menus, options, custom fields)
- Use context filters to modify data for specific templates
- Cache expensive context operations using Timber's caching system
- Keep context lean - only include data that templates actually need


### Error Handling with Timber
- Implement fallback templates for missing Timber templates
- Use `Timber::render()` with fallback template arrays
- Handle missing context data gracefully in Twig templates
- Provide meaningful error messages in development mode
- Log Timber-specific errors appropriately

### Timber Template Organization
- Follow WordPress template hierarchy naming conventions
- Use template inheritance effectively with `extends` and `block`
- Create reusable template partials in organized subdirectories
- Use Twig macros for repetitive template patterns
- Implement consistent template structure across the theme

## Alpine.js Best Practices

### Component Structure
- Create reusable Alpine components using `Alpine.data()`
- Keep component logic simple and focused on single responsibility
- Use descriptive names for Alpine data properties
- Implement proper initial state in `x-data`
- Use computed properties (getters) for derived state

### State Management
- Use local component state (`x-data`) for component-specific data
- Use Alpine's `$store` for global state shared across components
- Pass Timber context data to Alpine using Twig's `to_json` filter
- Avoid deep nesting in Alpine data objects
- Use Alpine's `$watch` for reactive side effects

### Event Handling
- Use `x-on` shorthand (`@click`, `@submit`, etc.) for event listeners
- Implement proper event delegation for dynamic content
- Use `$event` for accessing native event objects
- Use `$dispatch` for custom events between components
- Prevent default behavior explicitly when needed (`@click.prevent`)

### Performance Considerations
- Use `x-show` instead of `x-if` for frequently toggled elements
- Implement `x-cloak` to prevent layout shifts during Alpine initialization
- Use `x-ignore` for elements that shouldn't be processed by Alpine
- Lazy load Alpine components when possible
- Minimize watchers and computed properties

### Accessibility with Alpine.js
- Use `x-bind:aria-*` for dynamic ARIA attributes
- Implement proper focus management with `$refs` and `$nextTick`
- Use `x-trap` plugin for modal focus trapping
- Ensure keyboard navigation works with Alpine interactions
- Provide screen reader feedback for dynamic content changes
- Implement graceful fallbacks for missing ACF data
- Handle Vite asset loading failures
- Provide meaningful error messages in development
- Log errors appropriately without exposing sensitive information
- Test error scenarios and edge cases

## Documentation Rules
- Document custom ACF field groups and their usage
- Provide setup instructions for the development environment
- Document any custom Twig functions or filters
- Include code comments for complex implementations
- Maintain a changelog for theme updates