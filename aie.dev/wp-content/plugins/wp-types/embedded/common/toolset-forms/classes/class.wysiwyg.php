<?php
require_once 'class.textarea.php';

/**
 * Description of class
 *
 * @author Srdjan
 *
 * $HeadURL: https://www.onthegosystems.com/misc_svn/common/tags/Types1.6b3-CRED1.3b3/toolset-forms/classes/class.wysiwyg.php $
 * $LastChangedDate: 2014-05-30 16:03:40 +0000 (Fri, 30 May 2014) $
 * $LastChangedRevision: 22979 $
 * $LastChangedBy: marcin $
 *
 */
class WPToolset_Field_Wysiwyg extends WPToolset_Field_Textarea
{

    protected $_settings = array('min_wp_version' => '3.3');

    public function metaform()
    {
        $form = array();
        $markup = '';
        if ( is_admin() ) {
            $markup .= '<div class="form-item form-item-markup">';
            $markup .= sprintf( '<label class="wpt-form-label wpt-form-textfield-label">%s</label>', $this->getTitle() );
        }
        $markup .= $this->getDescription();
        $markup .= $this->_editor();
        if ( is_admin() ) {
            $markup .= '</div>';
        }
        $form[] = array(
            '#type' => 'markup',
            '#markup' => $markup,
        );
        return $form;
    }

    protected function _editor()
    {
        ob_start();
        wp_editor( $this->getValue(), $this->getId(),
            array(
                'wpautop' => true, // use wpautop?
                'media_buttons' => $this->_data['has_media_button'], // show insert/upload button(s)
                'textarea_name' => $this->getName(), // set the textarea name to something different, square brackets [] can be used here
                'textarea_rows' => get_option( 'default_post_edit_rows', 10 ), // rows="..."
                'tabindex' => '',
                'editor_css' => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
                'editor_class' => 'wpt-wysiwyg', // add extra class(es) to the editor textarea
                'teeny' => false, // output the minimal editor config used in Press This
                'dfw' => false, // replace the default fullscreen with DFW (needs specific DOM elements and css)
                'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
                'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
            ) );
        return ob_get_clean() . "\n\n";
    }

}
