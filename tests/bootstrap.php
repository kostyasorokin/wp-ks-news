<?php
/**
 * Unit-test bootstrap: no WordPress and no database.
 *
 * @package ks-news
 */

declare( strict_types=1 );

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

defined( 'ABSPATH' ) || define( 'ABSPATH', dirname( __DIR__ ) . '/' );
defined( 'KS_NEWS_POST_TYPE' ) || define( 'KS_NEWS_POST_TYPE', 'news' );
defined( 'KS_NEWS_CATEGORY_TAXONOMY' ) || define( 'KS_NEWS_CATEGORY_TAXONOMY', 'news_category' );
defined( 'KS_NEWS_TAG_TAXONOMY' ) || define( 'KS_NEWS_TAG_TAXONOMY', 'news_tag' );

$GLOBALS['ksNewsTestFilters']       = [];
$GLOBALS['ksNewsTestPostTypes']     = [];
$GLOBALS['ksNewsTestTaxonomies']    = [];
$GLOBALS['ksNewsTestRelationships'] = [];

if ( ! function_exists( '__' ) ) {
    function __( string $text, string $domain = 'default' ): string { // phpcs:ignore
        return $text;
    }
}

if ( ! function_exists( 'apply_filters' ) ) {
    function apply_filters( string $hook, mixed $value, mixed ...$args ): mixed { // phpcs:ignore
        $listener = $GLOBALS['ksNewsTestFilters'][ $hook ] ?? null;

        return is_callable( $listener ) ? $listener( $value, ...$args ) : $value;
    }
}

if ( ! function_exists( 'register_post_type' ) ) {
    function register_post_type( string $postType, array $args = [] ): object { // phpcs:ignore
        $GLOBALS['ksNewsTestPostTypes'][ $postType ] = $args;

        return (object) $args;
    }
}

if ( ! function_exists( 'register_taxonomy' ) ) {
    function register_taxonomy( string $taxonomy, array|string $objectType, array $args = [] ): object { // phpcs:ignore
        $GLOBALS['ksNewsTestTaxonomies'][ $taxonomy ] = [
            'object_type' => (array) $objectType,
            'args'        => $args,
        ];

        return (object) $args;
    }
}

if ( ! function_exists( 'register_taxonomy_for_object_type' ) ) {
    function register_taxonomy_for_object_type( string $taxonomy, string $objectType ): bool { // phpcs:ignore
        $GLOBALS['ksNewsTestRelationships'][] = [ $taxonomy, $objectType ];

        return true;
    }
}
