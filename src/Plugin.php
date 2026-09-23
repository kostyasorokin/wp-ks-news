<?php
/**
 * Plugin service list.
 *
 * @package ks-news
 */

declare( strict_types=1 );

namespace KonstantinSorokin\News;

use KonstantinSorokin\News\Attribute\Hook;
use KonstantinSorokin\News\Support\HookBinder;
use KonstantinSorokin\News\Template\TemplateLoader;
use KonstantinSorokin\News\Taxonomy\LegacyTermRedirect;
use KonstantinSorokin\News\Taxonomy\Taxonomies;
use WP_Site;
use WP_Textdomain_Registry;

defined( 'ABSPATH' ) || exit;

final class Plugin {

    public static function boot(): void {
        self::registerTranslations();

        array_map(
            HookBinder::bind( ... ),
            [
                new self(),
                new Taxonomies(),
                new PostType(),
                new TemplateLoader(),
                new LegacyTermRedirect(),
            ]
        );
    }

    private static function registerTranslations(): void {
        $registry = $GLOBALS['wp_textdomain_registry'] ?? null;

        if ( $registry instanceof WP_Textdomain_Registry ) {
            $registry->set_custom_path( 'ks-news', KS_NEWS_PATH . 'languages' );
        }
    }

    public static function activate( bool $networkWide = false ): void {
        if ( ! $networkWide || ! is_multisite() ) {
            self::prepareSite();
            flush_rewrite_rules();

            return;
        }

        foreach ( get_sites(
            [
                'fields' => 'ids',
                'number' => 0,
            ]
        ) as $siteId ) {
            switch_to_blog( (int) $siteId );
            self::prepareSite();
            delete_option( 'rewrite_rules' );
            restore_current_blog();
        }
    }

    #[Hook( 'wp_initialize_site', 20 )]
    public function prepareCreatedSite( WP_Site $site ): void {
        $networkPlugins = (array) get_site_option( 'active_sitewide_plugins', [] );

        if ( ! isset( $networkPlugins[ plugin_basename( KS_NEWS_FILE ) ] ) ) {
            return;
        }

        switch_to_blog( (int) $site->blog_id );
        self::prepareSite();
        restore_current_blog();
    }

    private static function prepareSite(): void {
        $taxonomies = new Taxonomies();
        $postType   = new PostType();

        $taxonomies->register();
        $postType->register();
        $postType->attachTaxonomies();
    }

    public static function deactivate(): void {
        unregister_post_type( KS_NEWS_POST_TYPE );
        unregister_taxonomy( KS_NEWS_CATEGORY_TAXONOMY );
        unregister_taxonomy( KS_NEWS_TAG_TAXONOMY );
        flush_rewrite_rules();
    }
}
