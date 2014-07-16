<?php
/**
 *
 * $HeadURL: https://www.onthegosystems.com/misc_svn/common/tags/Types1.6b3-CRED1.3b3/toolset-forms/classes/class.taxonomyhierarchical.php $
 * $LastChangedDate: 2014-06-10 14:23:32 +0000 (Tue, 10 Jun 2014) $
 * $LastChangedRevision: 23446 $
 * $LastChangedBy: francesco $
 *
 */

include_once 'class.textfield.php';

class WPToolset_Field_Taxonomyhierarchical extends WPToolset_Field_Textfield
{
    protected $child;
    protected $names;
    protected $values = array();
    protected $valuesId = array();
    protected $objValues;

    public function init()
    {
        global $post;

        $this->objValues = array();
        if (isset($post)) {
            $terms = wp_get_post_terms($post->ID, $this->getName(), array("fields" => "all"));
            foreach ($terms as $n => $term) {
                $this->values[] = $term->slug;
                $this->valuesId[] = $term->term_id;
                $this->objValues[$term->slug] = $term;
            }
        }

        $all = $this->buildTerms(get_terms($this->getName(),array('hide_empty'=>0,'fields'=>'all')));

        $childs=array();
        $names=array();
        foreach ($all as $term) {
            $names[$term['term_id']]=$term['name'];
            if (!isset($childs[$term['parent']]) || !is_array($childs[$term['parent']]))
                $childs[$term['parent']]=array();
            $childs[$term['parent']][]=$term['term_id'];
        }

        $this->childs = $childs;
        $this->names = $names;
    }

    public function enqueueScripts()
    {
    }

    public function enqueueStyles()
    {
    }

    public function metaform()
    {
        $res = '';
        $metaform = array();
        if ( array_key_exists( 'display', $this->_data ) && 'select' == $this->_data['display'] ) {
            return $this->buildSelect();
        } else {
            $res = $this->buildCheckboxes(0, $this->childs, $this->names, $metaform);
            $this->set_metaform($res);
            return $metaform;
        }
    }

    private function buildTerms($obj_terms)
    {
        $tax_terms=array();
        foreach ($obj_terms as $term) {
            $tax_terms[]=array(
                'name'=>$term->name,
                'count'=>$term->count,
                'parent'=>$term->parent,
                'term_taxonomy_id'=>$term->term_taxonomy_id,
                'term_id'=>$term->term_id
            );
        }
        return $tax_terms;
    }

    private function buildSelect()
    {
        $curr_options = $this->getOptions();
        $options = array();
        foreach ($curr_options as $name=>$value) {
            $one_option_data = array(
                '#value' => $name,
                '#title' => $value,
            );
            $options[] = $one_option_data;
        }
        $form = array();
        $form[] = array(
            '#type' => 'select',
            '#title' => $this->getTitle(),
            '#description' => $this->getDescription(),
            '#name' => $this->getName(),
            '#options' => $options,
            '#default_value' => isset( $data['default_value'] ) ? $data['default_value'] : null,
            '#validate' => $this->getValidationData(),
            '#class' => 'form-inline',
            '#repetitive' => $this->isRepetitive(),
        );
        return $form;
    }

    private function getOptions($index = 0, $level = 0)
    {
        if ( !isset($this->childs[$index]) || empty( $this->childs[$index] ) ) {
            return;
        }
        $options = array();

        foreach( $this->childs[$index] as $one ) {
            $options[$one] = sprintf(
                '%s%s',
                str_repeat('&nbsp;', 2*$level ),
                $this->names[$one]
            );
            if ( isset($this->childs[$one]) && count($this->childs[$one])) {
                foreach( $this->getOptions( $one, $level + 1 ) as $id => $value ) {
                    $options[$id] = $value;
                }
            }
        }
        return $options;
    }

    private function buildCheckboxes($index, &$childs, &$names, &$metaform, $ischild=false)
    {
        if (isset($childs[$index])) {
            foreach ($childs[$index] as $tid) {
                $name = $names[$tid];
                $metaform[] = array(
                            '#type' => 'checkbox',
                            '#title' => $names[$tid],
                            '#description' => '',
                            '#name' => $this->getName()."[]",
                            '#value' => $tid,
                            '#default_value' => in_array($tid, $this->valuesId),
                            '#validate' => $this->getValidationData(),
                            '#after' => '<br />',
                        );

                if (isset($childs[$tid])) {
                    $metaform = $this->buildCheckboxes($tid,$childs,$names, $metaform, true);
                }

            }
        }
        return $metaform;
    }
}
