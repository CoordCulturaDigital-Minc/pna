<?php 

define( 'PARENT_URI', get_template_directory_uri() );
define( 'CHILD_URI', get_stylesheet_directory_uri() );


// include( STYLESHEETPATH . '/inc/shortcode-menu-paginas.php' );
include dirname(__FILE__).'/inc/custom-profile.php';
// include dirname(__FILE__).'/inc/general-comments.php';

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
    $var_pna['is_administrator'] = current_user_can('administrator');
    
    wp_localize_script( 'scripts', 'pna', $var_pna );

    if ( is_single() || is_page() ) {

         wp_enqueue_script('jquery-ui-dialog');

        //se o usuário ainda nao atualizou o cadastro
        if( function_exists('cdbr_current_user_updated_profile')) {
            if( !cdbr_current_user_updated_profile() ) {
                wp_enqueue_script('msg-update-register', CHILD_URI . '/js/msg-update-register.js');
                wp_localize_script('msg-update-register', 'msg', array( 'ajaxurl' => admin_url('admin-ajax.php'), 'cadastro_url' =>  get_bloginfo('url') . "/cadastro"));
                wp_deregister_script('terms-of-use');
            }else {
                wp_enqueue_script('msg-guide-1', CHILD_URI . '/js/msg-guide.js');
                wp_localize_script('msg-guide-1', 'msg', array( 'ajaxurl' => admin_url('admin-ajax.php'), 'termos_url' =>  get_bloginfo('url') . "/termos-de-uso", 'comments_gerais_url' => get_permalink() . "?comments=general"));

            }
        }

        


       

    }
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );


function cdbr_admin_enqueue_scripts( $hook ){
    // if ( 'edit-comments.php' != $hook ) {
    //     return;
    // }

    wp_enqueue_script('move-comment', CHILD_URI . '/js/move-comment.js');
    // wp_localize_script('move-comment', 'move', array( 'ajaxurl' => admin_url('admin-ajax.php'));
}
add_action( 'admin_enqueue_scripts', 'cdbr_admin_enqueue_scripts' );

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

add_filter( 'wp_side_comments_avatar_size', function( ) { return 55; } );

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
add_filter( 'get_comment_author', 'modify_author_link', 10,2 );        
function modify_author_link( $display_name, $comment_ID  ) { 

    $user_id = get_comment($comment_ID)->user_id;;

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
    
    if( !empty($user_name) )
         $userDetails["name"] = $user_name;

    return $userDetails;         
}

// Acrescenta campos nos comentários por parágrafo.
add_filter( 'wp_side_post_comment_data', 'modify_post_comment_data', 9,1);        
function modify_post_comment_data( $comment ) {

    $user_id = $comment['authorID'];

    $segmento = get_user_meta($user_id, 'segmento', true);
    $tipo_manifestacao = get_user_meta($user_id, 'tipo_manifestacao', true);
    $nome_instituicao = get_user_meta($user_id, 'nome_instituicao', true);
    
    if( !empty($segmento) )
        $comment["authorSegmento"] = cdbr_get_label_segmento($segmento);

    if( !empty($tipo_manifestacao) ) 
        $comment["authorManifestacao"] = ucwords($tipo_manifestacao);

    if( !empty($nome_instituicao) ) {
        $comment["authorInstituicao"] = $nome_instituicao;
    }

    return $comment;         
}

function cdbr_type_comment_columns( $columns )
{
    return array_merge( $columns, array(
        'custom_type_comment' => __( 'Tipo comentário' ),
    ) );
}
add_filter( 'manage_edit-comments_columns', 'cdbr_type_comment_columns' );

function cdbr_comment_type_column( $column, $comment_ID )
{
    switch ( $column ) {
        case 'custom_type_comment':
            if ( $type = get_comment_type( $comment_ID ) ) {
                switch ($type) {
                    case 'comment':
                        echo 'Comentário padrão';
                        break;
                    case 'side-comment':
                        echo 'Comentário por parágrafo';
                        break;
                    case 'general-comment':
                        echo 'Comentário geral';
                        break;    
                    default:
                        echo $type;
                        break;
                }
            } else {
                echo "Padrão";
            }
       break;
    }
}
add_filter( 'manage_comments_custom_column', 'cdbr_comment_type_column', 10, 2 );

// add_filter( 'manage_book_posts_columns', 'set_custom_edit_book_columns' );
// add_action( 'manage_book_posts_custom_column' , 'custom_book_column', 10, 2 );


