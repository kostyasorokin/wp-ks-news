<?php
/**
 * News templates for classic themes.
 *
 * @package ks-news
 */

declare( strict_types=1 );

namespace KonstantinSorokin\News\Template;

use KonstantinSorokin\News\Attribute\Hook;

defined( 'ABSPATH' ) || exit;

final class TemplateLoader {

    public const STYLE = 'ks-news-front';

    private const GENERIC_ARCHIVE  = [ 'archive.php', 'index.php' ];
    private const GENERIC_SINGLE   = [ 'single.php', 'singular.php', 'index.php' ];
    private const GENERIC_TAXONOMY = [ 'taxonomy.php', 'archive.php', 'index.php' ];

    #[Hook( 'archive_template' )]
    public function archive( string $template ): string {
        return is_post_type_archive( KS_NEWS_POST_TYPE )
            ? $this->resolve( $template, self::GENERIC_ARCHIVE, 'archive-' . KS_NEWS_POST_TYPE . '.php', 'archive-news.php' )
            : $template;
    }

    #[Hook( 'single_template' )]
    public function single( string $template ): string {
        return is_singular( KS_NEWS_POST_TYPE )
            ? $this->resolve( $template, self::GENERIC_SINGLE, 'single-' . KS_NEWS_POST_TYPE . '.php', 'single-news.php' )
            : $template;
    }

    #[Hook( 'taxonomy_template' )]
    public function taxonomy( string $template ): string {
        $taxonomy = get_query_var( 'taxonomy' );

        if ( ! is_string( $taxonomy ) || ! in_array( $taxonomy, [ KS_NEWS_CATEGORY_TAXONOMY, KS_NEWS_TAG_TAXONOMY ], true ) ) {
            return $template;
        }

        $themeTemplate = locate_template(
            [
                'taxonomy-' . $taxonomy . '.php',
                'archive-' . KS_NEWS_POST_TYPE . '.php',
            ]
        );

        if ( '' !== $themeTemplate ) {
            return $themeTemplate;
        }

        return $this->resolve( $template, self::GENERIC_TAXONOMY, 'taxonomy-' . $taxonomy . '.php', 'archive-news.php' );
    }

    #[Hook( 'wp_enqueue_scripts' )]
    public function registerStyles(): void {
        $relative = 'assets/front.css';
        $path     = KS_NEWS_PATH . $relative;

        wp_register_style(
            self::STYLE,
            KS_NEWS_URL . $relative,
            [],
            file_exists( $path ) ? (string) filemtime( $path ) : KS_NEWS_VERSION
        );
    }

    private function resolve( string $template, array $generic, string $themeFile, string $fallback ): string {
        if ( wp_is_block_theme() ) {
            return $template;
        }

        if ( '' !== $template && ! in_array( basename( $template ), $generic, true ) ) {
            return $template;
        }

        $path = KS_NEWS_PATH . 'templates/' . $fallback;

        /**
         * @param string $path      Absolute plugin template path.
         * @param string $themeFile Theme override file name.
         */
        $path = (string) apply_filters( 'ksNewsFallbackTemplate', $path, $themeFile );

        if ( ! is_readable( $path ) ) {
            return $template;
        }

        if ( ! wp_style_is( self::STYLE, 'registered' ) ) {
            $this->registerStyles();
        }

        wp_enqueue_style( self::STYLE );

        return $path;
    }

    /** Render a card, allowing themes to override it at ks-news/news-card.php. */
    public static function card( int $postId ): void {
        $path = locate_template( [ 'ks-news/news-card.php' ] );

        if ( '' === $path ) {
            $path = KS_NEWS_PATH . 'templates/parts/news-card.php';
        }

        if ( is_readable( $path ) ) {
            load_template( $path, false, [ 'post_id' => $postId ] );
        }
    }
}
