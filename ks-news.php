<?php
/**
 * Plugin Name: KS News
 * Plugin URI:  https://github.com/kostyasorokin/wp-ks-news
 * Description: News as a dedicated content type with separate categories and tags.
 * Version:     1.0.0
 * Author:      Konstantin Sorokin
 * Author URI:  https://konstantinsorokin.com
 * License:     GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: ks-news
 * Domain Path: /languages/
 * Requires at least: 6.7
 * Requires PHP: 8.5
 *
 * @author  Konstantin Sorokin
 * @link    https://konstantinsorokin.com
 * @package ks-news
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

const KS_NEWS_VERSION = '1.0.0';

define( 'KS_NEWS_FILE', __FILE__ );
define( 'KS_NEWS_PATH', plugin_dir_path( __FILE__ ) );
define( 'KS_NEWS_URL', plugin_dir_url( __FILE__ ) );

/** The value stored in wp_posts.post_type. Override before the first news item exists, never after. */
defined( 'KS_NEWS_POST_TYPE' ) || define( 'KS_NEWS_POST_TYPE', 'news' );

/** Separate vocabularies: news terms must never share the blog taxonomies. */
defined( 'KS_NEWS_CATEGORY_TAXONOMY' ) || define( 'KS_NEWS_CATEGORY_TAXONOMY', 'news_category' );
defined( 'KS_NEWS_TAG_TAXONOMY' ) || define( 'KS_NEWS_TAG_TAXONOMY', 'news_tag' );

/**
 * Show an admin notice instead of loading.
 *
 * @param callable(): string $problem Returns the sentence completing "KS News …".
 */
function ksNewsHalt( callable $problem ): void {
    add_action(
        'admin_notices',
        static function () use ( $problem ) {
            if ( ! current_user_can( 'activate_plugins' ) ) {
                return;
            }

            printf(
                '<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
                esc_html__( 'KS News', 'ks-news' ),
                esc_html( call_user_func( $problem ) )
            );
        }
    );
}

$ksNewsRequiredPhp = get_file_data( __FILE__, [ 'php' => 'Requires PHP' ] )['php'];

if ( '' !== $ksNewsRequiredPhp && version_compare( phpversion(), $ksNewsRequiredPhp, '<' ) ) {
    ksNewsHalt(
        static function () use ( $ksNewsRequiredPhp ) {
            return sprintf(
                /* translators: 1: required PHP version, 2: current PHP version. */
                __( 'needs PHP %1$s or newer and has not loaded. This server runs PHP %2$s.', 'ks-news' ),
                $ksNewsRequiredPhp,
                phpversion()
            );
        }
    );

    return;
}

$ksNewsAutoload = __DIR__ . '/vendor/autoload.php';

if ( is_readable( $ksNewsAutoload ) ) {
    try {
        require_once $ksNewsAutoload;
    } catch ( \Throwable $e ) {
        ksNewsHalt( static fn(): string => $e->getMessage() );

        return;
    }
}

if ( ! class_exists( 'KonstantinSorokin\News\Plugin' ) ) {
    ksNewsHalt(
        static function () {
            return __( 'could not load its classes. Run "composer install" in the plugin directory, or reinstall the plugin.', 'ks-news' );
        }
    );

    return;
}

KonstantinSorokin\News\Plugin::boot();

register_activation_hook( __FILE__, array( 'KonstantinSorokin\News\Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'KonstantinSorokin\News\Plugin', 'deactivate' ) );
