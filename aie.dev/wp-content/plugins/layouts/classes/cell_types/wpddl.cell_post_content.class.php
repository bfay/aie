<?php

class WPDD_layout_cell_post_content extends WPDD_layout_cell {

	function __construct($name, $width, $css_class_name = '', $content = null, $css_id = '') {
		parent::__construct($name, $width, $css_class_name, 'cell-post-content-template', $content, $css_id);

		$this->set_cell_type('cell-post-content');
	}

	function frontend_render_cell_content($target) {
		global $WPV_templates;
		$cell_content = $this->get_content();
		if (isset($WPV_templates) && isset($cell_content['view_template']) && $cell_content['view_template'] != 'None') {
			$content_template_id = $WPV_templates->get_template_id( $cell_content['view_template'] );
			if ($cell_content['page'] == 'current_page') {
				global $post;
				$content = render_view_template($content_template_id, $post );
			} elseif ($cell_content['page'] == 'this_page') {
				$post = get_post( $cell_content['selected_post'] );
				$content = render_view_template($content_template_id, $post );
			}
		} else {

			global $post;
			$content = '';
			if ($cell_content['page'] == 'current_page') {
				$content = apply_filters('the_content', $post->post_content);
			} elseif ($cell_content['page'] == 'this_page') {
				$other_post = get_post($cell_content['selected_post']);
				if (isset($other_post->post_content)) {
					$content = apply_filters('the_content', $other_post->post_content);
				}
			}
		}
		$target->cell_content_callback($content);
	}

}

class WPDD_layout_cell_post_content_factory extends WPDD_layout_cell_factory{

	function __construct() {
		if( is_admin()){
			add_action('wp_ajax_get_posts_for_post_content', array($this, 'get_posts_for_post_content_callback') );
			add_action('wp_ajax_dll_refresh_ct_list', array($this, 'get_ct_select_box'));
			add_action('wp_ajax_ddl_content_template_preview', array($this, 'get_content_template'));
		}

	}

	public function build($name, $width, $css_class_name = '', $content = null, $css_id, $tag) {
		return new WPDD_layout_cell_post_content($name, $width, $css_class_name, $content, $css_id, $tag);
	}

	public function get_cell_info($template) {
		$template['icon-css'] = 'icon-file-text';
		$template['preview-image-url'] = WPDDL_RES_RELPATH . '/images/post-content.png';
		$template['name'] = __('Post content', 'ddl-layouts');
		$template['description'] = __('Displays the post content.', 'ddl-layouts');
		$template['button-text'] = __('Assign Post content Box', 'ddl-layouts');
		$template['dialog-title-create'] = __('Create a new Post content Cell', 'ddl-layouts');
		$template['dialog-title-edit'] = __('Edit Post content Cell', 'ddl-layouts');
		$template['dialog-template'] = $this->_dialog_template();
		return $template;
	}

	public function get_editor_cell_template() {
		ob_start();
		?>
			<div class="cell-content">
				<p class="cell-name"><%- name %></p>
				<% if( content ) { %>
				<div class="cell-preview">
					<%
					var preview = DDL_Helper.sanitizeHelper.stringToDom( DDLayout.post_content_cell.display_post_content_info(content, '<?php _e('Loading...', 'ddl-layouts'); ?>') );
					print( preview.innerHTML );
					%>
				</div>
			<% } %>
			</div>
		<?php
		return ob_get_clean();
	}

