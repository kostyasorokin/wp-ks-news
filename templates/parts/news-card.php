<?php
/**
 * One news card without a thumbnail.
 *
 * @package ks-news
 *
 * @var array{post_id?: mixed} $args
 */

use KonstantinSorokin\News\Helpers\News;

defined( 'ABSPATH' ) || exit;

$ksNewsPost = get_post( absint( $args['post_id'] ?? 0 ) );

if ( ! $ksNewsPost instanceof WP_Post || KS_NEWS_POST_TYPE !== $ksNewsPost->post_type ) {
    return;
}

$ksNewsUrl     = get_permalink( $ksNewsPost );
$ksNewsLocked  = post_password_required( $ksNewsPost );
$ksNewsSource  = '' !== trim( $ksNewsPost->post_excerpt ) ? $ksNewsPost->post_excerpt : $ksNewsPost->post_content;
$ksNewsSummary = $ksNewsLocked ? '' : News::summary( $ksNewsSource );
$ksNewsTerms   = get_the_terms( $ksNewsPost, KS_NEWS_CATEGORY_TAXONOMY );
$ksNewsTerms   = is_array( $ksNewsTerms ) ? $ksNewsTerms : [];
?>

<article class="ks-news-card kp-post-card">
    <div class="ks-news-card__body kp-post-card__body">
        <time class="ks-news-card__date kp-post-date" datetime="<?php echo esc_attr( get_the_date( 'c', $ksNewsPost ) ); ?>">
            <?php echo esc_html( get_the_date( '', $ksNewsPost ) ); ?>
        </time>
        <?php if ( [] !== $ksNewsTerms ) : ?>
            <div class="ks-news-card__categories">
                <?php foreach ( $ksNewsTerms as $ksNewsTerm ) : ?>
                    <?php
                    $ksNewsTermUrl = get_term_link( $ksNewsTerm );

                    if ( is_wp_error( $ksNewsTermUrl ) ) {
                        continue;
                    }
                    ?>
                    <a href="<?php echo esc_url( $ksNewsTermUrl ); ?>"><?php echo esc_html( $ksNewsTerm->name ); ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <h2 class="ks-news-card__title kp-post-title">
            <a href="<?php echo esc_url( $ksNewsUrl ); ?>"><?php echo esc_html( get_the_title( $ksNewsPost ) ); ?></a>
        </h2>
        <?php if ( '' !== $ksNewsSummary ) : ?>
            <p class="ks-news-card__excerpt"><?php echo esc_html( $ksNewsSummary ); ?></p>
        <?php endif; ?>
    </div>
</article>
