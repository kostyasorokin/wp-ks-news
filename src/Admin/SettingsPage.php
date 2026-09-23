<?php
/**
 * News archive settings in the WordPress Settings menu.
 *
 * @package ks-news
 */

declare( strict_types=1 );

namespace KonstantinSorokin\News\Admin;

use KonstantinSorokin\News\Archive;
use KonstantinSorokin\News\Attribute\Hook;

defined( 'ABSPATH' ) || exit;

final class SettingsPage {

    private const PAGE_SLUG      = 'ks-news';
    private const SETTINGS_GROUP = 'ks_news';
    private const SECTION_ID     = 'ks_news_archive';

    #[Hook( 'admin_menu' )]
    public function registerMenu(): void {
        add_options_page(
            __( 'KS News', 'ks-news' ),
            __( 'KS News', 'ks-news' ),
            'manage_options',
            self::PAGE_SLUG,
            [ $this, 'render' ]
        );
    }

    #[Hook( 'admin_init' )]
    public function registerSettings(): void {
        register_setting(
            self::SETTINGS_GROUP,
            Archive::OPTION_NAME,
            [
                'type'              => 'integer',
                'sanitize_callback' => [ $this, 'sanitizePerPage' ],
                'default'           => Archive::DEFAULT_PER_PAGE,
            ]
        );

        add_settings_section(
            self::SECTION_ID,
            __( 'News archive', 'ks-news' ),
            '__return_false',
            self::PAGE_SLUG
        );

        add_settings_field(
            Archive::OPTION_NAME,
            __( 'News per page', 'ks-news' ),
            [ $this, 'renderPerPageField' ],
            self::PAGE_SLUG,
            self::SECTION_ID,
            [ 'label_for' => Archive::OPTION_NAME ]
        );
    }

    public function sanitizePerPage( mixed $value ): int {
        $perPage = Archive::validatedPerPage( $value );

        if ( null !== $perPage ) {
            return $perPage;
        }

        add_settings_error(
            Archive::OPTION_NAME,
            'invalid_news_per_page',
            __( 'Enter a whole number from 1 to 100.', 'ks-news' )
        );

        return Archive::perPage();
    }

    public function renderPerPageField(): void {
        printf(
            '<input id="%1$s" name="%1$s" type="number" min="%2$d" max="%3$d" step="1" value="%4$d" class="small-text" required>',
            esc_attr( Archive::OPTION_NAME ),
            Archive::MIN_PER_PAGE,
            Archive::MAX_PER_PAGE,
            Archive::perPage()
        );

        printf(
            '<p class="description">%s</p>',
            esc_html__( 'Applies to the News archive, categories and tags.', 'ks-news' )
        );
    }

    public function render(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to manage these settings.', 'ks-news' ) );
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'KS News', 'ks-news' ); ?></h1>
            <?php settings_errors(); ?>
            <form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
                <?php
                settings_fields( self::SETTINGS_GROUP );
                do_settings_sections( self::PAGE_SLUG );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}
