<?php
/**
 * Taxonomies owned exclusively by news.
 *
 * @package ks-news
 */

declare( strict_types=1 );

namespace KonstantinSorokin\News\Taxonomy;

use KonstantinSorokin\News\Attribute\Hook;

defined( 'ABSPATH' ) || exit;

final class Taxonomies {

    /** Public because activation registers both vocabularies before rewrites are flushed. */
    #[Hook( 'init' )]
    public function register(): void {
        $category = [
            'label'             => __( 'News categories', 'ks-news' ),
            'labels'            => $this->categoryLabels(),
            'public'            => true,
            'hierarchical'      => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rest_base'         => KS_NEWS_CATEGORY_TAXONOMY,
            'query_var'         => true,
            'rewrite'           => [
                'slug'         => 'news/category',
                'with_front'   => false,
                'hierarchical' => true,
            ],
        ];
        $tag      = [
            'label'             => __( 'News tags', 'ks-news' ),
            'labels'            => $this->tagLabels(),
            'public'            => true,
            'hierarchical'      => false,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rest_base'         => KS_NEWS_TAG_TAXONOMY,
            'query_var'         => true,
            'rewrite'           => [
                'slug'       => 'news/tag',
                'with_front' => false,
            ],
        ];

        /** @param array<string, mixed> $category Arguments passed to register_taxonomy(). */
        register_taxonomy(
            KS_NEWS_CATEGORY_TAXONOMY,
            [ KS_NEWS_POST_TYPE ],
            (array) apply_filters( 'ksNewsCategoryTaxonomyArgs', $category )
        );

        /** @param array<string, mixed> $tag Arguments passed to register_taxonomy(). */
        register_taxonomy(
            KS_NEWS_TAG_TAXONOMY,
            [ KS_NEWS_POST_TYPE ],
            (array) apply_filters( 'ksNewsTagTaxonomyArgs', $tag )
        );
    }

    /** @return array<string, string> */
    private function categoryLabels(): array {
        return [
            'name'                  => __( 'News categories', 'ks-news' ),
            'singular_name'         => __( 'News category', 'ks-news' ),
            'menu_name'             => __( 'News categories', 'ks-news' ),
            'all_items'             => __( 'All news categories', 'ks-news' ),
            'edit_item'             => __( 'Edit news category', 'ks-news' ),
            'view_item'             => __( 'View news category', 'ks-news' ),
            'update_item'           => __( 'Update news category', 'ks-news' ),
            'add_new_item'          => __( 'Add new news category', 'ks-news' ),
            'new_item_name'         => __( 'New news category name', 'ks-news' ),
            'parent_item'           => __( 'Parent news category', 'ks-news' ),
            'parent_item_colon'     => __( 'Parent news category:', 'ks-news' ),
            'search_items'          => __( 'Search news categories', 'ks-news' ),
            'not_found'             => __( 'No news categories found', 'ks-news' ),
            'no_terms'              => __( 'No news categories', 'ks-news' ),
            'filter_by_item'        => __( 'Filter by news category', 'ks-news' ),
            'items_list_navigation' => __( 'News categories list navigation', 'ks-news' ),
            'items_list'            => __( 'News categories list', 'ks-news' ),
            'back_to_items'         => __( '&larr; Go to news categories', 'ks-news' ),
        ];
    }

    /** @return array<string, string> */
    private function tagLabels(): array {
        return [
            'name'                       => __( 'News tags', 'ks-news' ),
            'singular_name'              => __( 'News tag', 'ks-news' ),
            'menu_name'                  => __( 'News tags', 'ks-news' ),
            'all_items'                  => __( 'All news tags', 'ks-news' ),
            'edit_item'                  => __( 'Edit news tag', 'ks-news' ),
            'view_item'                  => __( 'View news tag', 'ks-news' ),
            'update_item'                => __( 'Update news tag', 'ks-news' ),
            'add_new_item'               => __( 'Add new news tag', 'ks-news' ),
            'new_item_name'              => __( 'New news tag name', 'ks-news' ),
            'search_items'               => __( 'Search news tags', 'ks-news' ),
            'popular_items'              => __( 'Popular news tags', 'ks-news' ),
            'separate_items_with_commas' => __( 'Separate news tags with commas', 'ks-news' ),
            'add_or_remove_items'        => __( 'Add or remove news tags', 'ks-news' ),
            'choose_from_most_used'      => __( 'Choose from the most used news tags', 'ks-news' ),
            'not_found'                  => __( 'No news tags found', 'ks-news' ),
            'no_terms'                   => __( 'No news tags', 'ks-news' ),
            'filter_by_item'             => __( 'Filter by news tag', 'ks-news' ),
            'items_list_navigation'      => __( 'News tags list navigation', 'ks-news' ),
            'items_list'                 => __( 'News tags list', 'ks-news' ),
            'back_to_items'              => __( '&larr; Go to news tags', 'ks-news' ),
        ];
    }
}
