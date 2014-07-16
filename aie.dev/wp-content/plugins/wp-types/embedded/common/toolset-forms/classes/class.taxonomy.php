<?php

/**
 *
 * $HeadURL: https://www.onthegosystems.com/misc_svn/common/tags/Types1.6b3-CRED1.3b3/toolset-forms/classes/class.taxonomy.php $
 * $LastChangedDate: 2014-06-12 16:01:42 +0000 (Thu, 12 Jun 2014) $
 * $LastChangedRevision: 23567 $
 * $LastChangedBy: marcin $
 *
 */

require_once 'class.textfield.php';

class WPToolset_Field_Taxonomy extends WPToolset_Field_Textfield
{
    public $values = "";
    public $objValues;

    public function init() {
        $this->objValues = array();

        $terms = wp_get_post_terms(CredForm::$current_postid, $this->getName(), array("fields" => "all"));
        $i = 0;
        foreach ($terms as $n => $term) {
            $this->values .= ($i==0) ? $term->slug : ",".$term->slug;
            $this->objValues[$term->slug] = $term;
            $i++;
        }

        wp_register_style('wptoolset-taxonomy',
                WPTOOLSET_FORMS_RELPATH.'/css/taxonomy.css');

        wp_register_script( 'wptoolset-jquery-autocompleter',
                WPTOOLSET_FORMS_RELPATH . '/js/jquery.autocomplete.js',
                array('jquery'), WPTOOLSET_FORMS_VERSION );

        wp_register_style('wptoolset-autocompleter', WPTOOLSET_FORMS_RELPATH.'/css/autocompleter.css');

        add_action( 'wp_footer', array($this, 'javascript_autocompleter') );
    }

