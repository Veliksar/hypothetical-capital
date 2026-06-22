# Hypothetical Capital

WordPress theme for the Hypothetical Capital corporate website.

Classic PHP templates, ACF Pro Flexible Content, modular SCSS, and GSAP-driven front-end animations. Based on [Underscores](https://underscores.me/).

---

## Stack overview

### WordPress & PHP

| Item | Details |
|------|---------|
| Theme type | Classic theme (no block theme / FSE) |
| PHP | 7.4+ |
| Templates | `header.php`, `footer.php`, `page.php`, `single.php`, `archive.php`, `index.php`, `search.php`, `404.php` |
| Blog | Standard post templates in `template-parts/` |
| Modules | `inc/` - ACF setup, Flexible Content renderer, SEO meta, customizer helpers |
| Text domain | `hypothetical-capital` |
| Function prefix | `hypothetical_capital_` |
| Version constant | `HC_THEME_VERSION` |

**Not included:** sidebars, widget areas, WooCommerce, Jetpack.

### ACF Pro

| Item | Details |
|------|---------|
| Plugin | [ACF Pro](https://www.advancedcustomfields.com/pro/) (required) |
| Field sync | Local JSON in `acf-json/` - version-controlled, synced via WP Admin |
| Config | Save/load paths in `inc/acf.php` |
| Page builder | Flexible Content field `flexible_content` on template `page-templates/flexible-content.php` |
| Global settings | Option page (`option`) - header/footer branding, legal links, social links |

**Flexible Content workflow:**

1. Layout defined in `acf-json/group_hc_flexible_content.json`
2. Renderer `hypothetical_capital_render_flexible_content()` in `inc/flexible-content.php` loads `template-parts/flexible-content/{layout}.php`
3. Each layout uses root class `fc-block fc-block--{layout}`

After JSON changes: **Custom Fields -> Field Groups -> Sync available**.

### Styles

| Item | Details |
|------|---------|
| Base CSS | `style.css` - Underscores normalize and legacy base (do not add component styles here) |
| Components | `assets/scss/` - modular SCSS with `@use` |
| Entry point | `assets/scss/custom.scss` |
| Output | `assets/css/custom.min.css` + source map |
| Compiler | Dart Sass via npm |
| Design tokens | `_variables.scss` - Figma palette, fluid spacing, breakpoints |
| Typography | `_typography.scss` - type scale mixins (`type-h1`, `type-body-m`, `type-button`, etc.) |
| Layout mixins | `site-section`, `site-container`, `grid-lines`, `corner-bracket-button` |
| Fonts | Google Fonts handle `hypothetical-capital-fonts` - Playfair Display (headings), Albert Sans (body) |

**SCSS structure:**

```
assets/scss/
├── custom.scss              # bundles all partials
├── _variables.scss
├── _typography.scss
├── _lenis.scss
├── header.scss
├── header-nav.scss
├── footer.scss
└── flexible-content/
    ├── base.scss            # shared .fc-block styles
    ├── hero.scss
    ├── stats.scss
    ├── highlights.scss
    ├── content.scss
    └── cta.scss
```

Edit SCSS only. Run `npm run build` before deploy or commit.

### JavaScript

Third-party libraries are vendored to `assets/js/lib/` and committed to the repo - production server does not need Node.js.

| Script | Role |
|--------|------|
| `lib/gsap.min.js` | GSAP 3 core |
| `lib/ScrollTrigger.min.js` | Scroll-linked animations |
| `lib/lenis.min.js` | Smooth scroll engine |
| `animations.js` | Shared GSAP presets (`window.HCAnimations`) |
| `hero-intro.js` | Hero load timeline (media, header, grid lines, content) |
| `stats-counter.js` | Stats section counters and grid line draw (ScrollTrigger) |
| `highlights-slider.js` | Highlights carousel |
| `smooth-scroll.js` | Lenis init and GSAP ticker sync |
| `scroll.js` | Anchor links and back-to-top |
| `navigation.js` | Mobile menu toggle |

Vendor update (after `npm update`):

```powershell
npm run vendor:gsap
npm run vendor:lenis
```

---

## Requirements

- WordPress (latest stable recommended)
- [ACF Pro](https://www.advancedcustomfields.com/pro/)
- [Node.js](https://nodejs.org/) 18+ (local development only)
- [Composer](https://getcomposer.org/) (optional, PHP tooling)

---

## Quick start

```powershell
cd public/wp-content/themes/hypothetical-capital
npm install
npm run watch    # dev: expanded CSS + source map, auto-rebuild on save
npm run build    # prod: minified CSS + source map
```

Optional PHP tooling:

```powershell
composer install
composer lint:wpcs    # WordPress Coding Standards
composer lint:php     # PHP syntax check
composer make-pot     # regenerate languages/hypothetical-capital.pot
```

---

## Project layout

```
hypothetical-capital/
├── acf-json/                    # ACF field groups (version-controlled)
├── assets/
│   ├── scss/                    # SCSS source - edit here
│   ├── css/                     # compiled bundle - do not edit by hand
│   ├── js/                      # theme scripts + lib/
│   └── img/                     # SVG icons
├── inc/
│   ├── acf.php
│   ├── flexible-content.php
│   ├── seo-meta.php
│   └── ...
├── page-templates/
│   └── flexible-content.php
├── template-parts/
│   ├── flexible-content/        # one PHP file per FC layout
│   └── partials/
├── functions.php
├── header.php
├── footer.php
└── style.css
```

---

## Flexible Content layouts

| Layout | Template | SCSS | Description |
|--------|----------|------|-------------|
| `hero` | `hero.php` | `hero.scss` | Full-width hero with image or video background, intro animation |
| `stats` | `stats.php` | `stats.scss` | 50/50 grid: heading + image, animated stat counters |
| `highlights` | `highlights.php` | `highlights.scss` | Two columns: intro text + highlight items carousel |
| `content_block` | `content_block.php` | `content.scss` | Single WYSIWYG content block |
| `cta` | `cta.php` | `cta.scss` | Centered call-to-action |

Shared styles: `assets/scss/flexible-content/base.scss` - common wrappers, columns, buttons, images.

**Adding a new layout:**

1. Add layout to `acf-json/group_hc_flexible_content.json`
2. Create `template-parts/flexible-content/{name}.php`
3. Create `assets/scss/flexible-content/{name}.scss` and `@use` it in `custom.scss`
4. Sync field group in WP Admin
5. Run `npm run build`

---

## Navigation & global content

| Menu location | Slug | Used in |
|---------------|------|---------|
| Primary | `menu-1` | Header |
| Footer | `footer` | Footer |

Global site content (footer colors, legal links, social, copyright) is managed via the ACF Option page and read with:

```php
get_field( 'field_name', 'option' );
```

---

## Development notes

- Keep `npm run watch` running during local SCSS work
- Use typography and layout mixins from `_typography.scss` / `_variables.scss` instead of hardcoded sizes
- Escape all template output: `esc_html()`, `esc_url()`, `esc_attr()`, `wp_kses_post()`
- Animations respect `prefers-reduced-motion` where implemented
- `style.css` is not compiled from Sass - component styles live in `assets/scss/` only

---

## License

GPL-2.0-or-later. Based on [Underscores](https://underscores.me/).
