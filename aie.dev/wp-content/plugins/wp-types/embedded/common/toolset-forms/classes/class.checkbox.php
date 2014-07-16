<?php
/**
 *
 * $HeadURL: https://www.onthegosystems.com/misc_svn/common/tags/Types1.6b3-CRED1.3b3/toolset-forms/classes/class.checkbox.php $
 * $LastChangedDate: 2014-06-05 12:29:27 +0000 (Thu, 05 Jun 2014) $
 * $LastChangedRevision: 23216 $
 * $LastChangedBy: francesco $
 *
 */
require_once 'class.field_factory.php';

/**
 * Description of class
 *
 * @author Srdjan
 */
class WPToolset_Field_Checkbox extends FieldFactory
{
    public function metaform()
    {
        global $post;

        $value = $this->getValue();
        $data = $this->getData();

        /**
         * turn off autocheck for saved posts
         */
        if (isset($post) && 'auto-draft' != $post->post_status && empty( $data['value'] )) {
            $data['checked'] = false;
        }
        
        $form = array();
        $form[] = array(
            '#type' => 'checkbox',
            '#value' => $value,
            '#default_value' => array_key_exists( 'default_value', $data )? $data['default_value']:null,
            '#name' => $this->getName(),
            '#title' => $this->getTitle(),
            '#validate' => $this->getValidationData(),
            '#after' => '<input type="hidden" name="_wptoolset_checkbox[' . $this->getId() . ']" value="1" />',
            '#checked' => isset($data['options']) ? (array_key_exists( 'checked', $data['options'] ) ? $data['options']['checked']:null) : null,
            '#repetitive' => $this->isRepetitive(),
        );
        return $form;
    }
}
