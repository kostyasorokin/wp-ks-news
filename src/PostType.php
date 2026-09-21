<?php
/**
 * Post type: news.
 *
 * @package ks-news
 */

declare( strict_types=1 );

namespace KonstantinSorokin\News;

use KonstantinSorokin\News\Attribute\Hook;

defined( 'ABSPATH' ) || exit;

final class PostType {

    /** Public because activation registers it before flushing rewrite rules. */
    #[Hook( 'init' )]
    public function register(): void {
        $args = [
            'label'              => __( 'News', 'ks-news' ),
            'labels'             => $this->labels(),
            'public'             => true,
            'has_archive'        => 'news',
            'rewrite'            => [
                'slug'       => 'news',
                'with_front' => false,
            ],
            'menu_icon'          => 'dashicons-megaphone',
            'menu_position'      => 22,
            'supports'           => [ 'title', 'editor', 'author', 'excerpt', 'thumbnail', 'comments', 'revisions' ],
            'taxonomies'         => [ KS_NEWS_CATEGORY_TAXONOMY, KS_NEWS_TAG_TAXONOMY ],
            'show_in_rest'       => true,
            'rest_base'          => KS_NEWS_POST_TYPE,
            'publicly_queryable' => true,
        ];

        /** @param array<string, mixed> $args Arguments passed to register_post_type(). */
        register_post_type( KS_NEWS_POST_TYPE, (array) apply_filters( 'ksNewsPostTypeArgs', $args ) );
    }

    /**
     * Keep the relationship explicit when a filter replaces the post type's
     * taxonomies argument.
     */
    #[Hook( 'init', 20 )]
    public function attachTaxonomies(): void {
        register_taxonomy_for_object_type( KS_NEWS_CATEGORY_TAXONOMY, KS_NEWS_POST_TYPE );
        register_taxonomy_for_object_type( KS_NEWS_TAG_TAXONOMY, KS_NEWS_POST_TYPE );
    }

    /** @return array<string, string> */
    private function labels(): array {
        return [
            'name'                   => __( 'News', 'ks-news' ),
            'singular_name'          => __( 'News item', 'ks-news' ),
            'menu_name'              => __( 'News', 'ks-news' ),
            'name_admin_bar'         => __( 'News item', 'ks-news' ),
            'all_items'              => __( 'All news', 'ks-news' ),
            'add_new'                => __( 'Add news item', 'ks-news' ),
            'add_new_item'           => __( 'Add new news item', 'ks-news' ),
            'edit_item'              => __( 'Edit news item', 'ks-news' ),
            'new_item'               => __( 'New news item', 'ks-news' ),
            'view_item'              => __( 'View news item', 'ks-news' ),
            'view_items'             => __( 'View news', 'ks-news' ),
            'search_items'           => __( 'Search news', 'ks-news' ),
            'not_found'              => __( 'No news found', 'ks-news' ),
            'not_found_in_trash'     => __( 'No news found in Trash', 'ks-news' ),
            'archives'               => __( 'News archives', 'ks-news' ),
            'attributes'             => __( 'News attributes', 'ks-news' ),
            'featured_image'         => __( 'News image', 'ks-news' ),
            'set_featured_image'     => __( 'Set news image', 'ks-news' ),
            'remove_featured_image'  => __( 'Remove news image', 'ks-news' ),
            'use_featured_image'     => __( 'Use as news image', 'ks-news' ),
            'item_published'         => __( 'News item published.', 'ks-news' ),
            'item_updated'           => __( 'News item updated.', 'ks-news' ),
            'item_reverted_to_draft' => __( 'News item reverted to draft.', 'ks-news' ),
            'item_scheduled'         => __( 'News item scheduled.', 'ks-news' ),
            'filter_items_list'      => __( 'Filter news list', 'ks-news' ),
            'items_list_navigation'  => __( 'News list navigation', 'ks-news' ),
            'items_list'             => __( 'News list', 'ks-news' ),
            'item_link'              => __( 'News item link', 'ks-news' ),
            'item_link_description'  => __( 'A link to a news item.', 'ks-news' ),
        ];
    }
}
