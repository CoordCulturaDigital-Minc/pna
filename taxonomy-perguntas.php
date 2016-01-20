<?php get_header(); ?>

<div class="wrapper section-inner">
	
		<div class="content left">
			<div class="section-header">                    
            	<h2 class="section-title">Enquete</h2>
            </div>
				
			<div class="posts">
			
				<div class="page-title">
	
				<?php //_e( 'Enquetes', 'hemingway' ); 

					echo category_description(); ?>


			
				</div> <!-- /page-title -->
				
				<div class="clear"></div>

				<div class="post">

					<?php if ( function_exists('priorize_print_pergunta') ): ?>

						<?php  $queried_object = get_queried_object(); ?>

						<?php  priorize_print_pergunta( $queried_object->term_id ); ?>
					<?php endif; ?>
					
					<div class="clear"></div>
					
				</div> <!-- /post -->
							
			</div> <!-- /posts -->

		</div> <!-- /content -->
	
	<?php get_sidebar(); ?>
	
	<div class="clear"></div>

</div> <!-- /wrapper -->

<?php get_footer(); ?>