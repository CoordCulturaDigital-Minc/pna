<?php get_header(); ?>

<div class="wrapper section-inner">						

	<div class="content full-width">
	
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			
		<div <?php post_class(); ?>>
	
			<div class="post">
			
				<?php if ( has_post_thumbnail() ) : ?>
					
					<div class="featured-media">
					
						<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>">
						
							<?php the_post_thumbnail('post-image'); ?>
							
							<?php if ( !empty(get_post(get_post_thumbnail_id())->post_excerpt) ) : ?>
											
								<div class="media-caption-container">
								
									<p class="media-caption"><?php echo get_post(get_post_thumbnail_id())->post_excerpt; ?></p>
									
								</div>
								
							<?php endif; ?>
							
						</a>
								
					</div> <!-- /featured-media -->
						
				<?php endif; ?>
													
				<div class="post-header">
											
				    <h2 class="post-title">Comentários Gerais: <?php the_title(); ?></h2>
				    				    
			    </div> <!-- /post-header -->
			   				        			        		                
	
			</div> <!-- /post -->
			
			<?php if ( comments_open() ) : ?>
			
				<?php comments_template( '/comments-general.php', true ); ?>
			
			<?php endif; ?>
		
		</div> <!-- /posts -->
		
		<?php endwhile; else: ?>

			<p><?php _e("We couldn't find any posts that matched your query. Please try again.", "hemingway"); ?></p>
	
		<?php endif; ?>
	
	</div> <!-- /content -->
	
</div> <!-- /wrapper section-inner -->
								
<?php get_footer(); ?>