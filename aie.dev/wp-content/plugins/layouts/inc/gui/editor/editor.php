<?php

class WPDD_GUI_EDITOR{


	private $layout_id = null;


	function __construct(){

		$this->layout_id = isset( $_GET['layout_id'] ) ? $_GET['layout_id'] : null;

		if (isset($_GET['page']) and $_GET['page']=='dd_layouts_edit') {

			add_action('wpddl_pre_render_editor', array($this,'pre_render_editor'), 10, 1);
			add_action('wpddl_render_editor', array($this,'render_editor'), 10, 1);
			add_action('wpddl_after_render_editor', array($this,'after_render_editor'), 10, 1);

			add_action('wpddl_after_render_editor', array($this,'add_where_used_links'), 11, 1);
			add_action('wpddl_after_render_editor', array($this,'add_video_toolbar'), 11, 1);
			add_action('wpddl_after_render_editor', array($this,'add_select_post_types'), 11, 1);



			add_action('wpddl_layout_actions', array($this,'layout_actions'));

			add_action('admin_enqueue_scripts', array($this, 'preload_styles'));
			add_action('admin_enqueue_scripts', array($this, 'preload_scripts'));

			//add_action('admin_enqueue_scripts', array($this, 'load_latest_backbone'), -1000);

			do_action('wpddl_layout_actions');
		}

		//leave wp_ajax out of the **** otherwise it won't be fired
		add_action('wp_ajax_get_layout_data', array($this, 'get_layout_data_callback') );
		add_action('wp_ajax_save_layout_data', array($this, 'save_layout_data_callback') );
		add_action('wp_ajax_get_layout_parents', array($this, 'get_layout_parents_callback') );
		add_action('wp_ajax_check_for_parents_loop', array($this, 'check_for_parents_loop_callback') );
		add_action('wp_ajax_check_for_parent_child_layout_width', array($this, 'check_for_parent_child_layout_width_callback') );
	}

	function __destruct(){
	}


	public function get_layout_data_callback()
	{
		echo get_post_meta( $_POST['layout_id'], 'dd_layouts_settings', true );
		die(  );
	}
	private function slug_exists( $slug, $layout_id )
	{
		global $wpdb;

		$id = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type='dd_layouts' AND post_name=%s AND ID != %d", $slug, $layout_id) );

		if ( !empty( $id ) ) return true;

		return false;
	}
	public function save_layout_data_callback()
	{
		if( $_POST && wp_verify_nonce( $_POST['save_layout_nonce'], 'save_layout_nonce' ) )
		{
				if( $_POST['layout_model'] && $_POST['layout_id'] )
				{
					$raw = stripslashes( $_POST['layout_model'] );
					$json = json_decode( $raw, true );
					$children_to_delete = $json['children_to_delete'];
					$child_delete_mode = $json['child_delete_mode'];
					// we don't want to save these to the db
					//TODO:this is not affecting data saved in DB
					unset($json['children_to_delete']); 
					unset($json['child_delete_mode']);

					$post = get_post( $_POST['layout_id'] );

					if( $post->post_title != $json['name'] || $post->post_name != $json['slug'] )
					{

						 if( $this->slug_exists( $json['slug'], $_POST['layout_id'] ) )
						{
							echo json_encode(array( "Data" => array( 'error' =>  __( sprintf('The layout %s cannot be saved, the post name  %s is already taken. Please try with a different name.', $json['name'], $json['slug'] ), 'wpv-views') ) ) );

							die();
						}
						else
						{
							$postarr = array(
									'ID' => $_POST['layout_id'],
									'post_title' => $json['name'],
									'post_name' => $json['slug']
								);

								wp_update_post($postarr);

							$this->normalize_layout_slug_if_changed( $_POST['layout_id'], $json, $post->post_name );

						}

					}
					if ( $raw === WPDD_Layouts::get_layout_settings( $_POST['layout_id'] ) ) {
						// no need to save as it hasn't changed.
						$up = false;
					} else {
						$layout_previous_settings = get_post_meta($_POST['layout_id'], 'dd_layouts_settings', true);
						$up = update_post_meta($_POST['layout_id'], 'dd_layouts_settings', $_POST['layout_model'], $layout_previous_settings );
					}

					$msg = array( "Data" => $json );

					// I commented out !empty( $_POST['layout_css'] ) to allow users to erase css entirely
					if( isset( $_POST['layout_css'] ) /*&& !empty( $_POST['layout_css'] )*/ )
					{
						$msg['Data']['css_saved'] = $this->handle_layout_css( stripslashes($_POST['layout_css']) );
					}

					if( isset( $_POST['ddl_post_types_options'] ) && $_POST['ddl_post_types_options'] && !empty( $_POST['ddl_post_types_options'] ) )
					{
						$post_types = json_decode( stripslashes( $_POST['ddl_post_types_options'] ), true );
						$msg['Data']['ddl_post_types_options_saved'] = $this->handle_post_type_data_save( $post_types );
					}
					if( $children_to_delete && !empty($children_to_delete) )
					{
						$delete_children = $this->purge_layout_children( $children_to_delete, $child_delete_mode );
						if( $delete_children ) $msg['Data']['layout_children_deleted'] = $delete_children;
					}

					global $wpddlayout;

					$msg['Data']['post_types_options_html'] = $wpddlayout->post_types_manager->print_post_types_checkboxes( $_POST['layout_id'] );

					$msg['message']['layout_changed'] = $up;

					$send = json_encode( $msg );
				}
		}
		else
		{
			$send = json_encode(array( "Data" => array( 'error' =>  __( sprintf('Nonce problem: apparently we do not know from where the request comes from. %s', __METHOD__ ), 'ddl-layouts') ) ) );
		}
		echo $send;
		die();
	}

