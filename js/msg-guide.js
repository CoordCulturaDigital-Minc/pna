(function($) {
    $(document).ready(function(e) {

        $('#cdbr_dialog').remove();

        var link_termos_de_uso = msg.termos_url, link_comentarios_gerais = msg.comments_gerais_url;

        var msg_title = 'Atenção';
        var msg_text = 'As mensagens que não digam respeito ao dispositivo comentado devem ser realizadas no campo <a href="'+ msg.comments_gerais_url +'">“Comentários de caráter geral”</a><br><br><a href="'+ msg.termos_url +'">(Termos de Uso - item 7)</a>';
        
        $.post(
            msg.ajaxurl, 
            {
                action: 'current_user_guide_1'
            },
            function(response) {

                if( response == false){


                    $('<div id="cdbr_dialog"></div>').appendTo( $( "body" ) )
                      .html('<div id="dialog_guide_1" title="'+msg_title+'"><p>'+msg_text+'</p></div');

                    $('#dialog_guide_1').dialog({
                        resizable: false,
                        draggable: false,
                        modal: true,
                        closeOnEscape: false,
                        buttons: {
                            "Entendi": function() {
                                $( this ).dialog( "close" );
                                $('#cdbr_dialog').remove();
                                return false;
                            }
                        }
                    });        
                }
            });
              
    });
})(jQuery);