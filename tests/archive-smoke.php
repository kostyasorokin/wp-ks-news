<?php
/**
 * Run with: php tests/archive-smoke.php
 *
 * Isolated archive sizing checks without a database or WordPress bootstrap.
 */

declare( strict_types=1 );

define( 'ABSPATH', __DIR__ );
define( 'KS_NEWS_POST_TYPE', 'news' );
define( 'KS_NEWS_CATEGORY_TAXONOMY', 'news_category' );
define( 'KS_NEWS_TAG_TAXONOMY', 'news_tag' );

$ksNewsIsAdmin = false;
$ksNewsOption  = null;
$ksNewsErrors  = [];
$ksNewsMenu    = [];
$ksNewsSetting = [];
$ksNewsField   = [];

function is_admin(): bool {
    global $ksNewsIsAdmin;
    return $ksNewsIsAdmin;
}

function get_option( string $name, mixed $default = false ): mixed {
    global $ksNewsOption;
    return null === $ksNewsOption ? $default : $ksNewsOption;
}

function __( string $message, string $domain = '' ): string {
    return $message;
}

function add_settings_error( string $setting, string $code, string $message ): void {
    global $ksNewsErrors;
    $ksNewsErrors[] = [ $setting, $code, $message ];
}

function add_options_page( mixed ...$args ): void {
    global $ksNewsMenu;
    $ksNewsMenu = $args;
}

function register_setting( mixed ...$args ): void {
    global $ksNewsSetting;
    $ksNewsSetting = $args;
}

function add_settings_section( mixed ...$args ): void {}

function add_settings_field( mixed ...$args ): void {
    global $ksNewsField;
    $ksNewsField = $args;
}

function esc_attr( string $value ): string {
    return htmlspecialchars( $value, ENT_QUOTES );
}

function esc_html__( string $message, string $domain = '' ): string {
    return $message;
}

class WP_Query {

    public array $vars = [];

    public function __construct(
        private bool $main,
        private string $archiveType = '',
        private string $taxonomy = ''
    ) {}

    public function is_main_query(): bool {
        return $this->main;
    }

    public function is_post_type_archive( string $postType ): bool {
        return $this->archiveType === $postType;
    }

    public function is_tax( array $taxonomies ): bool {
        return in_array( $this->taxonomy, $taxonomies, true );
    }

    public function set( string $key, mixed $value ): void {
        $this->vars[ $key ] = $value;
    }
}

require dirname( __DIR__ ) . '/vendor/autoload.php';

use KonstantinSorokin\News\Archive;
use KonstantinSorokin\News\Admin\SettingsPage;

function check( bool $condition, string $message ): void {
    if ( ! $condition ) {
        throw new RuntimeException( $message );
    }
}

check( 32 === Archive::perPage(), 'News archives default to 32 items.' );

foreach ( [ 1, 16, 32, 64, 100 ] as $size ) {
    $ksNewsOption = (string) $size;
    check( $size === Archive::perPage(), 'Configured page sizes in range are accepted.' );
}

foreach ( [ '0', '-1', '101', 'all', [ '64' ], 1.5 ] as $invalid ) {
    $ksNewsOption = $invalid;
    check( 32 === Archive::perPage(), 'Invalid stored page sizes fall back to 32.' );
}

$ksNewsOption = null;
$_GET['news_per_page'] = '1';
check( 32 === Archive::perPage(), 'Legacy public query parameters cannot override the admin setting.' );

$settings = new SettingsPage();
$settings->registerMenu();
check( 'manage_options' === $ksNewsMenu[2] && 'ks-news' === $ksNewsMenu[3], 'KS News is available under Settings to administrators.' );
$settings->registerSettings();
check( 'ks_news' === $ksNewsSetting[0] && Archive::OPTION_NAME === $ksNewsSetting[1], 'The News page-size option is registered.' );
check( 'ks-news' === $ksNewsField[3] && 'ks_news_archive' === $ksNewsField[4], 'The page-size field appears on the KS News settings page.' );
check( 48 === $settings->sanitizePerPage( '48' ), 'Valid admin input is accepted.' );
$ksNewsOption = 24;
check( 24 === $settings->sanitizePerPage( '101' ), 'Invalid admin input preserves the stored setting.' );
check( 1 === count( $ksNewsErrors ), 'Invalid admin input produces a settings error.' );

ob_start();
$settings->renderPerPageField();
$ksNewsFieldHtml = ob_get_clean();
check( str_contains( $ksNewsFieldHtml, 'value="24"' ) && str_contains( $ksNewsFieldHtml, 'max="100"' ), 'The admin field shows the saved value and limit.' );

$ksNewsOption = null;
$archive = new Archive();
$mainNews = new WP_Query( true, 'news' );
$archive->setPostsPerPage( $mainNews );
check( 32 === $mainNews->vars['posts_per_page'], 'The main News archive uses the default page size.' );

$ksNewsOption = '64';
$category = new WP_Query( true, '', 'news_category' );
$archive->setPostsPerPage( $category );
check( 64 === $category->vars['posts_per_page'], 'Category archives honor the admin setting.' );

$tag = new WP_Query( true, '', 'news_tag' );
$archive->setPostsPerPage( $tag );
check( 64 === $tag->vars['posts_per_page'], 'Tag archives honor the admin setting.' );

$secondary = new WP_Query( false, 'news' );
$archive->setPostsPerPage( $secondary );
check( [] === $secondary->vars, 'Secondary queries remain unchanged.' );

$other = new WP_Query( true, 'post' );
$archive->setPostsPerPage( $other );
check( [] === $other->vars, 'Other post types remain unchanged.' );

$ksNewsIsAdmin = true;
$archive->setPostsPerPage( $mainNews );
check( 32 === $mainNews->vars['posts_per_page'], 'Admin queries remain unchanged.' );

echo "News archive smoke tests passed.\n";
