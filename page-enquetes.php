<?php get_header(); ?>

<div class="wrapper section-inner enquetes">						

	<div class="content left">

		<div>

           <?php 
            	$tag_description = tag_description();
					if ( ! empty( $tag_description ) )
						echo apply_filters( 'tag_archive_meta', '<div class="tag-archive-meta">' . $tag_description . '</div>' );
             ?>                   
        </div>
	
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			
		<div class="posts">

			<div class="section-header">                    
            	<h2 class="section-title"><?php the_title(); ?></h2>
            </div>
							   				        			        		                
			<div class="section-description">
						                                        
				<?php the_content(); ?>
													            			                        
			</div>
								

			<div>
			
				<div class="clear"></div>
				<?php 

					$args = array( 'hide_empty=0' );

					$terms = get_terms( 'perguntas', $args );

					if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {

					    foreach ( $terms as $term ) :  ?>

					    	<div class="post">

								<?php if ( function_exists('priorize_print_pergunta') ): ?>
									<?php  priorize_print_pergunta( $term->term_id ); ?>
								<?php endif; ?>
	    						
						   </div>

						<?php endforeach;					
					}
				?>
		
			</div> <!-- /posts -->	
			
		
		</div> <!-- /posts -->
		
		<?php endwhile; endif; ?>
	
		<div class="clear"></div>
		
	</div> <!-- /content left -->
	
	<?php get_sidebar(); ?>
	
	<div class="clear"></div>

</div> <!-- /wrapper -->
								
<?php get_footer(); ?>