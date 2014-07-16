<?php

define ( 'WPDDL_CSS_STYLING_LINK', 'http://wp-types.com/documentation/user-guides/using-html-css-style-layout-cells' );
define ( 'WPDLL_LEARN_ABOUT_FIXED_AND_FLUID', 'http://wp-types.com/documentation/user-guides/learn-fluid-fixed-width-layouts' );
define ( 'WPDLL_LEARN_ABOUT_SETTING_UP_TEMPLATE', 'http://wp-types.com/documentation/user-guides/adding-layout-support-theme-templates' );
define ( 'WPDLL_LEARN_ABOUT_ROW_MODES', 'http://wp-types.com/documentation/user-guides/learn-how-rows-can-displayed-different-ways' );
define ( 'WPDLL_LEARN_ABOUT_GRIDS', 'http://wp-types.com/documentation/user-guides/learn-creating-using-grids' );
define ( 'WPDLL_RICH_CONTENT_CELL', 'http://wp-types.com/documentation/user-guides/rich-content-cell-text-images-html' );
define ( 'WPDLL_POST_CONTENT_CELL', 'http://wp-types.com/documentation/user-guides/post-content-cell/' );
define ( 'WPDLL_WIDGET_AREA_CELL', 'http://wp-types.com/documentation/user-guides/widget-area-cell' );
define ( 'WPDLL_WIDGET_CELL', 'http://wp-types.com/documentation/user-guides/widget-cell' );
define ( 'WPDLL_CHILD_LAYOUT_CELL', 'http://wp-types.com/documentation/user-guides/hierarchical-layouts/' );
define ( 'WPDLL_THEME_INTEGRATION_QUICK', 'http://wp-types.com/documentation/user-guides/layouts-theme-integration-quick-start-guide' );

function ddl_add_help_link_to_dialog($link, $text) {
    ?>
        <p>
            <a href="<?php echo $link; ?>" target="_blank">
                <?php echo $text; ?> &raquo;
            </a>
        </p>
    <?php
}