	private function normalize_layout_slug_if_changed( $layout_id, $layout_data, $previous_slug)
	{

			$current = (object) $layout_data;

			if( $current->slug === $previous_slug ) return false;

			$this->normalize_posts_where_used_data_on_slug_change( $current->slug, $previous_slug );

			//print_r( $current );

			if( property_exists($current, 'has_child') && $current->has_child === true )
			{
				$this->normalize_children_on_slug_change( $current, $current->slug, $previous_slug );
			}
	}

	private function normalize_posts_where_used_data_on_slug_change( $slug, $previous_slug )
	{
		global $wpdb;

		$sql = $wpdb->prepare("UPDATE $wpdb->postmeta SET meta_value = %s WHERE meta_key = %s AND meta_value = %s", $slug, WPDD_Layouts_PostTypesManager::META_KEY, $previous_slug  );

		$wpdb->query( $sql );
	}

	private function normalize_children_on_slug_change( $layout, $slug, $previous_slug )
	{
		global $wpddlayout;

		$defaults = array(
			'numberposts' => -1,
			'post_type' => 'dd_layouts',
			'suppress_filters' => true,
			'post_status' => 'publish',
			'posts_per_page' => -1
		);

		$query = new WP_Query($defaults);

		$list = $query->get_posts();

		$children = $wpddlayout->listing_page->get_children( $layout, $list, $previous_slug);

		if( !is_array($children) || sizeof($children) === 0 ) return;

		if( is_array($children) && sizeof($children) > 0 )
		{
			foreach( $children as $child )
			{
				$current = WPDD_Layouts::get_layout_settings( $child, true );
				$current->parent = $slug;
				WPDD_Layouts::save_layout_settings( $child, $wpddlayout->json_encode( (array)$current ) );
			}
		}
	}

