<!DOCTYPE html>

<html <?php language_attributes(); ?>>

	<head>
		
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" >

		<!-- <link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/img/favicon.ico" /> -->
																		
		<title><?php wp_title('|', true, 'right'); ?></title>
				
		<?php if ( is_singular() ) wp_enqueue_script( "comment-reply" ); ?>

		<?php wp_head(); ?>
	
	</head>
	
	<body <?php body_class(); ?>>
		<div id="barra-brasil" style=""> 
			<ul id="menu-barra-temp" style="list-style:none;">
				<li style="display:inline; float:left;padding-right:10px; margin-right:10px; border-right:1px solid #EDEDED">
				<a href="http://brasil.gov.br" style="font-family:sans,sans-serif; text-decoration:none; color:white;">Portal do Governo Brasileiro</a></li> 
				<li>
					<a style="font-family:sans,sans-serif; text-decoration:none; color:white;" href="http://epwg.governoeletronico.gov.br/barra/atualize.html">Atualize sua Barra de Governo</a>
				</li>
			</ul>
		</div>

		<div class="big-wrapper">

			<div class="header-cover section bg-dark-light no-padding">
		
				<div class="header section" >
							
					<div class="header-inner section-inner">

						<?php if (  get_header_image() != '' ) : ?>
						
							<div class='blog-logo'>
							
						        <a href='<?php echo esc_url( home_url( '/' ) ); ?>' title='<?php echo esc_attr( get_bloginfo( 'title' ) ); ?> &mdash; <?php echo esc_attr( get_bloginfo( 'description' ) ); ?>' rel='home'>
						        	<img  src='<?php echo esc_url( header_image() ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'title' ) ); ?>'>
						        </a>
						        	
						    </div> <!-- /blog-logo -->

						    
							<div class="blog-search-topo">
				
								<?php get_search_form(); ?>
							
							</div>
					
						<?php elseif ( get_bloginfo( 'description' ) || get_bloginfo( 'title' ) ) : ?>
					
							<div class="blog-info">
							
								<h1 class="blog-title">
									<a href="<?php echo esc_url( home_url() ); ?>" title="<?php echo esc_attr( get_bloginfo( 'title' ) ); ?> &mdash; <?php echo esc_attr( get_bloginfo( 'description' ) ); ?>" rel="home"><?php echo esc_attr( get_bloginfo( 'title' ) ); ?></a>
								</h1>
								
								<?php if ( get_bloginfo( 'description' ) ) { ?>
								
									<h3 class="blog-description"><?php echo esc_attr( get_bloginfo( 'description' ) ); ?></h3>
									
								<?php } ?>

							
							</div> <!-- /blog-info -->
							
						<?php endif; ?>
									
					</div> <!-- /header-inner -->
								
				</div> <!-- /header -->
			
			</div> <!-- /bg-dark -->
			
			<div class="navigation section no-padding bg-dark">
			
				<div class="navigation-inner section-inner">
				
					<div class="toggle-container hidden">
			
						<div class="nav-toggle toggle">
								
							<div class="bar"></div>
							<div class="bar"></div>
							<div class="bar"></div>
							
							<div class="clear"></div>
						
						</div>
						
						<div class="search-toggle toggle">
								
							<div class="metal"></div>
							<div class="glass"></div>
							<div class="handle"></div>
						
						</div>
						
						<div class="clear"></div>
					
					</div> <!-- /toggle-container -->
					
					<div class="blog-search hidden">
					
						<?php get_search_form(); ?>
					
					</div>
				
					<ul class="blog-menu">
					
						<?php if ( has_nav_menu( 'primary' ) ) {
																			
							wp_nav_menu( array( 
							
								'container' => '', 
								'items_wrap' => '%3$s',
								'theme_location' => 'primary', 
								'walker' => new hemingway_nav_walker
															
							) ); } else {
						
							wp_list_pages( array(
							
								'container' => '',
								'title_li' => ''
							
							));
							
						} ?>
						
						<div class="clear"></div>
												
					 </ul>
					 
					 <ul class="mobile-menu">
					
						<?php if ( has_nav_menu( 'primary' ) ) {
																			
							wp_nav_menu( array( 
							
								'container' => '', 
								'items_wrap' => '%3$s',
								'theme_location' => 'primary', 
								'walker' => new hemingway_nav_walker
															
							) ); } else {
						
							wp_list_pages( array(
							
								'container' => '',
								'title_li' => ''
							
							));
							
						} ?>
						
					 </ul>
				 
				</div> <!-- /navigation-inner -->
				
			</div> <!-- /navigation -->