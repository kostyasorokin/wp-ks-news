<?php
/**
 * Turns #[Hook] attributes into WordPress registrations.
 *
 * @package ks-news
 */

declare( strict_types=1 );

namespace KonstantinSorokin\News\Support;

use KonstantinSorokin\News\Attribute\Hook;
use ReflectionMethod;
use ReflectionObject;

defined( 'ABSPATH' ) || exit;

final class HookBinder {

    public static function bind( object $service ): void {
        foreach ( ( new ReflectionObject( $service ) )->getMethods( ReflectionMethod::IS_PUBLIC ) as $method ) {
            foreach ( $method->getAttributes( Hook::class ) as $attribute ) {
                $hook = $attribute->newInstance();

                add_filter(
                    $hook->name,
                    $method->getClosure( $service ),
                    $hook->priority,
                    $hook->args
                );
            }
        }
    }
}