	private function purge_layout_children( $children, $action )
	{
		global $wpddlayout;
		
		if( !is_array( $children ) ) return false;

		$ret = array();

		foreach( $children as $child )
		{
			$id = intval($child);
			$layout = WPDD_Layouts::get_layout_settings($id, true);
			$layout->parent = '';
			WPDD_Layouts::save_layout_settings( $id, $wpddlayout->json_encode( (array)$layout) );

			if( $action === 'delete' ) {
				// We also need to delete grandchildren
				$layout = $wpddlayout->get_layout_from_id($id);
				$grand_children = $layout->get_children();
				$this->purge_layout_children($grand_children, $action);
				$wpddlayout->post_types_manager->purge_layout_post_type_data( $id );
				$ret[] = wp_trash_post( $id );
			}
		}

		return true;
	}

	private function handle_layout_css( $css )
	{
		global $wpddlayout;
		return $wpddlayout->css_manager->handle_layout_css_save( $css );
	}

	private function handle_post_type_data_save( $post_types )
	{
		global $wpddlayout;
		return $wpddlayout->post_types_manager->handle_post_type_data_save( $post_types );
	}

	public function get_layout_parents_callback() {
		global $wpddlayout;

		$parents = array();

		$layout = $wpddlayout->get_layout( $_POST['layout_name'] );

		if ($layout) {
			$parent_layout = $layout->get_parent_layout();


			while ($parent_layout) {
				$parents[] = $parent_layout->get_post_slug();

				$parent_layout = $parent_layout->get_parent_layout();
			}
		}

		echo json_encode($parents);

		die();
	}

	public function check_for_parents_loop_callback () {
		global $wpddlayout;

		$loop_found = false;

		$layout = $wpddlayout->get_layout( $_POST['new_parent_layout_name'] );

		if ($layout) {
			$parent_layout = $layout->get_parent_layout();

			while ($parent_layout) {
				if ($_POST['layout_name'] == $parent_layout->get_name()) {
					$loop_found = true;
					break;
				}

				$parent_layout = $parent_layout->get_parent_layout();
			}
		}

		if ($loop_found) {
			echo json_encode(array('error' => sprintf(__("You can't use %s as a parent layout as it or one of its parents has the current layout as a parent.", 'ddl-layouts'), '<strong>' . $_POST['new_parent_layout_name'] . '</strong>') ) );
		} else {
			echo json_encode(array('error' => ''));
		}

		die();

	}

	public function check_for_parent_child_layout_width_callback () {
		global $wpddlayout;

		$layout = $wpddlayout->get_layout( $_POST['parent_layout_name'] );

		$result = json_encode(array('error' => ''));

		if ($layout) {
			$child_layout_width = $layout->get_width_of_child_layout_cell();

			if ($child_layout_width != $_POST['width']) {
				$result = json_encode(array('error' => sprintf(__("This layout width is %d and the child layout width in %s is %d. This layout may not display correctly.", 'ddl-layouts'), $_POST['width'], '<strong>' . $_POST['parent_layout_title'] . '</strong>', $child_layout_width) ) );
			}
		}

		echo $result;

		die();
	}

	function preload_styles(){
		global $wpddlayout;

		$wpddlayout->enqueue_styles(
			array(
				'progress-bar-css' ,
				'toolset-font-awesome',
				'toolset-utils',
				'jq-snippet-css',
				'jquery-ui',
				'wp-editor-layouts-css',
				'toolset-colorbox',
				'ddl-dialogs-css',
				'wp-pointer' ,
				'toolset-select2-css',
				'layouts-select2-overrides-css',
				'wp-mediaelement',
			)
		);

		$wpddlayout->enqueue_cell_styles();
	}

