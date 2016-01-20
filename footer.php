
	<div class="section footer-full">
		<div class="section-inner">
			<?php if ( is_active_sidebar( 'footer-full' ) ) : ?>
				
				<div class="">
				
					<div class="widgets">
			
						<?php dynamic_sidebar( 'footer-full' ); ?>
											
					</div>
											
				</div>
			<?php endif; ?> <!-- /footer-a -->
		</div>
	</div>

	<div class="footer bg-dark">
		
		<div class="footer-inner section-inner">
		
			<?php if ( is_active_sidebar( 'footer-a' ) ) : ?>
			
				<div class="column column-1 left">
				
					<div class="widgets">
			
						<?php dynamic_sidebar( 'footer-a' ); ?>
											
					</div>
										
				</div>
				
			<?php endif; ?> <!-- /footer-a -->
				
			<?php if ( is_active_sidebar( 'footer-b' ) ) : ?>
			
				<div class="column column-2 left">
				
					<div class="widgets">
			
						<?php dynamic_sidebar( 'footer-b' ); ?>
											
					</div> <!-- /widgets -->
					
				</div>
				
			<?php endif; ?> <!-- /footer-b -->
								
			<?php if ( is_active_sidebar( 'footer-c' ) ) : ?>
			
				<div class="column column-3 left">
			
					<div class="widgets">
			
						<?php dynamic_sidebar( 'footer-c' ); ?>
											
					</div> <!-- /widgets -->
					
				</div>
				
			<?php endif; ?> <!-- /footer-c -->
			
			<div class="clear"></div>
		
		</div> <!-- /footer-inner -->
	
	</div> <!-- /footer -->
	
	<div class="credits section bg-dark no-padding">
	
		<div class="credits-inner section-inner">
	
			<p class="credits-left">
			
				&copy; <?php echo date("Y") ?> <a href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a>
			
			</p>
			
			<p class="credits-right">
				
				<span><?php printf( __( 'Theme by <a href="%s">Cultura Digital</a></br> baseado no tema de <a href="%s">Anders Noren</a>', 'hemingway'), 'http://culturadigital.br', 'http://www.andersnoren.se' ); ?></span>  <a title="<?php _e('To the top', 'hemingway'); ?>" class="tothetop"><span> </span>topo</a>
				
			</p>
			
			<div class="clear"></div>
		
		</div> <!-- /credits-inner -->
		
	</div> <!-- /credits -->

</div> <!-- /big-wrapper -->

<?php wp_footer(); ?>

<script defer="defer" src="//barra.brasil.gov.br/barra.js" type="text/javascript"></script>
</body>
</html>