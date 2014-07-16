<?php
/**
 *
 * $HeadURL: https://www.onthegosystems.com/misc_svn/common/tags/Types1.6b3-CRED1.3b3/toolset-forms/classes/class.textarea.php $
 * $LastChangedDate: 2014-06-03 17:25:39 +0000 (Tue, 03 Jun 2014) $
 * $LastChangedRevision: 23085 $
 * $LastChangedBy: marcin $
 *
 */
require_once 'class.field_factory.php';

/**
 * Description of class
 *
 * @author Franko
 */
class WPToolset_Field_Textarea extends FieldFactory
{

    public function metaform() {
        $metaform = array();
        $metaform[] = array(
            '#type' => 'textarea',
            '#title' => $this->getTitle(),
            '#description' => $this->getDescription(),
            '#name' => $this->getName(),
            '#value' => $this->getValue(),
            '#validate' => $this->getValidationData(),
            '#repetitive' => $this->isRepetitive(),
        );
        return $metaform;
    }

}
