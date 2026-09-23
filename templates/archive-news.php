<?php
/**
 * News archive and news taxonomy listing.
 *
 * @package ks-news
 */

use KonstantinSorokin\News\Template\TemplateLoader;

defined( 'ABSPATH' ) || exit;

get_header();

$ksNewsCategories = get_terms(
    [
        'taxonomy'   => KS_NEWS_CATEGORY_TAXONOMY,
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ]
);
$ksNewsCategories = is_array( $ksNewsCategories ) ? $ksNewsCategories : [];
$ksNewsCurrent    = get_queried_object();
$ksNewsCategoryId = $ksNewsCurrent instanceof WP_Term && KS_NEWS_CATEGORY_TAXONOMY === $ksNewsCurrent->taxonomy
    ? (int) $ksNewsCurrent->term_id
    : 0;
$ksNewsTitle      = $ksNewsCurrent instanceof WP_Term
    ? $ksNewsCurrent->name
    : __( 'News', 'ks-news' );
$ksNewsArchiveUrl = get_post_type_archive_link( KS_NEWS_POST_TYPE );
?>

<section class="ks-news-archive kp-cases-archive">
    <div class="ks-news-archive__container container">
        <header class="ks-news-archive__header kp-page-head">
            <h1><?php echo esc_html( $ksNewsTitle ); ?></h1>

            <nav class="ks-news-category-nav" aria-label="<?php esc_attr_e( 'News categories', 'ks-news' ); ?>">
                <ul class="ks-news-category-nav__list">
                    <?php if ( is_string( $ksNewsArchiveUrl ) && '' !== $ksNewsArchiveUrl ) : ?>
                        <li>
                            <a class="ks-news-category-nav__link<?php echo is_post_type_archive( KS_NEWS_POST_TYPE ) ? ' is-current' : ''; ?>"
                                    href="<?php echo esc_url( $ksNewsArchiveUrl ); ?>"<?php echo is_post_type_archive( KS_NEWS_POST_TYPE ) ? ' aria-current="page"' : ''; ?>>
                                <?php esc_html_e( 'All news', 'ks-news' ); ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php foreach ( $ksNewsCategories as $ksNewsCategory ) : ?>
                        <?php
                        $ksNewsCategoryUrl = get_term_link( $ksNewsCategory );

                        if ( is_wp_error( $ksNewsCategoryUrl ) ) {
                            continue;
                        }

                        $ksNewsIsCurrent = $ksNewsCategoryId === (int) $ksNewsCategory->term_id;
                        ?>
                        <li>
                            <a class="ks-news-category-nav__link<?php echo $ksNewsIsCurrent ? ' is-current' : ''; ?>"
                                    href="<?php echo esc_url( $ksNewsCategoryUrl ); ?>"<?php echo $ksNewsIsCurrent ? ' aria-current="page"' : ''; ?>>
                                <?php echo esc_html( $ksNewsCategory->name ); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </header>

        <?php if ( have_posts() ) : ?>
            <div class="ks-news-grid">
                <?php
                while ( have_posts() ) :
                    the_post();
                    TemplateLoader::card( (int) get_the_ID() );
                endwhile;
                ?>
            </div>

            <?php
            $ksNewsLinks = paginate_links(
                [
                    'type'      => 'array',
                    'mid_size'  => 2,
                    'prev_text' => esc_html__( 'Previous', 'ks-news' ),
                    'next_text' => esc_html__( 'Next', 'ks-news' ),
                ]
            );
            ?>
            <?php if ( is_array( $ksNewsLinks ) && [] !== $ksNewsLinks ) : ?>
                <nav class="kp-pagination" aria-label="<?php esc_attr_e( 'News pagination', 'ks-news' ); ?>">
                    <ul class="pagination justify-content-center">
                        <?php foreach ( $ksNewsLinks as $ksNewsLink ) : ?>
                            <?php
                            $ksNewsTag = new WP_HTML_Tag_Processor( $ksNewsLink );

                            if ( ! $ksNewsTag->next_tag() ) {
                                continue;
                            }

                            $ksNewsIsCurrent = 'page' === $ksNewsTag->get_attribute( 'aria-current' );
                            $ksNewsIsDots    = true === $ksNewsTag->has_class( 'dots' );

                            foreach ( [ 'page-numbers', 'current', 'dots', 'prev', 'next' ] as $ksNewsCoreClass ) {
                                $ksNewsTag->remove_class( $ksNewsCoreClass );
                            }

                            $ksNewsTag->add_class( 'page-link' );

                            if ( 'A' === $ksNewsTag->get_tag() ) {
                                $ksNewsHref = (string) $ksNewsTag->get_attribute( 'href' );
                                $ksNewsHref = remove_query_arg( 'news_per_page', $ksNewsHref );

                                $ksNewsTag->set_attribute( 'href', $ksNewsHref );
                            }
                            ?>
                            <li class="page-item<?php echo $ksNewsIsCurrent ? ' active' : ''; ?><?php echo $ksNewsIsDots ? ' disabled' : ''; ?>">
                                <?php echo $ksNewsTag->get_updated_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress pagination markup and escaped URLs. ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else : ?>
            <p class="ks-news-archive__empty"><?php esc_html_e( 'No news yet.', 'ks-news' ); ?></p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
