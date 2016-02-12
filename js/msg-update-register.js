(function($) {
    $(document).ready(function(e) {

    $('.logged-in p.commentable-section, .logged-in textarea#comment, .logged-in #submit').on('click', function(){
        $('#cdbr_dialog').remove();

        var msg_title = 'Atualizar cadastro';
        var msg_text = 'Para participar você deve completar o seu cadastro!';
        
        $.post(
            msg.ajaxurl, 
            {
                action: 'current_user_updated_profile'
            },
            function(response) {

                if( response == false){


                    $('<div id="cdbr_dialog"></div>').appendTo( $( "body" ) )
                      .html('<div id="dialog-confirms" title="'+msg_title+'"><p>'+msg_text+'</p></div');

                    $('#dialog-confirms').dialog({
                        resizable: false,
                        draggable: false,
                        modal: true,
                        closeOnEscape: false,
                        buttons: {
                            "Atualizar cadastro": function() {
                                $( this ).dialog( "close" );
                                $('#cdbr_dialog').remove();
                                window.location.replace(msg.cadastro_url);
                                return false;
                            },
                            "Depois": function() {
                              $( this ).dialog( "close" );
                               $('#cdbr_dialog').remove();
                              return false;
                            }
                        }
                    });        
                }
            });
        });       
    });
})(jQuery);