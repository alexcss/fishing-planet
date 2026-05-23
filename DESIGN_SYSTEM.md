# Fishing Planet Design System

This document outlines the design system extracted from Figma and implemented in the theme.

## Colors

### Primary Colors

```css
--color-primary: #F88A1E

; /* Primary orange */
--color-primary-active: #F37900

; /* Active/pressed state */
--color-accent: #F88A1E

; /* Accent color (same as primary) */
```

### Neutral Colors

```css
--color-white: #FFFFFF

; /* White */
--color-black: #010101

; /* Black */
--color-gray: #AAACAF

; /* Light gray */
--color-dark-gray: #757C81

; /* Dark gray (disabled states) */
--color-gray-stone: #777E83

; /* Stone gray */
```

### Tailwind Usage

```twig
<div class="bg-accent text-white">Primary accent background</div>
<div class="text-gray">Gray text</div>
<button class="bg-dark-gray">Disabled state</button>
```

## Typography

### Font Families

```css
--font-heading:

'Bebas Neue'
,
sans-serif

; /* Headings and buttons */
--font-sans:

'Sora'
,
sans-serif

; /* Body text */
--font-mono:

'IBM Plex Mono'
,
monospace

; /* Monospace text */
```

### Heading Styles

All headings use Bebas Neue font, normal weight, and uppercase transformation.

**Mobile → Desktop (md: 768px)**

- **H0**: 40px → 100px, line-height: 1.2 (Hero headings)
- **H1**: 30px → 50px, line-height: 1.3
- **H2**: 26px → 38px, line-height: 1.3, letter-spacing: -0.01em
- **H3**: 22px → 32px, line-height: 1.1
- **H4**: 18px → 26px, line-height: 1.4
- **H5**: 20px → 24px, line-height: 1.3
- **H6**: 18px → 24px, line-height: 1.4

### Usage in Twig

```twig
<h1 class="h1">Heading 1</h1>
<h2 class="h2">Heading 2</h2>
<div class="h0">Hero Heading</div>
```

### Body Text Styles

```twig
<p class="fp-text-body">Standard body text (18px → 20px)</p>
<p class="fp-text-body-article">Article text with optimized line-height</p>
<p class="fp-text-body-small">Small body text (16px)</p>
<p class="fp-text-body-xs">Extra small text (14px → 16px)</p>
```

### Specialized Text Styles

```twig
{# Button text #}
<span class="fp-btn-text">Button Text</span>

{# Heading text (non-semantic) #}
<span class="fp-heading-text">Heading Style Text</span>

{# Footer title #}
<h5 class="fp-title-footer">Footer Section Title</h5>

{# Link text #}
<a class="fp-text-link">Navigation Link</a>

{# Small description with mono font #}
<span class="fp-text-small-description">Technical Label</span>

{# Gradient text effect #}
<span class="text-gradient">Gradient Text</span>
```

## Buttons

### Primary Button (Orange Gradient)

```twig
<button class="fp-btn">Play for free</button>
```

**Properties:**

- Height: 64px
- Min-width: 180px
- Padding: 20px
- Font: Bebas Neue, 24px, line-height: 0.8
- Background: Orange gradient with layered effects
- Text: White, uppercase

**States:**

- Default: Orange gradient (`--background-image-btn-default`)
- Hover: 110% brightness
- Active: 90% brightness
- Disabled: Dark gray background with 70% white text opacity

### Button with Icon

```twig
{% from "components/icon.twig" import icon %}

<button class="fp-btn">
  Play for free
  {{ icon({ name: 'fishing', w: 24, h: 24 }) }}
</button>
```

### Button Variants

**Legacy Button (Rounded)**

```twig
<button class="fp-btn-legacy">Legacy Button</button>
```

- Height: 44px (mobile) → 50px (desktop)
- Rounded full
- Font: Sora (sans-serif)

**Light Button**

```twig
<button class="fp-btn-light">Light Button</button>
```

- White background, black text
- Hover: Orange background

**Outline Buttons**

```twig
<button class="fp-btn-outline">Outline Dark</button>
<button class="fp-btn-outline-light">Outline Light</button>
```

- Transparent background with border
- Hover: Orange text color

**Circle Button**

```twig
<button class="fp-btn-circle">
  {{ icon({ name: 'arrow-right', w: 16, h: 16 }) }}
</button>
```

- Size: 32x32px
- Rounded full with border

### Button Gradients (CSS Variables)

