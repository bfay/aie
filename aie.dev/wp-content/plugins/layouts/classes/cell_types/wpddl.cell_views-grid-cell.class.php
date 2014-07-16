<?php
/*
 * Theme Views content grid cell type.
 * Displays current theme basic footer with two credits area.
 *
 */

/*
 * Render preview for view
 */
add_action('wp_ajax_ddl_views_content_grid_preview', 'ddl_views_content_grid_preview');
function ddl_views_content_grid_preview(){
	if (!isset($_POST['wpnonce']) || !wp_verify_nonce($_POST['wpnonce'],
                        'ddl_layout_view_nonce')) {
            die('verification failed');
        }	
		
	global $wpdb;

	if ( isset($_POST['view_id']) ){
		$view_name = $_POST['view_id'];
	}else{
		return __('View not set','ddl-layouts');
	}
	$layout_style = array(
		'unformatted' => __('Unformatted','ddl-layouts'),
		'bootstrap-grid' => __('Unformatted','ddl-layouts'),
		'table' => __('Table-based grid','ddl-layouts'),
		'table_of_fields' => __('Table','ddl-layouts'),
		'un_ordered_list' => __('Unordered list','ddl-layouts'),
		'ordered_list' => __('Ordered list','ddl-layouts')
	);
	$view = $wpdb->get_results( $wpdb->prepare("SELECT ID, post_title FROM $wpdb->posts WHERE post_name = '%s' AND post_type='view'",$view_name) );
	if ( isset($view[0]) ){
		$post_title = $view[0]->post_title;
		$id = $view[0]->ID;
		$meta = get_post_meta($id,'_wpv_layout_settings',true);
		$view_output = get_view_query_results($id);
		ddl_views_generate_cell_preview( $post_title, $id, $meta, $view_output );
	}

	die();

}

/*
 * Create new view and output view info
 * $id, $slug, $title
 */
add_action('wp_ajax_ddl_create_new_view', 'ddl_create_new_view');
function ddl_create_new_view(){
	global $wpdb;

	if (!isset($_POST['wpnonce']) || !wp_verify_nonce($_POST['wpnonce'],
                        'ddl_layout_view_nonce')) {
            die('verification failed');
        }
	$name = $original_name = $_POST['cell_name'];
	$i = 0;
	$name_in_use = true;
	while( $name_in_use ){
		$i++;
		$postid = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_title = '" . $name . "' AND post_type='view'" );
		if ( $postid ) {
			$name = $original_name . ' ' . $i;
		}
		else{
			$name_in_use = false;
		}
	}
	$args = array(
		'title' => $name
	);
	$view_id = wpv_create_view( $args );
    if ( isset( $view_id['success']) ){

		$id = $view_id['success'];

		$view_normal_layout_defaults = wpv_view_defaults( 'view_layout_settings', 'full' );
		$view_normal_layout_defaults['bootstrap_grid_cols'] = $_POST['cols'];
		$view_normal_layout_defaults['bootstrap_grid_container'] = 'false';
		$view_normal_layout_defaults['bootstrap_grid_individual'] = '';
		$view_normal_layout_defaults['style'] = 'bootstrap-grid';
		$view_normal_layout_defaults['insert_at'] = 'insert_replace';
		$view_normal_layout_defaults['real_fields'] = '[[wpv-post-link]]';
		update_post_meta($id, '_wpv_layout_settings', $view_normal_layout_defaults);
		$res = $wpdb->get_results( "SELECT post_name FROM $wpdb->posts WHERE ID = '" . $id . "' AND post_type='view'" );
		$post_name = $res[0]->post_name;
		$output = json_encode(array( 'id'=>$id, 'post_name' => $post_name, 'post_title'=> $name));
		print json_encode(array( 'id'=>$id, 'post_name' => $post_name, 'post_title'=> $name));
		die();
	}

	die();
}

