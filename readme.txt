=== KS News ===
Contributors: konstantinsorokin
Tags: news, custom post type, categories, tags
Requires at least: 6.7
Tested up to: 7.1
Requires PHP: 8.5
Stable tag: 1.0.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

News as a dedicated WordPress content type with separate categories and tags.

== Description ==

Registers the public `news` post type with dedicated `news_category` and
`news_tag` taxonomies. News categories and tags are completely separate from
the taxonomies used by regular blog posts.

The archive is available at `/news/`; individual news items use
`/news/<name>/`. Category archives use `/news/category/<term>/` and tag archives
use `/news/tag/<term>/`.

The post type supports the block editor, REST API, titles, body content, authors,
excerpts, featured images, comments, and revisions. The administration interface
includes bundled Russian and Ukrainian translations; English is the source
language used in code.

== Installation ==

1. Copy the `ks-news` directory into `wp-content/plugins`.
2. Activate **KS News**.
3. Add and classify content under **News** in the admin menu.

== Frequently Asked Questions ==

= Are news categories shared with blog post categories? =

No. News categories and news tags are separate taxonomies. Matching names or
slugs still produce independent WordPress terms.

= Does deactivation delete news content? =

No. News items and terms remain in the database and become available again when
the plugin is reactivated.

== Changelog ==

= 1.0.0 =
* Register the News post type with dedicated categories and tags.
* Add Russian and Ukrainian translations.