```css
/* Default state */
--background-image-btn-default:
linear-gradient

(
180
deg,
rgba

(
0
,
0
,
0
,
0
)
87.5
%
,
rgba

(
0
,
0
,
0
,
0.12
)
100
%
)
,
linear-gradient

(
180
deg,
rgba

(
255
,
255
,
255
,
0.3
)
0
%
,
rgba

(
255
,
255
,
255
,
0
)
8.33
%
)
,
linear-gradient

(
90
deg, #F88A1E

0
%
,
#F88A1E

100
%
)
;

/* Active state */
--background-image-btn-active:
linear-gradient

(
180
deg,
rgba

(
0
,
0
,
0
,
0
)
87.5
%
,
rgba

(
0
,
0
,
0
,
0.12
)
100
%
)
,
linear-gradient

(
180
deg,
rgba

(
255
,
255
,
255
,
0.3
)
0
%
,
rgba

(
255
,
255
,
255
,
0
)
8.33
%
)
,
linear-gradient

(
90
deg, #F37900

0
%
,
#F37900

100
%
)
;
```

## Icons

### Available Icons

The SVG sprite includes the following icons:

**Fishing & Activities:**

- `icon-fishing` - Fishing icon
- `icon-anchor` - Anchor
- `icon-fish` - Fish
- `icon-magnet` - Magnet
- `icon-medal` - Medal
- `icon-gamepad` - Gamepad
- `icon-boat` - Boat
- `icon-float` - Float

**UI Elements:**

- `icon-plus` - Plus sign
- `icon-minus` - Minus sign
- `icon-arrow-left` - Left arrow
- `icon-arrow-right` - Right arrow
- `icon-search` - Search
- `icon-close` - Close/X
- `icon-menu` - Hamburger menu
- `icon-filter` - Filter

**Social Media:**

- `icon-instagram` - Instagram
- `icon-youtube` - YouTube
- `icon-linkedin` - LinkedIn
- `icon-facebook` - Facebook
- `icon-steam` - Steam

**Device Icons:**

- `icon-mobile` - Mobile device
- `icon-desktop` - Desktop
- `icon-location` - Location pin
- `icon-job` - Job/briefcase

### Using Icons in Twig

**Using the Icon macro:**

```twig
{% import 'components/Icon.twig' as icons %}

{{ icons.icon({name: 'fishing', w: 24, h: 24, class: 'text-white'}) }}
```

**Direct SVG usage:**

```twig
<svg class="w-24 h-24">
  <use href="#icon-fishing" />
</svg>
```

**With Alpine.js:**

```twig
<button @click="toggleMenu">
  <svg class="w-24 h-24">
    <use href="#icon-menu" />
  </svg>
</button>
```

## Gradients

### Button Gradients

```css
--gradient-button-default:
linear-gradient

(
180
deg,
rgba

(
0
,
0
,
0
,
0
)
87.5
%
,
rgba

(
0
,
0
,
0
,
0.12
)
100
%
)
,
linear-gradient

(
180
deg,
rgba

(
255
,
255
,
255
,
0.3
)
0
%
,
rgba

(
255
,
255
,
255
,
0
)
8.33
%
)
,
linear-gradient

(
90
deg, #F88A1E

0
%
,
#F88A1E

100
%
)
;

--gradient-button-active:
linear-gradient

(
180
deg,
rgba

(
0
,
0
,
0
,
0
)
87.5
%
,
rgba

(
0
,
0
,
0
,
0.12
)
100
%
)
,
linear-gradient

(
180
deg,
rgba

(
255
,
255
,
255
,
0.3
)
0
%
,
rgba

(
255
,
255
,
255
,
0
)
8.33
%
)
,
linear-gradient

(
90
deg, #F37900

0
%
,
#F37900

100
%
)
;
```

## Responsive Breakpoints

```css
--breakpoint-sm:

640
px

; /* Mobile landscape */
--breakpoint-md:

768
px

; /* Tablet */
--breakpoint-lg:

1024
px

; /* Desktop */
--breakpoint-xl:

1280
px

; /* Large desktop */
--breakpoint-2xl:

1680
px

; /* Extra large */
```

## Usage Examples

### Complete Button Example

```twig
<button class="btn">
  Play for free
  <svg class="w-24 h-24">
    <use href="#icon-fishing" />
  </svg>
</button>
```

### Heading with Icon

```twig
<h2 class="h2 flex items-center gap-8">
  <svg class="w-32 h-32">
    <use href="#icon-medal" />
  </svg>
  Achievements
</h2>
```

### Social Media Links

```twig
<div class="flex gap-16">
  <a href="#" class="w-50 h-50 flex items-center justify-center">
    <svg class="w-24 h-24">
      <use href="#icon-instagram" />
    </svg>
  </a>
  <a href="#" class="w-50 h-50 flex items-center justify-center">
    <svg class="w-24 h-24">
      <use href="#icon-youtube" />
    </svg>
  </a>
  <a href="#" class="w-50 h-50 flex items-center justify-center">
    <svg class="w-24 h-24">
      <use href="#icon-facebook" />
    </svg>
  </a>
</div>
```

## Notes

- Button text uses Bebas Neue with 24px size and 0.8 line-height
- The sprite SVG should be included in your base template for icon usage
- Colors follow the Figma design system from the UI kit
