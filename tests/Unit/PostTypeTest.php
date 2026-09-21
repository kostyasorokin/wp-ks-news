<?php
/**
 * News post type registration.
 *
 * @package ks-news
 */

declare( strict_types=1 );

namespace KonstantinSorokin\News\Tests\Unit;

use KonstantinSorokin\News\PostType;
use PHPUnit\Framework\TestCase;

final class PostTypeTest extends TestCase {

    protected function setUp(): void {
        $GLOBALS['ksNewsTestFilters']       = [];
        $GLOBALS['ksNewsTestPostTypes']     = [];
        $GLOBALS['ksNewsTestRelationships'] = [];
    }

    public function testRegistersPublicNewsWithDedicatedTaxonomies(): void {
        ( new PostType() )->register();

        $args = $GLOBALS['ksNewsTestPostTypes']['news'];

        self::assertTrue( $args['public'] );
        self::assertSame( 'news', $args['has_archive'] );
        self::assertSame( 'news', $args['rewrite']['slug'] );
        self::assertSame( [ 'news_category', 'news_tag' ], $args['taxonomies'] );
        self::assertTrue( $args['show_in_rest'] );
        self::assertSame( 'News item', $args['labels']['singular_name'] );
    }

    public function testPostTypeArgumentsCanBeFiltered(): void {
        $GLOBALS['ksNewsTestFilters']['ksNewsPostTypeArgs'] = static function ( array $args ): array {
            $args['menu_position'] = 30;

            return $args;
        };

        ( new PostType() )->register();

        self::assertSame( 30, $GLOBALS['ksNewsTestPostTypes']['news']['menu_position'] );
    }

    public function testExplicitlyAttachesBothTaxonomies(): void {
        ( new PostType() )->attachTaxonomies();

        self::assertSame(
            [
                [ 'news_category', 'news' ],
                [ 'news_tag', 'news' ],
            ],
            $GLOBALS['ksNewsTestRelationships']
        );
    }
}