	private function _dialog_template() {
		global $WPV_templates, $WP_Views;

		$views_1_6_available = defined('WPV_VERSION') && WPV_VERSION >= 1.6 && isset($WP_Views) && class_exists('WP_Views') && !$WP_Views->is_embedded();
		$view_tempates_available = $this->_get_view_templates_available();

		ob_start();
		?>
		<script type="text/javascript">
			var ddl_new_ct_default_name = '<?php echo __('Post content for %s Layout', 'ddl-layouts'); ?>';
		</script>

		<ul class="ddl-form">
			<li>
				<fieldset>
					<legend><?php _e('Display content for:', 'ddl-layouts'); ?></legend>
					<div class="fields-group">
						<ul>
							<li>
								<label class="post-content-page">
									<input type="radio" name="<?php the_ddl_name_attr('page'); ?>" value="current_page" checked="checked"/>
									<?php _e('Current page', 'ddl-layouts'); ?>
								</label>
							</li>
							<li>
								<label class="post-content-page">
									<input type="radio" name="<?php the_ddl_name_attr( 'page' ); ?>" value="this_page" />
									<?php _e( 'A Specific page:', 'ddl-layouts' ); ?>
								</label>
							</li>
							<li id="js-post-content-specific-page">
								<select name="<?php the_ddl_name_attr( 'post_content_post_type' ); ?>" class="js-ddl-post-content-post-type" data-nonce="<?php echo wp_create_nonce( 'ddl-post-content-post-type-select' ); ?>">
									<option value="ddl-all-post-types"><?php _e('All post types', 'ddl-layouts'); ?></option>
									<?php
									$post_types = get_post_types( array( 'public' => true ), 'objects' );
									foreach ( $post_types as $post_type ) {
										$count_posts = wp_count_posts($post_type->name);
										if ($count_posts->publish > 0) {
											?>
												<option value="<?php echo $post_type->name; ?>"<?php if($post_type->name == 'page') { echo ' selected="selected"';} ?>>
													<?php echo $post_type->labels->singular_name; ?>
												</option>
											<?php
										}
									}
									?>
								</select>
								<?php
									$keys = array_keys( $post_types );
									$post_types_array = array_shift(  $keys  );
									$this->show_posts_dropdown( $post_types_array, get_ddl_name_attr( 'selected_post' ) );
								?>
							</li>
						</ul>
					</div>
				</fieldset>
			</li>
			<?php if ( $views_1_6_available || sizeof($view_tempates_available) > 0): ?>
				<li>
					<fieldset>
						<legend><?php _e('How to display:', 'ddl-layouts'); ?></legend>
						<div class="fields-group">
							<ul>
								<li>
									<label class="post-content-page">
										<input type="radio" name="use-ct" value="no" checked="checked"/>
										<?php _e('Display only the post content', 'ddl-layouts'); ?>
									</label>
								</li>
								<li>
									<label class="post-content-page">
										<input type="radio" name="use-ct" value="yes" />
										<?php _e( 'Display the cell content using post fields', 'ddl-layouts' ); ?>
									</label>
								</li>
							</ul>
						</div>
					</fieldset>
				</li>
			<?php endif; ?>

			<?php if ($views_1_6_available || sizeof($view_tempates_available) > 0): ?>
			<li>
				<fieldset>
					<div class="fields-group">
						<ul>
							<li class="js-post-content-ct js-ct-selector js-ct-select-box">
								<?php echo $this->_get_view_template_select_box($view_tempates_available); ?>
							</li>
							<?php if ($views_1_6_available): ?>
								<li class="js-post-content-ct js-ct-selector">
									<?php _e('or', 'ddl-layouts'); ?> <a href="#" class="js-create-new-ct"><?php _e('Create a new one', 'ddl-layout'); ?></a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</fieldset>
			</li>

			<?php endif; ?>


			<?php if( $views_1_6_available ): ?>
				<li class="js-post-content-ct js-ct-edit">
					<input class="js-ct-edit-name" type="text" style="float: left; width: 50%" /><span class="js-ct-editing"><strong><?php _e('Editing', 'ddl-layouts'); ?> :</strong> <span class="js-ct-name"></span></span> <a style="float: right" href="#" class="js-load-different-ct"><?php _e('Load a different Content Template', 'ddl-layouts'); ?></a>
				</li>
				<li class="js-post-content-ct js-ct-edit">
			        <div class="js-wpv-ct-inline-edit wpv-ct-inline-edit wpv-ct-inline-edit hidden"></div>
				</li>
			<?php else: ?>
				<div class="toolset-alert">
					<p>
						<?php _e('This cell can display the post content using fields. Install and activate Views 1.6 or greater and you will be able to create Content Templates to display post fields.', 'ddl-layouts'); ?>
						<br>
						<a class="fieldset-inputs" href="http://wp-types.com/home/views-create-elegant-displays-for-your-content/" target="_blank">
							<?php _e('Get Views plugin', 'ddl-layouts');?> &raquo;
						</a>
					</p>
				</div>
			<?php endif; ?>

			

		</ul>
		<?php ddl_add_help_link_to_dialog(WPDLL_POST_CONTENT_CELL, __('Learn about the Post Content cell', 'ddl-layouts')); ?>
		<?php wp_nonce_field( 'wpv-ct-inline-edit', 'wpv-ct-inline-edit' ); ?>

		<?php
		return ob_get_clean();
	}

	private function _get_view_templates_available() {
		global $wpdb;

		return $wpdb->get_results("SELECT ID, post_title, post_name FROM {$wpdb->posts} WHERE post_type='view-template' AND post_status in ('publish')");
	}

	private function _get_view_template_select_box($view_tempates_available) {

		// Add a "None" type to the list.
		$none = new stdClass();
		$none->ID = 0;
		$none->post_name = 'None';
		$none->post_title = __('None', 'ddl-layouts');
		array_unshift($view_tempates_available, $none);

		ob_start();
		?>
		<label for="post-content-view-template"><?php _e('Choose an existing Content Template:', 'ddl-layouts'); ?> </label>
		<select class="views_template_select" name="<?php echo $this->element_name('view_template'); ?>" id="post-content-view-template">';

		<?php
		foreach($view_tempates_available as $template) {
			$title = $template->post_title;
			if (!$title) {
				$title = $template->post_name;
			}

			?>
			<option value="<?php echo $template->post_name; ?>" data-ct-id="<?php echo $template->ID; ?>" ><?php echo $template->post_title; ?></option>
			<?php
		}
		?>
		</select>

		<?php

		return ob_get_clean();
	}

