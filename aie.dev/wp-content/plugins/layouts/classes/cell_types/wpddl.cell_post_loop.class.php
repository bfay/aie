<?php

function register_post_loop_cell_init() {

	register_dd_layout_cell_type (
		'post-loop-cell',
		array (
				'name' => __('Post loop', 'ddl-layouts'),
				'icon-css' => 'icon-cog',
				'description' => __('A cell that displays a WordPress post loop.', 'ddl-layouts'),
				'button-text' => __('Assign Post Loop cell', 'ddl-layouts'),
				'dialog-title-create' => __('Create a Post Loop cell', 'ddl-layouts'),
				'dialog-title-edit' => __('Edit Post Loop cell', 'ddl-layouts'),
				'dialog-template-callback' => 'post_loop_cell_dialog_template_callback',
				'cell-content-callback' => 'post_loop_cell_content_callback',
				'cell-template-callback' => 'post_loop_cell_template_callback',
				'cell-class' => 'post-loop-cell',
			  )
	);
}
add_action( 'init', 'register_post_loop_cell_init' );


function post_loop_cell_dialog_template_callback() {
    return '';
}


// Callback function for displaying the cell in the editor.
function post_loop_cell_template_callback() {

	// This should return an empty string or the attribute to display.
	return '';

}

// Callback function for display the cell in the front end.
function post_loop_cell_content_callback($cell_settings) {
	ob_start();

	if (have_posts()) {
		while (have_posts()) {
            the_post(); 
			get_template_part( 'content', get_post_format() );
		}
    }
    
	return ob_get_clean();
}
