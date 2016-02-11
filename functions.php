<?php 

define( 'PARENT_URI', get_template_directory_uri() );
define( 'CHILD_URI', get_stylesheet_directory_uri() );


// include( STYLESHEETPATH . '/inc/shortcode-menu-paginas.php' );
include dirname(__FILE__).'/inc/custom-profile.php';

/**
 *  Scripts and styles
 */
function theme_enqueue_styles() {

    // styles
    wp_enqueue_style( 'parent-style', PARENT_URI . '/style.css');
    wp_enqueue_style( 'child-style', CHILD_URI . '/css/style.css');
    
    //scripts
    wp_enqueue_script('jquery');
    wp_enqueue_script( 'flexslider', CHILD_URI . '/js/flexslider-min.js', 'jquery', 2.1);
    wp_enqueue_script( 'scripts', CHILD_URI . '/js/script.js', '', '');

    $var_pna['signup_url'] = get_bloginfo('url') . "/cadastro";
    $var_pna['login_url'] = wp_login_url( get_permalink() );

    wp_localize_script( 'scripts', 'pna', $var_pna );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );


/**
 * configurações do tema
 */
add_action( 'after_setup_theme', 'pda_theme_setup' );
function pda_theme_setup() {

    load_child_theme_textdomain( 'hemingway', get_stylesheet_directory() . '/languages' );

      // images sizes
    // add_image_size( 'cycle_destaque', 320, 250, array( 'bottom', 'left') );
    add_image_size( 'cycle_destaque', 600, 469, array( 'bottom', 'left') );
    add_image_size( 'index-image', 730, 410, true );

    // altera configurações da imagem padrão do header
    add_theme_support( 'custom-header', array(
        'default-image'          => '',
        'width'                  => 500,
        'height'                 => 250,
        'flex-height'            => true,
    ) );

}


/**
 * inicializações do tema
 */
add_action( 'init', 'pda_init' );
function pda_init() {

    $labels = array(
        'name'              =>  _x("Destaques", 'post type general name', 'planonacionaldasartes'),
        'singular_name'      => _x('Destaque', 'post type singular name', 'planonacionaldasartes'),
        'add_new'            => _x('Adicionar Novo', 'image', 'planonacionaldasartes'),
        'add_new_item'       => __('Adicionar nova Destaque'),
        'edit_item'          => __('Editar Destaque'),
        'new_item'           => __('Novo Destaque'),
        'view_item'          => __('Ver Destaque'),
        'search_items'       => __('Search Destaques'),
        'not_found'          => __('Nenhum Destaque Encontrado'),
        'not_found_in_trash' => __('Nenhum Destaque na Lixeira'),
         'parent_item_colon' => __( 'Parent Destaques' )
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'destaque' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 6,
        'supports'           => array( 'title', 'editor', 'excerpt', 'comments', 'thumbnail', 'custom-fields', 'revisions' ),
        'taxonomies'         => array('post_tag', 'category')
    );

    register_post_type( 'destaque',  $args);

    // suporte para resumos nas páginas
    add_post_type_support( 'page', 'excerpt' );

}

// registra sidebar
register_sidebar( array(
    'name'          => __( 'Footer Full' ),
    'id'            => 'footer-full',
    'description'   => 'Este espaço está localizado acima do footer e ocupa todo o container',
    'before_title'  => '<h3 class="widget-title">',
    'after_title'   => '</h3>',
    'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
    'after_widget'  => '</div><div class="clear"></div></div>'
) );

// Ocultando a Admin Bar
if( !is_user_logged_in())
    add_filter('show_admin_bar', '__return_false');


function custom_excerpt_length( $length ) {
    return 100;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );


// adiciona tags nas páginas
function tags_support_all() {
    register_taxonomy_for_object_type('post_tag', 'page');
}

// incluindo as tags em todas as queries
function tags_support_query($wp_query) {
    if ($wp_query->get('tag')) $wp_query->set('post_type', 'any');
}
// tag hooks
add_action('init', 'tags_support_all');
add_action('pre_get_posts', 'tags_support_query');


// plugin wp-side-comments
add_filter( 'wp_side_comments_container_css_selector', function( ) { return '.comment-container'; } );