	public function enqueue_editor_scripts() {
		wp_register_script( 'wp-post-content-editor', ( WPDDL_GUI_RELPATH . "editor/js/post-content-cell.js" ), array('jquery'), null, true );
		wp_enqueue_script( 'wp-post-content-editor' );

		wp_localize_script('wp-post-content-editor', 'DDLayout_post_content_strings', array(
				'current_post' => __('This cell will display the content of the post which uses the layout.', 'ddl-layouts'),
				'this_post' => __('This cell will display the content of a specific post.', 'ddl-layouts'),
				)
		);

	}

	private function show_posts_dropdown($post_type, $name, $selected = 0) {
		if ($post_type == 'ddl-all-post-types') {
			$post_type = 'any';
		}

		$attr = array('name'=> $name,
					  'post_type' => $post_type,
					  'show_option_none' => __('None', 'ddl-layouts'),
					  'selected' => $selected);


		add_filter('posts_clauses_request', array($this, 'posts_clauses_request_filter'), 10, 2 );

		$defaults = array(
			'depth' => 0, 'child_of' => 0,
			'selected' => $selected, 'echo' => 1,
			'name' => 'page_id', 'id' => '',
			'show_option_none' => '', 'show_option_no_change' => '',
			'option_none_value' => ''
		);
		$r = wp_parse_args( $attr, $defaults );
		extract( $r, EXTR_SKIP );

		$pages = get_posts(array('numberposts' => -1, 'post_type' => $post_type, 'suppress_filters' => false));
		$output = '';
		// Back-compat with old system where both id and name were based on $name argument
		if ( empty($id) )
			$id = $name;

		if ( ! empty($pages) ) {
			$output = "<select name='" . esc_attr( $name ) . "' id='" . esc_attr( $id ) . "' data-post-type='" . esc_attr( $post_type ). "'>\n";
			if ( $show_option_no_change )
				$output .= "\t<option value=\"-1\">$show_option_no_change</option>";
			if ( $show_option_none )
				$output .= "\t<option value=\"" . esc_attr($option_none_value) . "\">$show_option_none</option>\n";
			$output .= walk_page_dropdown_tree($pages, $depth, $r);
			$output .= "</select>\n";
		}

		echo $output;

		remove_filter('posts_clauses_request', array($this, 'posts_clauses_request_filter'), 10, 2 );

	}

	function posts_clauses_request_filter($pieces, $query ) {
		global $wpdb;
		// only return the fields required for the dropdown.
		$pieces['fields'] = "$wpdb->posts.ID, $wpdb->posts.post_parent, $wpdb->posts.post_title";

		return $pieces;
	}

	function get_posts_for_post_content_callback() {
		if (wp_verify_nonce( $_POST['nonce'], 'ddl-post-content-post-type-select' )) {
			$this->show_posts_dropdown($_POST['post_type'], get_ddl_name_attr('selected_post'));
		}
		die();
	}

	function get_ct_select_box () {
		if ( !isset($_POST["wpnonce"]) || !wp_verify_nonce($_POST["wpnonce"], 'wpv-ct-inline-edit') ) die("Undefined Nonce.");

		$view_tempates_available = $this->_get_view_templates_available();
		echo $this->_get_view_template_select_box($view_tempates_available);

		die();
	}
	
	function get_content_template () {
		if ( !isset($_POST["wpnonce"]) || !wp_verify_nonce($_POST["wpnonce"], 'wpv-ct-inline-edit') ) die("Undefined Nonce.");
		
		global $WPV_templates;
		if (isset($WPV_templates) && isset($_POST['view_template'])) {
			$content_template_id = $WPV_templates->get_template_id( $_POST['view_template'] );
			$content = $WPV_templates->get_template_content($content_template_id);
			
			echo $content;
		}
		
		die();
		
	}
}

add_filter('dd_layouts_register_cell_factory', 'dd_layouts_register_cell_post_content_factory');
function dd_layouts_register_cell_post_content_factory($factories) {
	$factories['cell-post-content'] = new WPDD_layout_cell_post_content_factory;
	return $factories;
}


add_action('wp_ajax_dll_add_view_template', 'ddl_add_view_template_callback');

function ddl_add_view_template_callback() {
    global $wpdb;
    //add new content template
    if ( !isset($_POST["wpnonce"]) || !wp_verify_nonce($_POST["wpnonce"], 'wpv-ct-inline-edit') ) die("Undefined Nonce.");

	$new_template = array(
	  'post_title'    => $_POST['ct_name'],
	  'post_type'      => 'view-template',
	  'post_content'  => '',
	  'post_status'   => 'publish',
	  'post_author'   => 1,// TODO check why author here
	);
	$ct_post_id = wp_insert_post( $new_template );
	update_post_meta( $ct_post_id, '_wpv_view_template_mode', 'raw_mode');
	update_post_meta( $ct_post_id, '_wpv-content-template-decription', '');

	echo json_encode(array('id' => $ct_post_id));

    die();
}