<?php
/*
 * Plugin Name: Contact Form 7 - Fields Attributes
 * 
 * Description: Inject custom attributes in fields
 * 
 * Author: Agent 3W
 * Author URI: https://agent3w.com
 * 
 * Text Domain: a3w-cf7-fields-attributes
 * Domain Path: /languages
 * 
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * 
 * Version: 1.0.1
 */


if ( ! class_exists( 'A3W_CF7_Fields_Attributes' ) ) :

class A3W_CF7_Fields_Attributes
{
    const VERSION = '1.0.1';
    const TEXTDOMAIN = 'a3w-cf7-fields-attributes';
    protected static $_plugin_uri;
    protected static $_plugin_path;
    

    public static function start()
    {
        add_action( 'init', [ self::class, 'init' ] );
    }



    public static function init()
    {
        if ( ! class_exists( 'WPCF7' ) ) return;

        add_action( 'wp_loaded', [ self::class, 'load_text_domain' ] );

        add_filter( 'wpcf7_form_elements', [ self::class, 'add_custom_attributes' ] );

        // URL Encoder for attributes in CF7 form page
        add_action( 'admin_enqueue_scripts', [ self::class, 'admin_enqueue_scripts' ] );
    }


    public static function get_plugin_uri( $filename = "" )
    {
        if ( is_null( self::$_plugin_uri) ) {
            self::$_plugin_uri = plugin_dir_url( __FILE__ );
        }

        return self::$_plugin_uri . $filename;
    }



    public static function get_plugin_path( $filename = "" )
    {
        if ( is_null( self::$_plugin_path ) ) {
            self::$_plugin_path = plugin_dir_path( __FILE__ );
        }

        return self::$_plugin_path . $filename;
    }


    public static function load_text_domain()
    {
        $locale = apply_filters( 'plugin_locale', get_user_locale(), self::TEXTDOMAIN );
        load_textdomain( self::TEXTDOMAIN, self::get_plugin_path() . 'languages/' . $locale . ".mo" );
        load_plugin_textdomain( self::TEXTDOMAIN, false, self::get_plugin_path() . "languages" );
    }




    /**
     * Add custom attributes on inputs
     * Put "data-my-attribute" to use it, with or without value
     * 
     * Inspired by these contributions  
     * @see https://stackoverflow.com/a/68827506
     * @author Killian Leroux https://stackoverflow.com/users/1579452/killian-leroux
     * 
     * @see https://wordpress.org/support/topic/custom-data-attributes-for-individual-inputs/#post-14430388
     * @author kkow https://wordpress.org/support/users/kkow/
     *
     * @param string $content HTML content
     *
     * @return string HTML content
     */
    public static function add_custom_attributes( $content )
    {
        $contact_form = WPCF7_FormTagsManager::get_instance();
        $tags = $contact_form->get_scanned_tags();

        foreach ( $tags as $tag ) {

            $attributes = [];

            foreach ( (array) $tag['options'] as $option ) {
                if ( strpos($option, 'attr_') === 0 ) {
                    $option = explode(':', $option, 2);
                    $option[0] = str_replace( 'attr_', '', $option[0] );

                    $is_allowed = (bool) preg_match( '/^data-|title|aria-/', $option[0] );

                    /**
                     * @hook a3w_cf7_fields_attributes_allowed_names   
                     * 
                     * @param bool $is_allowed
                     * @param string $attr_name name of attribute
                     * @return bool true|false attribute's name is authorized or not
                     */
                    if ( apply_filters( 'a3w_cf7_fields_attributes_allowed_names', $is_allowed, $option[0] ) ) {
                        $option[1] = urldecode( $option[1] );
                        $attributes[ $option[0] ] = apply_filters( 'wpcf7_option_value', $option[1], $option[0] );
                    }
                }
            }
    
            if ( empty( $attributes ) ) continue; // no attribute => continue to next tag
            
            $attributesHtml = '';

            foreach ($attributes as $key => $value) {
                $attributesHtml .= " $key";

                if (is_string($value) && strlen($value) > 0) {
                    $attributesHtml .= "=\"$value\"";
                }
            }

            if ( ! empty( $attributesHtml ) ) {
                $nameHTML = 'name="' . $tag['name'] . '"';
                $content = str_replace($nameHTML, "$nameHTML $attributesHtml ", $content);
            }

        }

        return $content;
    }



    public static function admin_enqueue_scripts( $hook_suffix )
    {
        if ( false === strpos( $hook_suffix, 'wpcf7' ) ) {
            return;
        }

        wp_enqueue_style( 'a3w-cf7-fields-attributes', self::get_plugin_uri( 'assets/css/style.css' ), [], self::VERSION );
        wp_enqueue_script( 'a3w-cf7-fields-attributes', self::get_plugin_uri( 'assets/js/script.min.js' ), [ 'wp-i18n' ], self::VERSION, true );
        wp_set_script_translations( 'a3w-cf7-fields-attributes', 'a3w-cf7-fields-attributes', self::get_plugin_path( 'languages/' ) );
        wp_localize_script( 'a3w-cf7-fields-attributes', 'a3wVar', [
            'panelHTML' => self::get_panel_html()
        ] );
    }



    public static function get_panel_html()
    {
        ob_start();
        ?>
        <div id="a3w-attribute-generator" style="display:none;">
            <div class="overlay close-a3w-generator"></div>
            <div class="panel">
                <div class="panel-header">
                    <h4><?php echo __( "Custom attribute generator", 'a3w-cf7-fields-attributes' ); ?></h4>
                    <button role="button" class="close close-a3w-generator" title="<?php echo __( "Close", 'a3w-cf7-fields-attributes' ); ?>"><?php echo __( "Close", 'a3w-cf7-fields-attributes' ); ?></button>
                </div>
                <div class="panel-body">
                    <div class="form">
                        <div class="form-field">
                            <label for="a3w-attr-name"><?php echo __( "Attribute's name", 'a3w-cf7-fields-attributes' ); ?></label>
                            <input type="text" name="a3w-attr-name" id="a3w-attr-name" />
                        </div>
                        <div class="form-field">
                            <label for="a3w-attr-content"><?php echo __( "Attribute's content", 'a3w-cf7-fields-attributes' ); ?></label>
                            <input type="text" name="a3w-attr-content" id="a3w-attr-content" />
                        </div>
                    </div>
                    <div class="a3w-attr-cf7-wrapper">
                        <input type="text" id="a3w-attr-cf7" class="a3w-attr-copy" value="" />
                    </div>
                    <button role="button" id="a3w-attr-copy" class="close-a3w-generator a3w-attr-copy button"><?php echo __( "Copy and close", 'a3w-cf7-fields-attributes' ); ?></button>
                </div>
                <div class="panel-footer">
                    <div class="description">
                        <h5><?php echo __( "Instructions", 'a3w-cf7-fields-attributes' ); ?></h5>
                        <p><?php echo __( "Copy the generated custom attribute inside the field shortcode as in this example: <br>Attribute's name: data-message, Attribute's content: Hello World<br>Generated custom attribute: attr_data-message:Hello%20World <br>CF7 field shortcode: [text field_name attr_data-message:Hello%20World ...]", 'a3w-cf7-fields-attributes' ); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

A3W_CF7_Fields_Attributes::start();

endif;