	function preload_scripts(){
		global $wpddlayout;

		$wpddlayout->enqueue_scripts(
			array(
				'jquery-ui-cell-sortable',
				'jquery-ui-custom-sortable',
				'jquery-ui-resizable',
				'jquery-ui-tabs',
				'wp-pointer',
				'backbone',
				'select2',
				'toolset-utils',
				'wp-pointer',
				'wp-mediaelement',
				'ddl-sanitize-html',
				'ddl-sanitize-helper',
				'ddl-post-types',
				'ddl-editor-main',
				'media_uploader_js'
			)
		);

		$wpddlayout->localize_script('ddl-editor-main', 'DDLayout_settings', array(
			'DDL_JS' => array(
				'res_path' => WPDDL_RES_RELPATH,
				'lib_path' => WPDDL_RES_RELPATH . '/js/external_libraries/',
				'editor_lib_path' => WPDDL_GUI_RELPATH."editor/js/",
				'dialogs_lib_path' => WPDDL_GUI_RELPATH."dialogs/js/",
				'layout_id' => $this->layout_id,
				'create_layout_nonce' => wp_create_nonce('create_layout_nonce'),
				'save_layout_nonce' => wp_create_nonce('save_layout_nonce'),
				'DEBUG' => WPDDL_DEBUG,
				'strings' => $this->get_editor_js_strings(),
				'has_theme_sections' => $wpddlayout->has_theme_sections(),
				'is_css_enabled' => $wpddlayout->css_manager->is_css_possible()
				, 'current_framework' => $wpddlayout->frameworks_options_manager->get_current_framework()
				)
			)
		);

		$wpddlayout->enqueue_cell_scripts();

	}

	function load_latest_backbone() {
		// load our own version of backbone for the editor.
		wp_dequeue_script('backbone');
		wp_deregister_script('backbone');
		wp_register_script('backbone', WPDDL_RES_RELPATH . '/js/external_libraries/backbone-min.js', array('underscore','jquery'), '1.1.0');
		wp_enqueue_script('backbone');

	}

	function pre_render_editor($inline) { ?>

		<div class="wrap" id="js-dd-layout-editor">

			<?php

			global $post;
			$post = $post ? $post : get_post( $this->layout_id );

			if (!$inline) {
				include_once 'templates/editor_header_box.tpl.php';
			}

	}

	function render_editor($inline){
		include WPDDL_GUI_ABSPATH . 'create_new_layout.php';
		include_once 'templates/editor_box.tpl.php';
		ddl_render_editor($inline);
	}

	function after_render_editor() {

		?>
		</div> <!-- .wrap -->

	<?php
	}

	function layout_actions(){
		if(isset($_REQUEST['action'])){
			switch ($_REQUEST['action']) {
				case 'trash':
					$this->delete_layout($_REQUEST['post']);
					break;
				default:
					break;
			}
		}
	}

	function delete_layout($layout_id){
		$post_id = $layout_id;
		wp_delete_post($post_id, true);
		delete_post_meta($post_id, 'dd_layouts_settings');
		delete_post_meta($post_id, 'dd_layouts_header');
		delete_post_meta($post_id, 'dd_layouts_styles');
		$url = home_url( 'wp-admin').'/admin.php?page=dd_layouts';
		header("Location: $url", true, 302);
		die();
	}

	public static function load_js_templates( $tpls_dir )
	{
		global $wpddlayout;

		WPDD_FileManager::include_files_from_dir( dirname(__FILE__), $tpls_dir );

		echo apply_filters("ddl_print_cells_templates_in_editor_page", $wpddlayout->get_cell_templates() );
	}

