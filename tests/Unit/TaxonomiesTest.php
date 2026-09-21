<?php
/**
 * News taxonomy registration.
 *
 * @package ks-news
 */

declare( strict_types=1 );

namespace KonstantinSorokin\News\Tests\Unit;

use KonstantinSorokin\News\Taxonomy\Taxonomies;
use PHPUnit\Framework\TestCase;

final class TaxonomiesTest extends TestCase {

    protected function setUp(): void {
        $GLOBALS['ksNewsTestFilters']    = [];
        $GLOBALS['ksNewsTestTaxonomies'] = [];
    }

    public function testRegistersSeparateCategoryAndTagTaxonomies(): void {
        ( new Taxonomies() )->register();

        $category = $GLOBALS['ksNewsTestTaxonomies']['news_category'];
        $tag      = $GLOBALS['ksNewsTestTaxonomies']['news_tag'];

        self::assertSame( [ 'news' ], $category['object_type'] );
        self::assertSame( [ 'news' ], $tag['object_type'] );
        self::assertTrue( $category['args']['hierarchical'] );
        self::assertFalse( $tag['args']['hierarchical'] );
        self::assertSame( 'news/category', $category['args']['rewrite']['slug'] );
        self::assertSame( 'news/tag', $tag['args']['rewrite']['slug'] );
        self::assertTrue( $category['args']['show_in_rest'] );
        self::assertTrue( $tag['args']['show_in_rest'] );
    }

    public function testTaxonomyArgumentsCanBeFilteredIndependently(): void {
        $GLOBALS['ksNewsTestFilters']['ksNewsCategoryTaxonomyArgs'] = static function ( array $args ): array {
            $args['show_admin_column'] = false;

            return $args;
        };

        ( new Taxonomies() )->register();

        self::assertFalse( $GLOBALS['ksNewsTestTaxonomies']['news_category']['args']['show_admin_column'] );
        self::assertTrue( $GLOBALS['ksNewsTestTaxonomies']['news_tag']['args']['show_admin_column'] );
    }
}
