# KS News

KS News adds a dedicated `news` post type to WordPress. News content has its own hierarchical categories (`news_category`) and non-hierarchical tags (`news_tag`), so its terms never mix with regular post categories or tags.

## Features

- Public news archive at `/news/`
- Single news URLs at `/news/<name>/`
- Category archives at `/news/category/<term>/`
- Tag archives at `/news/tag/<term>/`
- Block editor and REST API support
- Title, editor, author, excerpt, featured image, comments, and revisions
- Russian and Ukrainian translations; all source strings remain English
- Multisite-safe activation and rewrite setup

## Installation

1. Copy `ks-news` into `wp-content/plugins`.
2. Run `composer install --no-dev --optimize-autoloader` in the plugin directory
   if `vendor/` is not included in the package.
3. Activate **KS News** in WordPress.
4. Add content under **News** in the admin menu.

Activation registers the routes and flushes rewrite rules. Deactivation removes
only the runtime registrations; it never deletes news, categories, or tags.

## Extension points

- `ksNewsPostTypeArgs`
- `ksNewsCategoryTaxonomyArgs`
- `ksNewsTagTaxonomyArgs`

Each filter receives the complete registration arguments immediately before the
corresponding WordPress registration function is called.
