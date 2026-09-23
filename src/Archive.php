<?php
/**
 * News archive query settings.
 *
 * @package ks-news
 */

declare( strict_types=1 );

namespace KonstantinSorokin\News;

use KonstantinSorokin\News\Attribute\Hook;
use WP_Query;

defined( 'ABSPATH' ) || exit;

final class Archive {

    public const OPTION_NAME       = 'ks_news_posts_per_page';
    public const DEFAULT_PER_PAGE  = 32;
    public const MIN_PER_PAGE      = 1;
    public const MAX_PER_PAGE      = 100;

    /** Get the administrator-configured archive size. */
    public static function perPage(): int {
        return self::validatedPerPage( get_option( self::OPTION_NAME, self::DEFAULT_PER_PAGE ) )
            ?? self::DEFAULT_PER_PAGE;
    }

    /** Accept only whole numbers within the supported range. */
    public static function validatedPerPage( mixed $value ): ?int {
        if ( ! is_string( $value ) && ! is_int( $value ) ) {
            return null;
        }

        $value = (string) $value;

        if ( ! ctype_digit( $value ) ) {
            return null;
        }

        $perPage = (int) $value;

        return $perPage >= self::MIN_PER_PAGE && $perPage <= self::MAX_PER_PAGE
            ? $perPage
            : null;
    }

    /** Apply the configured limit only to public News main queries. */
    #[Hook( 'pre_get_posts' )]
    public function setPostsPerPage( WP_Query $query ): void {
        if ( is_admin() || ! $query->is_main_query() ) {
            return;
        }

        if ( ! $query->is_post_type_archive( KS_NEWS_POST_TYPE )
            && ! $query->is_tax( [ KS_NEWS_CATEGORY_TAXONOMY, KS_NEWS_TAG_TAXONOMY ] ) ) {
            return;
        }

        $query->set( 'posts_per_page', self::perPage() );
    }
}
