<p>
	<?php _e('Use this layout for these post types:', 'ddl-layouts'); ?>
</p>
<ul class="post-types-list-in-layout-editor js-post-types-list-in-layout-editor">
	<?php foreach( $types as $type ): ?>
	<?php
		$checked = $this->post_type_is_in_layout( $type->name, $current ) ? 'checked' : '';
		$unique_id = uniqid($id_string, true);
	?>
		<li class="js-checkboxes-elements-wrap">
			<label for="post-type-<?php echo $unique_id . $type->name; ?>">
				<input type="checkbox" <?php echo $checked;?> name="post-types[]" class="js-ddl-post-type-checkbox<?php echo $id_string ? '-'.$id_string: '';?>" value="<?php echo $type->name;?>" id="post-type-<?php echo $unique_id . $type->name;?>">
				<?php echo $type->labels->menu_name;?>
			</label>
			<?php if( $do_not_show === false ): ?>
				<?php $this->print_apply_to_all_link_in_layout_editor( $type, $checked, $current );?>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>