    public function enqueueScripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('wptoolset-jquery-autocompleter');
    }

    public function enqueueStyles() {
        wp_enqueue_style('wptoolset-taxonomy');
        wp_enqueue_style('wptoolset-autocompleter');
        wp_print_styles();
    }

    public function javascript_autocompleter() {
            $autosubmit = 'function onSelectItem(row)
                           {
                                jQuery("input#'.$this->getName().'").focus();
                           }';
            $extra = '
                    function formatItem(row) {
                            return row[0];
                    }
                    function formatItem2(row) {
                        if(row.length == 3){
                            var attr = "attr=\"" + row[2] + "\"";
                        } else {
                            attr = "";
                        }
                        return "<span "+attr+">" + row[1] + " matches</span>" + row[0];
                    }';
            $results = 1;
            echo '<script type="text/javascript">
                    jQuery(document).ready(function() {
                            initTaxonomies("'. $this->values .'", "'.$this->getName().'", "'.WPTOOLSET_FORMS_RELPATH.'", "'.$this->_nameField.'");
                    });
                    '.$autosubmit.'
                    '.$extra.'
            </script>';
    }

    public function metaform()
    {
        $use_bootstrap = array_key_exists( 'use_bootstrap', $this->_data ) && $this->_data['use_bootstrap'];
        $metaform = array();
        $metaform[] = array(
            '#type' => 'hidden',
            '#title' => '',
            '#description' => '',
            '#name' => $this->getName(),
            '#value' => $this->values,
            '#attributes' => array(
                'style' => 'float:left'
            ),
            '#validate' => $this->getValidationData(),
        );
        $metaform[] = array(
            '#type' => 'textfield',
            '#title' => '',
            '#description' => '',
            '#name' => "tmp_".$this->getName(),
            '#value' => '',
            '#attributes' => array(
                'style' => 'float:left'
            ),
            '#validate' => $this->getValidationData(),
            '#before' => $use_bootstrap? '<div class="form-group">':'',
            '#class' => $use_bootstrap? 'inline':'',
        );

        /**
         * add button
         */
        $metaform[] = array(
            '#type' => 'button',
            '#title' => '',
            '#description' => '',
            '#name' => "btn_".$this->getName(),
            '#value' => 'add',
            '#attributes' => array(
                'style' => 'float:left',
                'onclick' => 'setTaxonomy(\''.$this->getName().'\', this)'
            ),

            '#validate' => $this->getValidationData(),
            '#class' => $use_bootstrap? 'btn btn-default':'',
            '#after' => $use_bootstrap? '</div>':'',
        );

        /**
         * show popular button
         */
        $before = sprintf(
            '<div style="clear:both;"></div><div class="tagchecklist-%s"><span><a class="ntdelbutton" id="post_tag-check-num-0" onclick="del(this);">X</a>&nbsp;test</span></div><div style="clear:both;"></div>',
            $this->getName()
        );
        $after = '<div style="clear:both;"></div>'.$this->getMostPopularTerms().'<div style="clear:both;"></div>';
        if ( $use_bootstrap ) {
            $before = '<div class="form-group">'.$before;
            $after .= '</div>';
        }
        $metaform[] = array(
            '#type' => 'button',
            '#title' => '',
            '#description' => '',
            '#name' => "sh_".$this->getName(),
            '#value' => 'show popular',
            '#attributes' => array(
                'class' => 'popular',
                'onclick' => 'showHideMostPopularTaxonomy(this)',
                'data-taxonomy' => $this->getName(),
            ),
            '#before' => $before,
            '#after' => $after,
        );

        $this->set_metaform($metaform);
        return $metaform;
    }

    private function buildTerms($obj_terms) {
        $tax_terms=array();
        foreach ($obj_terms as $term)
        {
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

    private function buildCheckboxes($index, &$childs, &$names)
    {
        if (isset($childs[$index]))
        {
            foreach ($childs[$index] as $tid)
            {
                $name = $names[$tid];
                ?>
                <div style='position:relative;line-height:0.9em;margin:2px 0;<?php if ($tid!=0) echo 'margin-left:15px'; ?>' class='myzebra-taxonomy-hierarchical-checkbox'>
                    <label class='myzebra-style-label'><input type='checkbox' name='<?php echo $name; ?>' value='<?php echo $tid; ?>' <?php if (isset($values[$tid])) echo 'checked="checked"'; ?> /><span class="myzebra-checkbox-replace"></span>
                        <span class='myzebra-checkbox-label-span' style='position:relative;font-size:12px;display:inline-block;margin:0;padding:0;margin-left:15px'><?php echo $names[$tid]; ?></span></label>
                    <?php
                    if (isset($childs[$tid]))
                        echo $this->buildCheckboxes($tid,$childs,$names);
                    ?>
                </div>
            <?php
            }
        }
    }

    public function getMostPopularTerms()
    {
        $term_args = array(
            'number' => 10,
            'orderby' => 'count',
            'order' => 'DESC'
        );
        $terms = get_terms(array($this->getName()), $term_args);
        if ( empty( $terms ) ) {
            return '';
        }
        $max = -1;
        $min = PHP_INT_MAX;
        foreach($terms as $term) {
            if ( $term->count < $min ) {
                $min = $term->count;
            }
            if ( $term->count > $max ) {
                $max = $term->count;
            }
        }
        $add_sizes = $max > $min;
        $content = sprintf(
            '<div class="shmpt-%s" style="margin:5px;float:left;width:250px;display:none;">',
            $this->getName()
        );

        $style = '';
        foreach($terms as $term) {
            if ( $add_sizes ) {
                $font_size = ( ( $term->count - $min ) * 10 ) / ( $max - $min ) + 5;
                $style = sprintf( ' style="font-size:1.%dem;"', $font_size );
            }
            $content .= sprintf(
                '<a href="#" onclick="addTaxonomy(\'%s\', \'%s\', this);return false;" onkeypress="this.onclick" %s>%s</a> ',
                $term->slug,
                $this->getName(),
                $style,
                $term->name
            );
        }
        $content .= "</div>";
        return $content;
    }

}