add_filter( 'wp_side_comments_css_theme', function( ) { return CHILD_URI . '/wp-side-comments/default-theme.css'; } );

// engajamento redes sociais

add_filter('language_attributes', 'add_og_xml_ns');
function add_og_xml_ns($content) {
  return ' xmlns:og="http://ogp.me/ns#" ' . $content;
}
 
add_filter('language_attributes', 'add_fb_xml_ns');
function add_fb_xml_ns($content) {
  return ' xmlns:fb="https://www.facebook.com/2008/fbml" ' . $content;
}
 
// Set your Open Graph Meta Tags
function fbogmeta_header() {

    global $post;

    // encurtar a url
    if (is_single() || is_page() ) : ?>

        <?php

        //Check for post’s shortened URL. Used with twitter feedback.
        if( get_post_meta($post->ID, "short_url", true) != "") {
            
            //Short URL already exists, pull from post meta
            $short_url = get_post_meta( $post->ID, "short_url", true);
            
            }else{

                //No short URL has been made yet
                $full_url = get_permalink();
                $short_url = make_bitly_url( $full_url, 'json' );

                //Save generated short url for future views
                add_post_meta($post->ID, 'short_url', $short_url, true);
            }

            if( empty( $short_url ) )
               $short_url = $full_url;

        ?>

        <!-- Open Graph Meta Tags for Facebook and LinkedIn Sharing !-->
        <meta property="og:title" content="<?php the_title(); ?>"/>
        <meta property="og:url" content="<?php echo $short_url; ?>"/>
        <?php $fb_image = wp_get_attachment_image_src(get_post_thumbnail_id( get_the_ID() ), 'index-image'); ?>
       
        <?php if ($fb_image) : ?>
            <meta property="og:image" content="<?php echo $fb_image[0]; ?>" />
        <?php endif; ?>

        <meta property="og:type" content="<?php if (is_single() || is_page()) { echo "article"; } else { echo "website";} ?>" />

        <meta property="og:site_name" content="<?php bloginfo('name'); ?>"/>
        <!-- End Open Graph Meta Tags !-->
 
    <?php else : ?>

       <?php if ( get_theme_mod( 'hemingway_logo' ) ) : ?>
            <meta property="og:image" content="<?php echo esc_url( get_theme_mod( 'hemingway_logo' ) ); ?>" />
       <?php endif; ?> 
        
        <meta property="og:title" content="<?php echo esc_attr( get_bloginfo( 'title' ) ); ?>" />
        <meta property="og:description" content="<?php echo esc_attr( get_bloginfo( 'description' ) ); ?>" />
    
    <?php endif;
}
add_action('wp_head', 'fbogmeta_header',9);


/* Based on code from David Walsh – http://davidwalsh.name/bitly-php */
function make_bitly_url($url, $format = 'xml',$version = '2.0.1')
{
    //Set up account info
    $bitly_acess = '8ed57b784baaa9f6bbfb15ba72fe7bd6e5fb80eb';
    
    //create the URL
   // $bitly = 'http://api.bitly.com/shorten?version='.$version.'&longUrl='.urlencode($url).'&login='.$bitly_login.'&apiKey='.$bitly_api.'&format='.$format;
    $bitly = 'https://api-ssl.bitly.com/v3/shorten?access_token=' . $bitly_acess . '&longUrl='. urlencode($url) .'&format=' . $format;

    //get the url
    $response = file_get_contents($bitly);
    
    //parse depending on desired format
    if( $format == 'json')
    {
        $json = @json_decode($response, true);
        return $json['data']['url'];
    }
    else //For XML
    {
        $xml = simplexml_load_string($response);
        return 'http://bit.ly/'.$xml->results->nodeKeyVal->hash;
    }
}


function my_custom_shortlink_filter() {

    global $post;

    $short_url = get_post_meta( $post->ID, "short_url",true );

    if( !empty( $short_url ) ) {
        return $short_url;
    }

    return false;
}
add_filter('pre_get_shortlink','my_custom_shortlink_filter');



function addtoany_disable_sharing_on_page_template(){
    if ( is_page_template( 'template_side_comment.php' ) || is_page( 'enquetes' ) ) {
        return true;
    }
}
add_filter( 'addtoany_sharing_disabled', 'addtoany_disable_sharing_on_page_template' );


