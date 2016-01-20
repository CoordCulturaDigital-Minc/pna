<?php 
/*
	Template Name: Inicial PNA
	
*/
 ?>

<?php get_header(); ?>

<div class="wrapper section-inner">
	
	<div class="content left">

		<?php 
			$args = array( 'post_type' => 'destaque', 'posts_per_page' => 3, 'post_count' => 3 );
			$cycle = new WP_Query( $args );
		?>
			<?php if ( $cycle->have_posts() ) : ?>

				<div class="section section-cycle found-<?php echo $cycle->found_posts; ?>">
					<div class="section-body">
						<?php while ( $cycle->have_posts() ) : $cycle->the_post(); ?>
							<?php $useds[] = get_the_ID(); ?>
							<div class="post">
								<div class="thumb">
									<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_post_thumbnail( 'cycle_destaque' ); ?></a>
								</div>

								<div class="post-head">
									<h1 class="post-title"><a href="<?php the_permalink(); ?>" title="<?php print the_title(); ?>"><?php echo get_the_title(); ?></a></h1>
									<h2 class="post-subtitle"><a href="<?php the_permalink(); ?>" title="<?php print the_title(); ?>"><?php echo get_the_excerpt(); ?></a></h2>
								</div>
							</div>
						<?php endwhile; ?>
					</div>
				</div>
				<div class="clear"></div>

			<?php endif; ?>

		<?php //parse_str( $query_string, $query_array ); ?>
		<?php //$query_array[ 'post__not_in' ] = $useds; ?>
		<?php //$query_array[ 'showposts' ] = '3'; ?>
		<?php //query_posts( $query_array ); ?>

		<div class="posts" style="margin-bottom: 20px;">
			<div class="post">
				<div class="post-header"><h2 class="post-title">Participe da Política Nacional das Artes</h2></div>
				<div class="post-content">
					<p>O Ministério da Cultura quer conhecer profundamente a situação das artes no Brasil, levando em conta toda a pluralidade e diversidade de seu vasto território. Dê sua opinião!</p>
				</div>
			</div>
		</div>

		<?php if ( function_exists('priorize_print_pergunta') ): ?>
			<?php  priorize_print_pergunta("17589868"); ?>
		<?php endif; ?>

	</div> <!-- /content.left -->
		
	<?php get_sidebar(); ?>
	
	<div class="clear"></div>

</div> <!-- /wrapper -->
	              	        
<?php get_footer(); ?>
