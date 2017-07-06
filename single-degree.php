<?php get_header(); the_post(); ?>

<div class="container mb-5 mt-3 mt-md-5">
	<article class="<?php echo $post->post_status; ?> post-list-item">
		<div class="row">
			<div class="col-md-8">
				<?php echo get_degree_meta_markup( $post ); ?>
				<?php echo get_degree_desc_markup( $post ); ?>
				<?php the_content(); ?>
			</div>
			<div class="col-md-4">
				<?php echo get_degree_cta_btns_markup( $post ); ?>
			</div>
		</div>
	</article>
</div>

<?php get_footer(); ?>