function pna_social_priorize( $pergunta_id, $pergunta ) {

    if ( function_exists( 'ADDTOANY_SHARE_SAVE_KIT' ) ) { 
        echo '<div class="addtoany_list_container">';
            ADDTOANY_SHARE_SAVE_KIT( array( 'linkname' => $pergunta, 'linkurl' => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI'] ) );
        echo '</div>';
    }

}
add_action( 'priorize_template_after', 'pna_social_priorize', 10, 2);


if ( ! function_exists( 'cdbr_pna_comment' ) ) :
function cdbr_pna_comment( $comment, $args, $depth ) {
    $GLOBALS['comment'] = $comment;
    switch ( $comment->comment_type ) :
        case 'pingback' :
        case 'trackback' :
    ?>
    
    <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
    
        <?php __( 'Pingback:', 'hemingway' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'hemingway' ), '<span class="edit-link">', '</span>' ); ?>
        
    </li>
    <?php
            break;
        default :
        global $post;
    ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
    
        <div id="comment-<?php comment_ID(); ?>" class="comment">
        
            <div class="comment-meta comment-author vcard">
                            
                <?php echo get_avatar( $comment, 120 ); ?>

                <div class="comment-meta-content">
                                            
                    <?php printf( '<cite class="fn">%1$s %2$s</cite>',
                        get_comment_author_link(),
                        ( $comment->user_id === $post->post_author ) ? '<span class="post-author"> ' . __( '(Post author)', 'hemingway' ) . '</span>' : ''
                    ); ?>
                    
                    <p><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ) ?>"><?php echo get_comment_date() . ' at ' . get_comment_time() ?></a></p>
                    
                </div> <!-- /comment-meta-content -->
                
            </div> <!-- /comment-meta -->

            <div class="comment-content post-content">
            
                <?php if ( '0' == $comment->comment_approved ) : ?>
                
                    <p class="comment-awaiting-moderation"><?php _e( 'Awaiting moderation', 'hemingway' ); ?></p>
                    
                <?php endif; ?>
            
                <?php comment_text(); ?>
                
                <div class="comment-actions">
                
                    <?php edit_comment_link( __( 'Edit', 'hemingway' ), '', '' ); ?>
                    
                    <?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'hemingway' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
                    
                    <div class="clear"></div>
                
                </div> <!-- /comment-actions -->
                
            </div><!-- /comment-content -->

        </div><!-- /comment-## -->
    <?php
        break;
    endswitch;
}
endif;


// filtra os comentários para mostrar o nome completo
add_filter( 'get_comment_author', 'modify_author_link', 10 );        
function modify_author_link( $display_name  ) {     
    $user_id = get_current_user_id();

    $user_name  = get_user_meta($user_id, 'user_name', true);
    
    if( !empty($user_name) )
        $display_name = $user_name; 

    return $display_name;                
}

//insere o nome completo nos comentários do side comments
add_filter( 'wp_side_comments_user_details', 'modify_author_comments', 9,1);        
function modify_author_comments( $userDetails  ) {

    $user_id = get_current_user_id();

    $user_name  = get_user_meta($user_id, 'user_name', true);
    $segmento   = get_user_meta($user_id, 'segmento', true);
    
    if( !empty($user_name) )
         $userDetails["name"] = $user_name;

    if( !empty($segmento) )
        $userDetails["segmento"] = $segmento;

    return $userDetails;         
}


add_filter( 'wp_side_post_comment_data', 'modify_post_comment_data', 9,1);        
function modify_post_comment_data( $comment ) {

    $user_id = $comment['authorID'];

    $segmento = get_user_meta($user_id, 'segmento', true);
    $tipo_manifestacao = get_user_meta($user_id, 'tipo_manifestacao', true);
    $nome_instituicao = get_user_meta($user_id, 'nome_instituicao', true);
    
    if( !empty($segmento) )
        $comment["authorSegmento"] = $segmento;

    if( !empty($tipo_manifestacao) ) 
        $comment["authorManifestacao"] = $tipo_manifestacao;

    if( !empty($nome_instituicao) ) {
        $comment["authorInstituicao"] = $nome_instituicao;
    }

    return $comment;         
}
