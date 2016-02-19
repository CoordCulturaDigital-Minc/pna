/* global adminCommentsL10n, thousandsSeparator, list_args, QTags, ajaxurl, wpAjax */
var setCommentsList, theList, theExtraList, commentReply;

(function($) {

	$(document).ready(function(){

		$('span.move a').on('click', function(){
			if (confirm("Tem certeza que deseja mover este comentário?") ) {

				var wpListsData = $(this).attr('data-wp-lists'), id = wpListsData.replace(/.*?comment-([0-9]+).*/, '$1');
 				var commentNonce   = $(this).attr("nonce");

				var args =  {
					'action': 'move_comment',
					'comment_ID' : id,
					'nonce': commentNonce,
				};

				$.ajax({
					url: ajaxurl,
					type:"POST",
					dataType: 'json',
					data: args,
					success: function(response) {
						if( response.type == 'success' ){
							$("#comment-"+ id + " .custom_type_comment").text("Comentário Geral");
							alert("Comentário movido com sucesso");
						}else {
							alert("Erro ao mover o comentário");
						}
					}
				});
			}

			return false;
		});		
	});
})(jQuery);
