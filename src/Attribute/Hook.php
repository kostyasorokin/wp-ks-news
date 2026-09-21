<?php
/**
 * Declares a WordPress hook on a public method.
 *
 * @package ks-news
 */

declare( strict_types=1 );

namespace KonstantinSorokin\News\Attribute;

defined( 'ABSPATH' ) || exit;

#[\Attribute( \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE )]
final class Hook {

    public function __construct(
        public readonly string $name,
        public readonly int $priority = 10,
        public readonly int $args = 1,
    ) {
    }
}
