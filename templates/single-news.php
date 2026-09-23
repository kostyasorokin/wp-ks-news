<?php
/**
 * One news item with its own categories and tags.
 *
 * @package ks-news
 */

use KonstantinSorokin\Services\Helpers\Services;

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();

    $ksNewsPostId     = (int) get_the_ID();
    $ksNewsArchiveUrl = get_post_type_archive_link( KS_NEWS_POST_TYPE );
    $ksNewsCategories = get_the_terms( $ksNewsPostId, KS_NEWS_CATEGORY_TAXONOMY );
    $ksNewsCategories = is_array( $ksNewsCategories ) ? $ksNewsCategories : [];
    $ksNewsTags       = get_the_terms( $ksNewsPostId, KS_NEWS_TAG_TAXONOMY );
    $ksNewsTags       = is_array( $ksNewsTags ) ? $ksNewsTags : [];
    $ksNewsPrimary    = $ksNewsCategories[0] ?? null;
    ?>

    <section class="ks-news-single kp-post-single">
        <div class="ks-news-single__container container">
            <div class="ks-news-single__layout">
                <article id="post-<?php the_ID(); ?>" <?php post_class( 'ks-news-single__article' ); ?>>
                    <header class="ks-news-single__head kp-post-head">
                        <nav class="kp-breadcrumb kp-breadcrumb--light" aria-label="<?php esc_attr_e( 'Breadcrumbs', 'ks-news' ); ?>">
                            <?php if ( is_string( $ksNewsArchiveUrl ) && '' !== $ksNewsArchiveUrl ) : ?>
                                <a href="<?php echo esc_url( $ksNewsArchiveUrl ); ?>"><?php esc_html_e( 'News', 'ks-news' ); ?></a>
                            <?php endif; ?>
                            <?php if ( $ksNewsPrimary instanceof WP_Term ) : ?>
                                <?php $ksNewsPrimaryUrl = get_term_link( $ksNewsPrimary ); ?>
                                <?php if ( ! is_wp_error( $ksNewsPrimaryUrl ) ) : ?>
                                    <span aria-hidden="true">/</span>
                                    <a href="<?php echo esc_url( $ksNewsPrimaryUrl ); ?>"><?php echo esc_html( $ksNewsPrimary->name ); ?></a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </nav>

                        <h1 class="kp-post-head__title"><?php echo esc_html( get_the_title() ); ?></h1>

                        <div class="kp-post-head__meta">
                            <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
                            <?php if ( [] !== $ksNewsCategories ) : ?>
                                <span class="ks-news-single__categories kp-post-head__cats">
                                    <?php foreach ( $ksNewsCategories as $ksNewsCategory ) : ?>
                                        <?php
                                        $ksNewsCategoryUrl = get_term_link( $ksNewsCategory );

                                        if ( is_wp_error( $ksNewsCategoryUrl ) ) {
                                            continue;
                                        }
                                        ?>
                                        <a href="<?php echo esc_url( $ksNewsCategoryUrl ); ?>"><?php echo esc_html( $ksNewsCategory->name ); ?></a>
                                    <?php endforeach; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </header>

                    <?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
                        <figure class="kp-post-single__media">
                            <?php the_post_thumbnail( 'large', [ 'class' => 'kp-post-single__image' ] ); ?>
                        </figure>
                    <?php endif; ?>

                    <div class="ks-news-single__content kp-post-content">
                        <?php
                        the_content();
                        wp_link_pages(
                            [
                                'before' => '<nav class="page-links">',
                                'after'  => '</nav>',
                            ]
                        );
                        ?>
                    </div>

                    <?php if ( [] !== $ksNewsTags && ! post_password_required() ) : ?>
                        <footer class="ks-news-single__tags kp-post-tags">
                            <h2 class="ks-news-single__tags-title"><?php esc_html_e( 'News tags', 'ks-news' ); ?></h2>
                            <div class="ks-news-single__tag-links">
                                <?php foreach ( $ksNewsTags as $ksNewsTag ) : ?>
                                    <?php
                                    $ksNewsTagUrl = get_term_link( $ksNewsTag );

                                    if ( is_wp_error( $ksNewsTagUrl ) ) {
                                        continue;
                                    }
                                    ?>
                                    <a href="<?php echo esc_url( $ksNewsTagUrl ); ?>"><?php echo esc_html( $ksNewsTag->name ); ?></a>
                                <?php endforeach; ?>
                            </div>
                        </footer>
                    <?php endif; ?>
                </article>

                <aside class="ks-news-single__sidebar">
                    <?php
                    $ksNewsRecent = new WP_Query(
                        [
                            'post_type'           => KS_NEWS_POST_TYPE,
                            'post_status'         => 'publish',
                            'posts_per_page'      => 5,
                            'post__not_in'        => [ $ksNewsPostId ],
                            'ignore_sticky_posts' => true,
                            'no_found_rows'       => true,
                        ]
                    );
                    ?>
                    <?php if ( $ksNewsRecent->have_posts() ) : ?>
                        <nav class="kp-sidebar-nav" aria-label="<?php esc_attr_e( 'Recent news', 'ks-news' ); ?>">
                            <h2 class="kp-sidebar-nav__title"><?php esc_html_e( 'Recent news', 'ks-news' ); ?></h2>
                            <ul class="kp-sidebar-nav__list list-unstyled">
                                <?php while ( $ksNewsRecent->have_posts() ) : ?>
                                    <?php $ksNewsRecent->the_post(); ?>
                                    <li><a href="<?php echo esc_url( get_permalink() ); ?>"><span><?php echo esc_html( get_the_title() ); ?></span></a></li>
                                <?php endwhile; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                    <?php wp_reset_postdata(); ?>

                    <?php $ksNewsPractices = class_exists( Services::class ) ? Services::categories() : []; ?>
                    <?php if ( [] !== $ksNewsPractices ) : ?>
                        <nav class="kp-sidebar-nav" aria-label="<?php esc_attr_e( 'Practice areas', 'ks-news' ); ?>">
                            <h2 class="kp-sidebar-nav__title"><?php esc_html_e( 'Practice areas', 'ks-news' ); ?></h2>
                            <ul class="kp-sidebar-nav__list list-unstyled">
                                <?php foreach ( $ksNewsPractices as $ksNewsPractice ) : ?>
                                    <?php $ksNewsPracticeUrl = get_term_link( $ksNewsPractice ); ?>
                                    <?php if ( is_wp_error( $ksNewsPracticeUrl ) ) : ?>
                                        <?php continue; ?>
                                    <?php endif; ?>
                                    <li><a href="<?php echo esc_url( $ksNewsPracticeUrl ); ?>"><span><?php echo esc_html( $ksNewsPractice->name ); ?></span></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </aside>
            </div>
        </div>
    </section>

    <?php
    get_template_part(
        'template-parts/blocks/consultation',
        null,
        [
            'title' => __( 'Need advice on this issue?', 'ks-news' ),
            'lead'  => __( 'Describe your situation — we will contact you on the next business day and suggest a plan of action.', 'ks-news' ),
        ]
    );
    ?>

    <?php
endwhile;

get_footer();
