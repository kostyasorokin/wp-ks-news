<?php
/**
 * Formatting for news listings.
 *
 * @package ks-news
 */

declare( strict_types=1 );

namespace KonstantinSorokin\News\Helpers;

defined( 'ABSPATH' ) || exit;

final class News {

    /** Return plain text of no more than 160 Unicode characters. */
    public static function summary( string $source ): string {
        $source = (string) preg_replace( '/<\/?(?:p|div|h[1-6]|li|blockquote|br)\b[^>]*>/i', ' ', $source );
        $plain  = wp_strip_all_tags( strip_shortcodes( $source ), true );
        $plain  = html_entity_decode( $plain, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
        $plain  = trim( (string) preg_replace( '/\s+/u', ' ', $plain ) );

        if ( mb_strlen( $plain ) <= 160 ) {
            return $plain;
        }

        return rtrim( mb_substr( $plain, 0, 159 ) ) . '…';
    }
}
