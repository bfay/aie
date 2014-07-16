<?php
/**
 *
 * the_ddlayout
 *
 * Renders and echos the layout.
 *
 */

function the_ddlayout($layout = '') {
	echo get_the_ddlayout($layout);
}

/**
 * get_the_ddlayout
 *
 * Gets the layout
 *
 */

function get_the_ddlayout($layout = '') {
	global $wpddlayout, $wpdb, $post;


	$id = 0;
	$content = '';

	$template = basename( get_page_template() );
	$wpddlayout->save_option(array('templates' => array($template => $layout)));

	if ($layout) {

		$id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type='dd_layouts' AND post_name=%s", $layout));

		if (!$id) {
			// try the id.
			$id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type='dd_layouts' AND ID=%d", (int)$layout));
		}

		if (!$id) {
			// try the post title
			$id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type='dd_layouts' AND post_title=%s", $layout));
		}
	}

	// Check for layout selection for post.
	$post_id = $post->ID;

	$layout_selected = get_post_meta( $post_id, '_layouts_template', true );

	if ($layout_selected) {

		$id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type='dd_layouts' AND post_name=%s", $layout_selected));

		$option = $wpddlayout->post_types_manager->get_layout_to_type_object($post->post_type);

		if( is_object( $option ) && property_exists( $option, 'layout_id') && (int) $option->layout_id === (int) $id )
		{
			$id = $option->layout_id;
		}
	}


	if ($id) {

		// Check for preview mode
		$old_id = $id;
		if (isset($_GET['layout_id'])) {
			$id = $_GET['layout_id'];
		}

		$layout = $wpddlayout->get_layout_from_id($id);
		if (!$layout && isset($_GET['layout_id'])) {
			if ($id != $old_id) {
				$layout = $wpddlayout->get_layout_from_id($old_id);
			}
		}
		if ($layout) {
			$manager = new WPDD_layout_render_manager($layout);
			$renderer = $manager->get_renderer( );
			//$renderer = new WPDD_layout_render($layout);
			$content = $renderer->render_to_html();

			$content = do_shortcode( $content );

			$render_errors = $wpddlayout->get_render_errors();
			if (sizeof($render_errors)) {
				$content .= '<p class="alert alert-error"><strong>' . __('There were errors while rendering this layout.', 'ddl-layouts') . '</strong></p>';
				foreach($render_errors as $error) {
					$content .= '<p class="alert alert-error">' . $error . '</p>';
				}
			}
		}
	} else {
		if (!$layout) {
			$content = '<p>' . __('You need to select a layout for this page. The layout selection is available in the page editor.', 'ddl-layouts') . '</p>';
		}
	}

	return $content;


}

/**
 * @return bool
 * to be used in template files or with template redirect hook to check whether current page has a layout template
 */
function is_ddlayout_template( )
{
	global $wpddlayout;

	$temp = get_page_template();

	$pos = strrpos ( $temp , '/' );

	$template = substr ($temp , $pos+1 );

	return in_array( $template, $wpddlayout->templates_have_layout( array( $template => 'name') ) );
}

/**
 * generic version of the preceeding
 * @return bool
 */
function has_current_post_ddlayout_template( )
{
	global $template, $wpddlayout;
	$template = basename($template);
	return in_array( $template, $wpddlayout->templates_have_layout( array( $template => 'name') ) );
}

function is_ddlayout_assigned()
{
	global $post;

	$assigned_template = get_post_meta($post->ID, '_layouts_template', true);

	if( !$assigned_template ) return false;

	return $assigned_template !== 'none';
}

function ddlayout_set_framework ( $framework ) {
	$framework_manager = WPDD_Layouts_CSSFrameworkOptions::getInstance();
	
	$framework_manager->theme_set_framework( $framework );
}