if ( ! function_exists('register_views_content_grid_cell_init') ) {

	function register_views_content_grid_cell_init() {
		if ( function_exists('register_dd_layout_cell_type') ) {
			register_dd_layout_cell_type('views-content-grid-cell',
				array(
					'name'						=> __('Views Content Grid', 'ddl-layouts'),
					'description'				=> __('Content driven list, displayed as a grid. This cell is powered by the Views plugin, where you can customize the content of cells, as well as the grid itself.', 'ddl-layouts'),
					'category'					=> __('Standard WordPress elements', 'ddl-layouts'),
					'button-text'				=> __('Assign Views Content Grid', 'ddl-layouts'),
					'dialog-title-create'		=> __('Create a new Views Content Grid', 'ddl-layouts'),
					'dialog-title-edit'			=> __('Edit Views Content Grid cell', 'ddl-layouts'),
					'dialog-template-callback'	=> 'views_content_grid_dialog_template_callback',
					'cell-content-callback'		=> 'views_content_grid_content_callback',
					'cell-template-callback'	=> 'views_content_grid_template_callback',
					'category-icon-css'		   => 'icon-table',
					'icon-css'				   => 'icon-table',
					'register-scripts'		   => array(
						array( 'ddl_views_content_grid_js', WPDDL_RELPATH . '/inc/gui/dialogs/js/views-grid-cell.js', array( 'jquery' ), WPDDL_VERSION, true ),
					),
				)
			);
		}
	}
	add_action( 'init', 'register_views_content_grid_cell_init' );


	function views_content_grid_dialog_template_callback() {
		global $WP_Views;
		if( class_exists('WP_Views') ){
			$show_existing_views_dropdown = '';
			$i = 0;
			ob_start();
			?>
			<p>
				<label class="radio" for="ddl-views-grid-exitsting-view">
				<?php $checked = ( get_ddl_field('ddl_layout_view_slug') == '' )?' checked="checked" ':'';?>
				<input type="radio" name="view-grid-view-action" class="js-ddl-views-grid-create" value="existing_layout" <?php echo $checked?> id="ddl-views-grid-exitsting-view"><?php _e('Use an existing View', 'ddl-layouts');?>
				</label>
			</p>
			<p class="js-ddl-select-existing-view">
				<select name="<?php the_ddl_name_attr('ddl_layout_view_slug'); ?>" class="js-ddl-view-select">
				<option value=""><?php _e('None','ddl-layouts');?></option>';
				<?php
				$wpv_args = array( // array of WP_Query parameters
					'post_type' => 'view',
					'posts_per_page' => -1,
					'order' => 'ASC',
					'orderby' => 'title',
					'post_status' => 'publish'
				);
				$wpv_query = get_posts( $wpv_args );
				$wpv_count_posts = count($wpv_query);
				if ( $wpv_count_posts > 0 ) {
					foreach ( $wpv_query as $post ) :
						if (!$WP_Views->is_archive_view($post->ID)){
							$i++;
							?>
							<option data-id="<?php echo $post->ID; ?>" value="<?php echo $post->post_name; ?>"><?php echo $post->post_title; ?></option>
							<?php
						}
					endforeach;
				}
				?>
				</select>
				<?php if ( isset($WP_Views) && class_exists('WP_Views') && !$WP_Views->is_embedded()){?>
				<br />
				<div class="js-dll-edit-view-link-section">
					<button class="button button-primary js-ddl-edit-view-link"><?php _e('Edit the View settings in a new window', 'ddl-layouts'); ?></button> &nbsp;<?php _e('or', 'ddl-layouts'); ?> &nbsp;<a href="#" class="js-ddl-edit-view-link"><?php _e('Go to the View', 'ddl-layouts'); ?></a>
				</div>
				<?php }?>
			</p>
			<?php

			$show_existing_views_dropdown = ob_get_clean();
			$count_existing_views = $i;
			if ( $i == 0){
			 	$show_existing_views_dropdown = '<div class="ddl_existing_views_content" style="display:none">'.$show_existing_views_dropdown.'</div>';
			}else{
			 	$show_existing_views_dropdown = '<div class="ddl_existing_views_content">'.$show_existing_views_dropdown.'</div>';
			}

		}

		ob_start();

		//If Views activated
		if( defined('WPV_VERSION') && WPV_VERSION < 1.6 ){ ?>
			<input type="hidden" value="0" class="js-views-content-grid_is_views_installed" />
			<p>
				<?php echo sprintf(__('This cell requires version 1.6 or greater of the Views plugin, you are using version %s. Install and activate the latest version of Views and you will be able to create custom content-driven grids.', 'ddl-layouts'), WPV_VERSION); ?>
			</p>
		<?php }
		else{

			?>
			<input type="hidden" value="1" class="js-views-content-grid_is_views_installed" />
			<?php 
			
			if ( isset($WP_Views) && class_exists('WP_Views') && ( !$WP_Views->is_embedded() || ($WP_Views->is_embedded() && $count_existing_views>0)) ){
			?>
			<div class="ddl-form">
				<fieldset>
					<legend><?php _e('View:', 'ddl-layouts'); ?></legend>
					
					<div class="fields-group">
						<?php 
						$disabled = '';
						if ( isset($WP_Views) && class_exists('WP_Views') && $WP_Views->is_embedded()){
								$disabled = ' style="display: none;"';
						}
						
						?>
						<p<?php echo $disabled?>>
							<label class="radio" for="ddl-views-grid-new-view">
							<?php $checked = ( get_ddl_field('ddl_layout_view_slug') == '' )?' checked="checked" ':'';?>
							<input type="radio" name="view-grid-view-action" class="js-ddl-views-grid-create" checked="checked" <?php echo $checked?> value="new_layout" id="ddl-views-grid-new-view"><?php _e('Create new View', 'ddl-layouts');?>
							</label>
						</p>
						<?php echo $show_existing_views_dropdown?>
					</div>
					
				</fieldset>
			</div>
			<?php }?>
			<?php if( isset($WP_Views) && class_exists('WP_Views') && $WP_Views->is_embedded() ){?>
			<div class="toolset-alert">
				<p>
					<?php _e('This cell requires Views plugin. Install and activate Views and you will be able to create custom content-driven grids.', 'ddl-layouts'); ?>
					<br>
					<a class="fieldset-inputs" href="http://wp-types.com/home/views-create-elegant-displays-for-your-content/" target="_blank">
						<?php _e('Get Views plugin', 'ddl-layouts');?> &raquo;
					</a>
				</p>
			</div>
			<?php }?>
			<div class="ddl-form">
				<div class="js-fluid-grid-designer">
					<fieldset>
						<legend><?php _e('Grid size:', 'ddl-layouts'); ?></legend>
						<div class="fields-group">
							<div id="js-fluid-views-grid-slider-horizontal" class="horizontal-slider"></div>
							<div id="js-fluid-views-grid-slider-vertical" class="vertical-slider"></div>
							<div class="grid-designer-wrap grid-designer-wrap-views">
								<div class="grid-info-wrap">
									<span id="js-fluid-views-grid-info-container" class="grid-info"></span>
								</div>
								<div id="js-fluid-views-grid-designer" class="grid-designer"
									data-rows="1"
									data-cols="2"
									data-max-cols="12"
									data-max-rows="1"
									data-slider-horizontal="#js-fluid-views-grid-slider-horizontal"
									data-slider-vertical=""
									data-info-container="#js-fluid-views-grid-info-container"
									data-message-container="#js-views-fluid-grid-message-container"
									data-fluid="true">
								</div>
							</div>
							<button class="button button-primary js-create-and-edit-view"><?php _e('Create the View and edit it', 'ddl-layouts'); ?></button>
							<div id="js-fluid-views-grid-message-container"></div>
						</div>
					</fieldset>
				</div>
			</div>

		<?php
		 echo wp_nonce_field('ddl_layout_view_nonce', 'ddl_layout_view_nonce', true, false);
		}


		return ob_get_clean();

	}

	function views_content_grid_template_callback() {
        global $WP_Views;
		if( class_exists('WP_Views') ){

	        ob_start();

	        ?> <div class="cell-content">
	                <p class="cell-name"><%- name %> </p>
	                <div class="cell-preview">
	                    <%
	                       var preview = ddl_views_content_grid_preview( content.ddl_layout_view_slug, '<?php _e('Updating', 'ddl-layouts'); ?>...', '<?php _e('Loading', 'ddl-layouts'); ?>...' );
	                 	   print( preview );
	                    %>
	                </div>
	            </div>
	       <?php
	       return ob_get_clean();
	   }
	}



	function views_content_grid_content_callback() {
		//Render View
	    return render_view( array( 'name' => get_ddl_field('ddl_layout_view_slug') ) ) ;
	}

}
function ddl_views_generate_cell_preview( $post_title, $id, $meta, $view_output ){

	//Generate preview for bootstrap grid and table based grid
	if ( !isset($meta['style']) ){
		$meta['style'] = 'unformatted';	
	}
	if ( $meta['style'] == 'bootstrap-grid'  ):
		$col_number = $meta['bootstrap_grid_cols'];
		$i=$k=0;
		$col_width = 12/$col_number;
	?>
		<i class="icon-th-large ddl-view-layout-icon"></i><?php _e('Bootstrap grid', 'ddl-layouts'); ?>
		<br />
		<div class="presets-list fields-group">
		<?php
		$total_rows = 0;
		if ( count($view_output) > 0 ){
			for ($j = 0, $limit=count($view_output); $j < $limit; $j++){
			$view_post = $view_output[$j];
			$cell_content = ddl_view_content_grid_get_title( $view_post );
			$i++;
			if ($i == 1){
				$total_rows++;
				if ( $total_rows > 3){
					$j = count($view_output)+1;
					$hidden_items_count = $limit-$k;
					$hidden_rows = ceil($hidden_items_count/$col_number);
					?>
					<div class="row-fluid">
						<div class="span-preset12 views-cell-preview views-cell-preview-more">
							<?php echo sprintf(__('Plus %s more rows - %s items in total', 'ddl-layouts'), $hidden_rows, $limit); ?>
						</div>
					</div>
					<?php
					continue;
				}
				?>
				<div class="row-fluid">
				<?php
				}
				?>
				<div class="span-preset<?php echo $col_width; ?> views-cell-preview" ><?php echo $cell_content; ?></div>
					<?php
					if ( $i == $col_number){
						$i=0;
						?></div><?php
					}
					$k++;
				}
				if ( $i != 0 ){
					?></div><?php
				}
		} else {
			//Show empty grid when no posts
			?>
			<div class="row-fluid">
			<?php
			for( $i=0; $i<$col_number; $i++){
			?>
				<div class="span-preset<?php echo $col_width;?> views-cell-preview" ></div>
			<?php
			}
			?>
			</div>
			<div class="row-fluid">
				<div class="span-preset12 views-cell-preview views-cell-preview-more">
					<?php _e('No items where returned by the View', 'ddl-layouts'); ?>
				</div>
			</div>
		<?php
		}
		?></div><?php
	elseif ( $meta['style'] == 'table' ):
		$col_number = $meta['table_cols'];
		$i=$k=0;
		$col_width = round(100/$col_number, 2)-2;
		$total_rows = 0;
		?>
		<i class="icon-th ddl-view-layout-icon"></i><?php _e('Table-based grid', 'ddl-layouts'); ?>
		<br />
		<?php
		if ( count($view_output) > 0 ){
			$total_rows = 0;
			for ($j = 0, $limit=count($view_output); $j < $limit; $j++){
				$view_post = $view_output[$j];
				$cell_content = ddl_view_content_grid_get_title( $view_post );
				$i++;
				if ( $i == 1){
					$total_rows++;
					if ( $total_rows > 3){
						$j = count($view_output)+1;
						$hidden_items_count = $limit-$k;
						$hidden_rows = ceil($hidden_items_count/$col_number);
						?>
						<div class="row-fluid row">
							<div class="views-cell-table-preview views-cell-preview views-cell-preview-more views-cell-table-preview-more" style="width:100%;">
								<?php echo sprintf(__('Plus %s more rows - %s items in total', 'ddl-layouts'), $hidden_rows, $limit); ?>
							</div>
						</div>
						<?php
						continue;
					}
						?>
					<div class="row-fluid">
					<?php }	?>
					<div class="views-cell-preview views-cell-table-preview" style="width:<?php echo $col_width?>%;"><?php echo $cell_content;?></div>
					<?php
					if ( $i == $col_number ){
					$i = 0;
						?>
						</div>
					<?php }

				}
				if ( $i != 0 ){
					?></div><?php
				}
		} else {
			//If table 0 posts
			?>
			<div class="row-fluid">
			<?php
				for( $i=0; $i<$col_number; $i++){
					?>
					<div class="views-cell-preview views-cell-table-preview" style="width:<?php echo $col_width?>%;"></div>
				<?php
				}
				?>
			</div>
			<div class="row-fluid row">
				<div class="views-cell-table-preview views-cell-preview views-cell-preview-more views-cell-table-preview-more" style="width:100%;">
					<?php _e('No items where returned by the View', 'ddl-layouts'); ?>
				</div>
			</div>
			<?php
			}
	elseif ( $meta['style'] == 'unformatted' ||  $meta['style'] == 'un_ordered_list' || $meta['style'] == 'ordered_list' ):
		switch ($meta['style']) {
			case 'unformatted':
				$style_icon = 'icon-code';
				$style_name = __('Unformated', 'ddl-layouts');
				break;

			case 'un_ordered_list':
				$style_icon = 'icon-list-ul';
				$style_name = __('Unordered list', 'ddl-layouts');
				break;

			case 'ordered_list':
				$style_icon = 'icon-list-ol';
				$style_name = __('Ordered list', 'ddl-layouts');
				break;

		}
		?>
		<i class="<?php echo $style_icon; ?> ddl-view-layout-icon"></i><?php echo $style_name; ?>
		<br />
		<div class="presets-list fields-group">
		<?php
		for ( $i=0; $i<3; $i++ ){
			if (isset($view_output[$i])) {
				$view_post = $view_output[$i];
				$cell_content = ddl_view_content_grid_get_title( $view_post );
			} else {
				$cell_content = '';
			}
			?>
			<div class="row-fluid row">
				<?php if ( $meta['style'] == 'unformatted' ){?>
				<div class="span-preset12 views-cell-preview" >
					<?php echo $cell_content;?>
				</div>
				<?php }elseif(  $meta['style'] == 'un_ordered_list' || $meta['style'] == 'ordered_list' ){
					$list = '&#149;';
					if ( $meta['style'] == 'ordered_list' ){
						$list = $i+1;
					}
					?>
					<div class="views-cell-preview views-cell-table-preview views-cell-table-preview-no-border" style="width:8%;">
						<?php echo $list;?>
					</div>
					<div class="views-cell-preview views-cell-table-preview" style="width:85%;">
						<?php echo $cell_content;?>
					</div>
					<?php }?>
				</div>
				<?php
			}
			$cell_message = __('No items where returned by the View', 'ddl-layouts');
			$limit = count($view_output);
			if ( $limit > 3 ){
				$limit -= 3;
				$cell_message = sprintf(__('Plus %s more items', 'ddl-layouts'), $limit);
			}
		?>
		<div class="row-fluid">
			<div class="span-preset12 views-cell-preview views-cell-preview-more">
				<?php echo $cell_message ?>
			</div>
		</div>
		</div>
		<?php
	elseif ( $meta['style'] == 'table_of_fields' ):
		$col_number = (count($meta['fields'])+1)/5-1;
		$i=$k=0;
		$col_width = round(100/$col_number, 2)-2;
		$total_rows = 0;
		?>
		<i class="icon-table ddl-view-layout-icon"></i><?php _e('Table', 'ddl-layouts'); ?>
		<br />
		<div class="presets-list fields-group">
		<table class="ddl-view-table-preview" width="100%">
			<thead>
				<tr>

					<?php
					for ( $i=0,$limit=$col_number; $i<$limit; $i++ ){
						$col_title = __('Column ', 'ddl-layouts').' '.$i;
						if ( isset($meta['fields']['row_title_'.$i]) && !empty($meta['fields']['row_title_'.$i]) ){
							$col_title = $meta['fields']['row_title_'.$i];
						}
						?>
						<td width="<?php echo 100/count($meta['fields']); ?>%"><?php echo $col_title;?></td>
						<?php
					}
					?>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="<?php echo count($meta['fields']); ?>">

						<?php
						for ( $i=0; $i<3; $i++ ){
							if (isset($view_output[$i])) {
								$view_post = $view_output[$i];
								$cell_content = ddl_view_content_grid_get_title( $view_post );
							} else {
								$cell_content = '';
							}
							?>
							<div class="row-fluid row">
								<div class="span-preset12 views-cell-preview" >
									<?php echo $cell_content;?>
								</div>
							</div>
						<?php
						}
						$cell_message = __('No items where returned by the View', 'ddl-layouts');
						$limit = count($view_output);
						if ( $limit > 3 ){
							$limit -= 3;
							$cell_message = sprintf(__('Plus %s more items', 'ddl-layouts'), $limit);
						}
						?>

					</td>
				</tr>
			</tbody>
		</table>
		<div class="presets-list fields-group">
			<div class="row-fluid">
				<div class="span-preset12 views-cell-preview views-cell-preview-more">
					<?php echo $cell_message ?>
				</div>
			</div>
		</div>
		<?php
	else:
		$view_count = count($view_output);
		?>
		<?php _e('View name', 'ddl-layouts'); ?>: <?php echo $post_title; ?><br>
		<?php _e('Layout Style', 'ddl-layouts'); ?>: <?php echo isset($layout_style[$meta['style']])?$layout_style[$meta['style']]:'Undefined'; ?><br>
		<?php if ( $meta['style'] == 'bootstrap-grid' ) : ?>
			<?php _e('Columns', 'ddl-layouts'); ?>: <?php echo $meta['bootstrap_grid_cols']; ?><br>
		<?php endif; ?>
		<?php if ( $meta['style'] == 'table' ): ?>
			<?php _e('Columns', 'ddl-layouts'); ?> <?php echo $meta['table_cols']; ?><br>
		<?php endif; ?>
		<?php _e('Items to display', 'ddl-layouts'); ?>: <?php echo $view_count; ?><br>
		<?php
	endif;
}

function ddl_view_content_grid_get_title( $view_post ){
	$cell_content = '';
	if ( isset($view_post->post_title) ){
		$cell_content = $view_post->post_title;
	}
	if ( isset($view_post->name) ){
		$cell_content = $view_post->name;
	}
	if ( isset($view_post->user_login) ){
		$cell_content = $view_post->user_login;
	}
	return $cell_content;
}
