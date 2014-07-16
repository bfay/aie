<?php // TODO: Move it to a separate file ?>
<?php if ( sizeof($posts) > 0 ): ?>
	<div class="dd-layouts-wrap">
		<div class="dd-layouts-where-used">
			<p>
				<?php _e('This layout is used for these posts:', 'ddl-layouts'); ?>
			</p>
			<ul>
				<?php foreach($posts as $post): ?>
					<li>
						<a href="<?php echo get_edit_post_link($post->ID); ?>"><?php echo $post->post_title; ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
<?php endif; ?>