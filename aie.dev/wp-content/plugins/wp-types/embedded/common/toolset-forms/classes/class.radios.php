<?php
/**
 *
 * $HeadURL: https://www.onthegosystems.com/misc_svn/common/tags/Types1.6b3-CRED1.3b3/toolset-forms/classes/class.radios.php $
 * $LastChangedDate: 2014-06-11 13:49:47 +0000 (Wed, 11 Jun 2014) $
 * $LastChangedRevision: 23494 $
 * $LastChangedBy: marcin $
 *
 */
require_once 'class.field_factory.php';

/**
 * Description of class
 *
 * @author Srdjan
 */
class WPToolset_Field_Radios extends FieldFactory
{

    public function metaform()
    {
        $value = $this->getValue();
        $data = $this->getData();
        $form = array();
        $options = array();
        foreach ( $data['options'] as $option ) {
            $one_option_data = array(
                '#value' => $option['value'],
                '#title' => $option['title'],
                '#validate' => $this->getValidationData(),
                '#after' => '<br />',
            );
            /**
             * add default value if needed
             * issue: frontend, multiforms CRED
             */
            if ( array_key_exists( 'types-value', $option ) ) {
                $one_option_data['#types-value'] = $option['types-value'];
            }
            /**
             * add to options array
             */
            $options[] = $one_option_data;
        }
        if ( !empty( $value ) || $value == '0' ) {
            $data['default_value'] = $value;
        }
        $form[] = array(
            '#type' => 'radios',
            '#title' => $this->getTitle(),
            '#description' => $this->getDescription(),
            '#name' => $this->getName(),
            '#options' => $options,
            '#default_value' => isset( $data['default_value'] ) ? $data['default_value'] : false,
            '#repetitive' => $this->isRepetitive(),
        );

        return $form;
    }

}
