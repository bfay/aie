<div class="js-ddl-message-container dd-message-container"></div>

<div class="dd-layouts-wrap">

	<div class="dd-layouts-header">
		<div id="icon-edit" class="icon32 icon32-posts-dd_layouts"><br></div>
		<h2>
			<span class="js-layout-title dd-layout-title"><?php echo $post->post_title; ?></span>
			<a href="admin.php?page=dd_layouts&new_layout=true" class="add-new-h2"><?php _e( 'Add New', 'ddl-layouts' ); ?></a>
			<i class="icon-edit js-edit-layout-settings" title="<?php _e( 'Edit layout settings', 'ddl-layouts' ); ?>"></i>
		</h2>

		<p class="dd-layout-slug-wrap">
			<label for="layout-slug"><?php _e('Layout slug:','ddl-layouts'); ?> </label>
			<input id="layout-slug" name="layout-slug" type="text" class="edit-layout-slug js-edit-layout-slug" value="<?php echo urldecode( $post->post_name ); ?>"/>
		</p>
	</div>
</div>