function cdbr_comment_row_actions($actions, $comment) {
   
    if( $comment->comment_type !== 'general-comment') {
        $url = "comment.php?c=$comment->comment_ID";

        $nonce = wp_create_nonce( "move-comment_$comment->comment_ID" );
        $move_nonce = esc_html( '_wpnonce=' . $nonce  );

        $move_url = esc_url( $url . "&action=movecomment&$move_nonce" );
        
        $actions['move'] = "<a href='$move_url' nonce='$nonce' data-comment-id='$comment->comment_ID' data-wp-lists='move:the-comment-list:comment-$comment->comment_ID::move=1' class='move' title='" . esc_attr__( 'Mover este comentário para comentários gerais' ) . "'>" . _x( 'Mover', 'verb' ) . '</a>';
    }

    return $actions;

}
add_filter('comment_row_actions', 'cdbr_comment_row_actions',10,2);

/**
 * Ajax handler for deleting a comment.
 *
 * @since 3.1.0
 */
function cdbr_ajax_move_comment() {

    $id = isset( $_POST['comment_ID'] ) ? (int) $_POST['comment_ID'] : 0;

    // echo $id;
    if ( !$comment = get_comment( $id, ARRAY_A ) )
        wp_die( time() );

    if ( ! current_user_can( 'edit_comment', $comment['comment_ID'] ) )
        wp_die( -1 );

    check_ajax_referer( "move-comment_$id", 'nonce' );

    $comment['comment_type'] = 'general-comment';

    $hasUpdated = wp_update_comment( $comment );

    if( $hasUpdated == 1 )
    {   
         // Setup our data which we're echoing
        $result = array(
            'type' => 'success'
        );

        $side_comment_section = get_comment_meta( $id, 'side-comment-section', true );

        if( !empty($side_comment_section ) ) {
            update_comment_meta( $id, 'side-comment-section_', $side_comment_section);
            delete_comment_meta( $id,'side-comment-section', $side_comment_section );
        }
        // enviar o email
        cdbr_send_email_comment_moved($comment);
    }
    else
    {   
        $result = array(
            'type' => 'failure',
            'message' => __( 'Erro ao mover comentário' . $hasUpdated)
        );

    }

    $result = json_encode( $result );
    echo $result;
    die();
}
add_action ( 'wp_ajax_move_comment', 'cdbr_ajax_move_comment');


function cdbr_send_email_comment_moved( $comment ) {

    if( empty( $comment ) )
        return false;

    $user_name          = $comment->comment_author;
    $user_email         = $comment->comment_author_email;
    $comment_content    = $comment->comment_content;
    $comment_date       = $comment->comment_date;

    $date = new DateTime($comment_date);
    $comment_date = $date->format('d/m/Y \à\s H:i:s');

    $link_termos_de_uso              = get_bloginfo('url') . "/termos-de-uso";
    $link_commentarios_gerais        = get_permalink($comment->comment_post_ID) . "?comments=general";
    $link_entenda_mais               = get_bloginfo('url') . "/entenda-mais";

    $from = get_option('admin_email');
    $headers = 'From: '.$from . "\r\n";
    $subject = "Participação - " . get_bloginfo('name');

    $msg =  "<p>Prezado " . $user_name . ",</p>"
            . "<p>Em observância ao disposto no item 7 dos <a href='" .  $link_termos_de_uso . "'>Termos de Uso</a>, as mensagens publicadas na plataforma " 
            . "da consulta devem obedecer ao escopo e ao objetivo da Consulta Pública, mantendo-se dentro do "
            . "assunto específico em que estão inseridas.</p>"
            . "<p>As mensagens que não digam respeito ao dispositivo comentado devem ser realizadas no campo " 
            . "'Comentários Gerais'</p>"
            . "<p>Sendo assim, informamos que seu comentário foi movido para o campo <a href='".$link_commentarios_gerais."'>“Comentários de caráter geral”</a></p>"
            . "<p>Para entender porque seu comentário foi movido <a href='$link_entenda_mais'>clique aqui</a>.</p>"
            . "<p>Comentário movido: " . $comment_content . "</p>"
            . "<p>Data do comentário: " . $comment_date . "</p>"
            . "<p></p>";

     echo $msg;

    wp_mail( $user_email, $subject, $msg, $headers );
}



### mostrar página com os comentários gerais ###
add_filter('query_vars', 'cdbr_comments_variables');
function cdbr_comments_variables($public_query_vars) {
    $public_query_vars[] = 'comments';
    return $public_query_vars;
}


###
function cdbr_general_comments()
{
    if(get_query_var('comments') == 'general' )
    {
             
        include(dirname(__FILE__).'/inc/general-comments.php');
        exit();
    }
}
add_action('template_redirect', 'cdbr_general_comments', 5);