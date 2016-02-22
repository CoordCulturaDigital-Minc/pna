<?php 


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
                        echo 'Comentário Geral';
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

    $user_name          = $comment['comment_author'];
    $user_email         = $comment['comment_author_email'];
    $comment_content    = $comment['comment_content'];
    $comment_post_ID    = $comment['comment_post_ID'];
    $comment_date       = $comment['comment_date'];

    $date = new DateTime($comment_date);
    $comment_date = $date->format('d/m/Y \à\s H:i:s');

    $link_termos_de_uso              = get_bloginfo('url') . "/termos-de-uso";
    $link_commentarios_gerais        = get_permalink($comment_post_ID) . "?comments=general";
    $link_entenda_mais               = get_bloginfo('url') . "/entenda-mais";

    $from = get_option('admin_email');
    $headers = 'Content-type: text/html'. "\r\n"; 
    $headers .= 'From: '.$from . "\r\n";
    $subject = "Participação - " . get_bloginfo('name');

    $msg_header = "<html><body>";
    $msg_content =  "<p>Prezado " . $user_name . ",</p>"
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
    $msg_footer = "</body></html>";

    $msg = $msg_header . $msg_content . $msg_footer;

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
             
        include(dirname(__FILE__).'/general-comments.php');
        exit();
    }
}
add_action('template_redirect', 'cdbr_general_comments', 5);

 ?>