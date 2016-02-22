<?php
/*
Template Name: Page comments
*/
?>

<?php get_header(); ?>

<div class="wrapper section-inner">		

	<span class="comment-sucess hidden">Comentário postado com sucesso! Agradecemos sua participação</span>				

	<div class="content comments-width">
		
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			

			<?php 

			$parent_id = $post->post_parent;


			// if( $parent_id == 0  ) 
				//$parent_id = get_the_ID();
			?>

			<?php if( $parent_id !== 0 ) :  ?>

				<?php $page_parent = get_post( $parent_id ); ?>

				<div class="page-parent">
														
					<div class="post-header">
					     <?php if ( current_user_can( 'manage_options' ) ) : ?>
																		
							<p><?php edit_post_link( __('Edit', 'hemingway') ); ?></p>
						
						<?php endif; ?>
												
					    <h2 class="post-title"><?php print $page_parent->post_title; ?></h2>

				    
				    </div> <!-- /post-header -->
						        		                
					<div class="post-content">
								                                        
						<?php  echo apply_filters('the_content', $page_parent->post_content ); ?>
						
						<div class="clear"></div>
															            			                        
					</div> <!-- /post-content -->

				</div> <!-- /post -->
			
	
				<div <?php post_class(); ?>>
														
					<div class="post-header">
												
					    <!-- <h2 class="post-title"><?php the_title(); ?></h2> -->
					    <?php 
					    $current_page_id = get_the_ID();

						$parent = $post->post_parent;

						if( $parent == 0 )
							$parent = $current_page_id;
						
						?>

					    <?php $parent_pages = get_pages( array( 'parent' => $parent, 'sort_column' => 'menu_order', 'sort_order' => 'asc', 'number' => '6' ) ); ?>

			            <ul id="menu-abas" class="itens">
							
							<?php foreach( $parent_pages as $key => $page ) {	
								
								$class = null;

								if ( $page->ID == get_the_ID() )
									$class = "current"; ?>
							
								<li class='item <?php echo $class; ?>'>
									<a href="<?php echo get_page_link( $page->ID ); ?><?php echo ($key==0) ? '?init=true' : ''; ?>"><?php echo $page->post_title; ?></a>
			                    </li>  

							<?php } ?>

						</ul>
					    				    
				    </div> <!-- /post-header -->
			<?php else: ?>

					<div class="post-header">
												
					    <h2 class="post-title"><?php the_title(); ?></h2>
					    <div class="post-excerpt"><?php echo $post->post_excerpt; ?></div>
 
				    </div> <!-- /post-header -->

			<?php endif; ?>
       			        		                
				<div class="comment-container post-content">

					<?php the_content(); ?>

					<?php if ( function_exists( 'ADDTOANY_SHARE_SAVE_KIT' ) ) { ADDTOANY_SHARE_SAVE_KIT(); } ?>
														            			                        
				</div> <!-- /post-content -->

	
			</div> <!-- /post -->
			
			<?php if ( comments_open() ) : ?>

				<?php global $PNAThemeOptions;
				if( $PNAThemeOptions->isPopupMsgAllowed() ) : ?>
					<div class="general-comments">
						<p>As mensagens que não digam respeito ao dispositivo comentado devem ser realizadas no campo <a href="<?php echo get_permalink() ?>?comments=general">“Comentários de caráter geral”</a></p>
					</div>
				<?php endif; ?>

				<?php comments_template( '/comments-page.php', true ); ?>
			
			<?php endif; ?>
		
		</div> <!-- /posts -->
		
		<?php endwhile; else: ?>

			<p><?php _e("We couldn't find any posts that matched your query. Please try again.", "hemingway"); ?></p>
	
		<?php endif; ?>
	
	</div> <!-- /content -->
	
</div> <!-- /wrapper section-inner -->
								
<?php get_footer(); ?>