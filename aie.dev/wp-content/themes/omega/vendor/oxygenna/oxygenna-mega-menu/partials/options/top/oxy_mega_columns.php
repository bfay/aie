<?php
/**
 * Column menu options
 *
 * @package Omega
 * @subpackage Admin
 *
 * @copyright (c) 2014 Oxygenna.com
 * @license **LICENSE**
 * @version 1.5.1
 * @author Oxygenna.com
 */
require_once OXY_TF_DIR . 'inc/options/fields/select/OxygennaSelect.php';
// create options and value for icon select
$widget_option = array(
    'name'    => 'Widget',
    'desc'    => 'Widget',
    'id'      => 'Widget',
    'type'    => 'select',
    'options' => array(
        'on' => __('On', 'omega-admin-td'),
        ''   => __('Off', 'omega-admin-td'),
    ),
    'default' => ''
);
$widget_select_value = isset($item->oxy_widget) ? esc_attr($item->oxy_widget) : '';
$widget_select = new OxygennaSelect($widget_option, $widget_select_value, array(
    'id' => 'edit-menu-item-widget-' . $item_id,
    'name' => 'menu-item-oxy_widget[' . $item_id . ']',
    'class' => 'widefat edit-menu-item-widget',
));
?>
<p class="field-widget oxy-widget description-wide">
    <label for="edit-menu-item-oxy-widget-<?php echo $item_id; ?>">
        <?php _e('Use Column As Widget', 'omega-admin-td'); ?><br />
        <?php $widget_select->render(); ?>
        <span class="description"><?php _e('This will set this column up to be used as a widget position.', 'omega-admin-td'); ?></span>
    </label>
</p>
