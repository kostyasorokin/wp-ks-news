<?php
/**
 * Preserve links to category and tag archives after editorial slug changes.
 *
 * @package ks-news
 */

declare( strict_types=1 );

namespace KonstantinSorokin\News\Taxonomy;

use KonstantinSorokin\News\Attribute\Hook;
use WP_Term;

defined( 'ABSPATH' ) || exit;

final class LegacyTermRedirect {

    /** @var array<string, string> */
    private const CATEGORY_SLUGS = [
        'viiskove-pravo'            => 'military-law',
        'it-pravo-ta-kiberbezpeka'  => 'it-law-cybersecurity',
        'tsyvilne-ta-simeine-pravo' => 'civil-family-law',
    ];

    /** @var array<string, string> */
    private const TAG_SLUGS = [
        'bankivskyi-vklad'             => 'bank-deposit',
        'vidstrochka-vid-mobilizatsii' => 'mobilization-deferment',
        'viiskovyi-kontrakt'           => 'military-contract',
        'viiskovopoloneni'             => 'prisoners-of-war',
        'vlk'                          => 'military-medical-commission',
        'znykli-bezvisty'              => 'missing-persons',
        'kibershakhraistvo'            => 'cyber-fraud',
        'povernenna-na-sluzhbu'        => 'return-to-service',
        'podil-maina-podruzhzhia'      => 'marital-property-division',
        'szch'                         => 'awol',
        'spadshchyna'                  => 'inheritance',
        'spilni-borhy'                 => 'joint-debts',
        'fishynh'                      => 'phishing',
        'tsnap'                        => 'administrative-service-center',
    ];

    #[Hook( 'template_redirect', 1 )]
    public function redirect(): void {
        if ( ! is_404() ) {
            return;
        }

        foreach ( [
            KS_NEWS_CATEGORY_TAXONOMY => self::CATEGORY_SLUGS,
            KS_NEWS_TAG_TAXONOMY      => self::TAG_SLUGS,
        ] as $taxonomy => $slugs ) {
            $requested = get_query_var( $taxonomy );

            if ( ! is_string( $requested ) || '' === $requested ) {
                if ( get_query_var( 'taxonomy' ) !== $taxonomy ) {
                    continue;
                }

                $requested = get_query_var( 'term' );
            }

            if ( ! is_string( $requested ) || ! isset( $slugs[ $requested ] ) ) {
                continue;
            }

            $term = get_term_by( 'slug', $slugs[ $requested ], $taxonomy );

            if ( ! $term instanceof WP_Term ) {
                return;
            }

            $url = get_term_link( $term );

            if ( is_wp_error( $url ) ) {
                return;
            }

            wp_safe_redirect( $url, 301, 'KS News' );
            exit;
        }
    }
}
