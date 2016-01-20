<div class="post-header">

	<?php $videourl = get_post_meta($post->ID, 'videourl', true); if ( $videourl != '' ) : ?>

		<div class="featured-media">
		
			<?php if (strpos($videourl,'.mp4') !== false) : ?>
				
				<video controls>
				  <source src="<?php echo $videourl; ?>" type="video/mp4">
				</video>
																		
			<?php else : ?>
				
				<?php 
				
					$embed_code = wp_oembed_get($videourl); 
					
					echo $embed_code;
					
				?>
					
			<?php endif; ?>
			
		</div>
	
	<?php endif; ?>
	
    <h2 class="post-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
    
    <div class="post-meta">
								
	</div>
    
</div> <!-- /post-header -->
									                                    	    
<div class="post-content">
	    		            			            	                                                                                            
	<?php the_content(); ?>
			
	<?php wp_link_pages(); ?>
				        
</div> <!-- /post-content -->
            
<div class="clear"></div>