	function get_editor_js_strings () {
		return array(
			'only_one_cell' => __('Only one cell of this type is allowed per layout.', 'ddl-layouts'),
			'save_required' => __('This layout has changed', 'ddl-layouts'),
			'page_leave_warning' => __('This layout are changed. Are you sure you want to leave this page?', 'ddl-layouts'),
			'save_before_edit_parent' => __('Do you want to save the current layout before editing the parent layout?', 'ddl-layouts'),
			'save_required_edit_child' => __('Switching to the child layout', 'ddl-layouts'),
			'save_before_edit_child' => __('Do you want to save the current layout before editing the child layout?', 'ddl-layouts'),
			'save_layout_yes' => __('Save layout', 'ddl-layouts'),
			'save_layout_no' => __('Discard changes', 'ddl-layouts'),
			'save_required_new_child' => __('Creating a new child layout', 'ddl-layouts'),
			'save_before_creating_new_child' => __('Do you want to save the current layout before creating a new child layout?', 'ddl-layouts'),
			'no_parent' => __('No parent set', 'ddl-layouts'),
			'content_template' => __('Content Template', 'ddl-layouts'),
			'save_complete' => __('The layout has been saved.', 'ddl-layouts'),
			'one_column' => __('1 Column', 'ddl-layouts'),
			'columns' => __('Columns', 'ddl-layouts'),
			'at_least_class_or_id' => __('You should define either an ID or one class for this cell to style its CSS', 'ddl-layouts'),
			'select_range_one_column' => __('1 column', 'ddl-layouts'),
			'select_range_more_columns' => __('%d columns', 'ddl-layouts'),
			'dialog_yes' => __('Yes', 'ddl-layouts'),
			'dialog_no' => __('No', 'ddl-layouts'),
			'dialog_cancel' => __('Cancel', 'ddl-layouts'),
			'slug_unwanted_character' => __("The slug should contain only lower case letters", 'ddl-layouts' ),
			'save_and_also_save_css' => __('The layout has been saved. Layouts CSS has been updated.', 'ddl-layouts'),
			'save_and_save_css_problem' => __('The layout has been saved. Layouts CSS has NOT been updated. Check credentials for the file at ', 'ddl-layouts'),
			'invalid_slug' => __("The slug should contain only lower case letters and should not be an empty string.",'ddl-layouts'),
			'title_not_empty_string' => __("The title shouldn't be an empty string.", 'ddl-layouts'),
			'more_than_4_rows' => __('If you need more than 4 rows you can add them later in the editor', 'ddl-layouts'),
			'id_duplicate' => __("This id is already used in the current layout, please select a unique id for this element", 'ddl-layouts'),
			'edit_cell' => __('Edit cell', 'ddl-layouts'),
			'remove_cell' => __('Remove cell', 'ddl-layouts'),
			'set_cell_type' => __('Select cell type', 'ddl-layouts'),
			'show_grid_edit' => __('Show grid edit', 'ddl-layouts'),
			'hide_grid_edit' => __('Hide grid edit', 'ddl-layouts'),
			'css_file_loading_problem' => __('It is not possible to handle CSS loading in the front end. You should either make your uploads directory writable by the server, or use permalinks.', 'ddl-layouts'),
			'save_required_open_view' => __('Switching to the View', 'ddl-layouts'),
			'save_before_open_view' => __('The layout has changed. Do you want to save the current layout before switching to the View?', 'ddl-layouts'),
		);
	}

	public static function print_layouts_css()
	{
		global $wpddlayout;
		echo $wpddlayout->get_layout_css();
	}

	public function add_where_used_links() {

		global $wpddlayout;

		$posts = $wpddlayout->get_where_used( $_GET['layout_id'] );

		include_once WPDDL_GUI_ABSPATH.'editor/templates/list-layouts-where_used.box.tpl.php';

	}

	public function add_video_toolbar()
	{
		include_once WPDDL_GUI_ABSPATH.'editor/templates/tutorial-video-bar.box.tpl.php';
	}

	public function add_select_post_types()
	{
		global $wpddlayout;

		$layout = WPDD_Layouts::get_layout_settings($this->layout_id, true);

		if( property_exists ( $layout , 'has_child' ) === false ) $layout->has_child = false;

		if( $layout->has_child === false ):
		?>
		<div class="dd-layouts-wrap">
			<div class="dd-layouts-where-used js-selected-post-types-in-layout-div">
				<?php echo $wpddlayout->post_types_manager->print_post_types_checkboxes(); ?>
			</div>
		</div> <!-- .dd-layouts-wrap -->
	<?php
		endif;
	}
}