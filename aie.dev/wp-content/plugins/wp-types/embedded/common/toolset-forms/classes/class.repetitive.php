<?php
/*
 * Repetitive controller
 *
 * $HeadURL: https://www.onthegosystems.com/misc_svn/common/tags/Types1.6b3-CRED1.3b3/toolset-forms/classes/class.repetitive.php $
 * $LastChangedDate: 2014-06-03 17:25:39 +0000 (Tue, 03 Jun 2014) $
 * $LastChangedRevision: 23085 $
 * $LastChangedBy: marcin $
 *
 * If field is repetitive
 * - queues repetitive CSS and JS
 * - renders JS templates in admin footer
 */
class WPToolset_Forms_Repetitive
{
    private $__templates = array();

    function __construct(){
        // Register
        wp_register_script( 'wptoolset-forms-repetitive',
                WPTOOLSET_FORMS_RELPATH . '/js/repetitive.js',
                array('jquery', 'jquery-ui-sortable'), WPTOOLSET_FORMS_VERSION,
                false );
//        wp_register_style( 'wptoolset-forms-repetitive', '' );
        // Render settings
        add_action( 'admin_footer', array($this, 'renderTemplates') );
        add_action( 'wp_footer', array($this, 'renderTemplates') );

        wp_enqueue_script( 'wptoolset-forms-repetitive' );
        wp_enqueue_script( 'underscore' );
    }

    function add( $config, $html ) {
        if ( !empty( $config['repetitive'] ) ) {
            $this->__templates[$config['id']] = $html;
        }
    }

    function renderTemplates() {
        foreach ( $this->__templates as $id => $template ) {
            echo '<script type="text/html" id="tpl-wpt-field-' . $id . '">'
            . $template . '</script>';
        }
    }
}