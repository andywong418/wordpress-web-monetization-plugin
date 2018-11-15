<?php
/*
Plugin Name: wordpress-web-monetization
Description: A wordpress plugin that allows your site to receive payment from browsers which have web monetization enabled.
Author: Andros Wong
Version: 1.0
Author URI: http://github.com/andywong418
*/

class WordPressWebMonetization
{
  static function init() {
    add_action('wp_head', array( __CLASS__, 'custom_js_register' ));

  }

  public function custom_js_register() {
    wp_register_script('polyfill', 'https://polyfill.webmonetization.org/polyfill.js');
    wp_register_script('coil', 'https://cdn.coil.com/donate.js');
    wp_register_script('inject', plugins_url( '/', __FILE__ ) . 'inject.js');
    wp_enqueue_script('polyfill');
    wp_enqueue_script('coil');
    wp_enqueue_script('inject');
    $dataToBePassed = array(
     get_option('payment_pointer_option'),
     get_option('add_coil_advert_option')
    );
    wp_localize_script( 'inject', 'php_vars', $dataToBePassed );
  }
  // Add options to let wordpress user set up a payment pointer and for them to choose pages to inject
}

class WebMonetizationSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin',
            'Web Monetization Settings',
            'manage_options',
            'web-monetization-admin',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = array(
            'payment_pointer'=> get_option( 'payment_pointer_option' ),
            'add_coil_advert'=> get_option('add_coil_advert_option')
        );
        ?>
        <div class="wrap">
            <h1>Web Monetization Settings</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'my_option_group' );
                do_settings_sections( 'web-monetization-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {

        add_settings_section(
            'setting_section_id', // ID
            'My Custom Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'web-monetization-admin' // Page
        );

        add_settings_field(
            'payment_pointer', // ID
            'Payment Pointer', // Title
            array( $this, 'payment_pointer_callback' ), // Callback
            'web-monetization-admin', // Page
            'setting_section_id' // Section
        );
        add_settings_field(
            'add_coil_advert',
            'Advertise Coil',
            array($this, 'add_coil_advert_callback'),
            'web-monetization-admin',
            'setting_section_id'
        );
        register_setting(
            'my_option_group', // Option group
            'payment_pointer_option', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );
        register_setting(
            'my_option_group',
            'add_coil_advert_option'
        );
        
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();

        if( isset( $input['payment_pointer'] ) )
            $new_input['payment_pointer'] = sanitize_text_field( $input['payment_pointer'] );

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function add_coil_advert_callback()
    {
        $options = get_option( 'add_coil_advert_option' );
        echo "<script>console.log( \"PHP DEBUG: $options\" );</script>";
        $html = '<input type="checkbox" id="add_coil_advert" name="add_coil_advert_option[add_coil_advert]" value="1"' . checked( 1, $options['add_coil_advert'], false ) . '/>';
        $html .= '<label for="add_coil_advert">This displays a widget that advertises Coil to those who do not have a subscription.</label>';

        echo $html;
    }

    public function payment_pointer_callback()
    {
        $options = get_option('payment_pointer_option');
        printf(
            '<input type="text" style="min-width: 300px;" id="payment_pointer" name="payment_pointer_option[payment_pointer]" value="%s" />',
            isset( $this->options['payment_pointer'] ) ? esc_attr( $options['payment_pointer']) : ''
        );
    }

}


if( ! defined('ABSPATH' ) ) {
  die;
}
if( is_admin() )
    $my_settings_page = new WebMonetizationSettingsPage();
if( class_exists('WordPressWebMonetization' ) ) {
  $wordPressWebMonetization = new WordPressWebMonetization();
  $wordPressWebMonetization::init();
}
