<?php
/**
 *
 * $HeadURL: https://www.onthegosystems.com/misc_svn/common/tags/Types1.6b3-CRED1.3b3/toolset-forms/classes/class.textfield.php $
 * $LastChangedDate: 2014-06-10 12:58:55 +0000 (Tue, 10 Jun 2014) $
 * $LastChangedRevision: 23430 $
 * $LastChangedBy: francesco $
 *
 */
require_once "class.field_factory.php";
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class
 *
 * @author Franko
 */
class WPToolset_Field_Textfield extends FieldFactory
{

    public function metaform() {
        $metaform = array();
        $metaform[] = array(
            '#type' => 'textfield',
            '#title' => $this->getTitle(),
            '#description' => $this->getDescription(),
            '#name' => $this->getName(),
            '#value' => $this->getValue(),
            '#validate' => $this->getValidationData(),
            '#repetitive' => $this->isRepetitive(),
            '#attributes' => $this->getAttr()
        );
        return $metaform;
    }

}
