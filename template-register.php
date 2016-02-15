<?php
/*
	Template Name: Página de cadastro
*/

	global $user_ID;
	
	$disabled = "";

	wp_enqueue_script('jquery-ui-dialog');
	wp_enqueue_script('jquery-mask', get_stylesheet_directory_uri() . '/js/jquery.mask.min.js', array('jquery'));
	wp_enqueue_script('cadastro', get_stylesheet_directory_uri() . '/js/cadastro.js', array('jquery'));
	wp_localize_script('cadastro', 'vars', array( 'ajaxurl' => admin_url('admin-ajax.php'), 'admin_email' => get_option('admin_email') ) );


	// se o usuário já possuir cadastro, deverá apenas atualizar os campos não cadastrados
	if ($user_ID) {

		$cdbr_user = cdbr_get_user_all_data($user_ID);

		$user_name 					= isset( $cdbr_user['user_name'] ) ? $cdbr_user['user_name'] : $_POST['user_name'];

		$user_cpf					= isset( $cdbr_user['user_cpf'] ) ? $cdbr_user['user_cpf'] : $_POST['user_cpf'];
		$tipo_manifestacao			= isset( $cdbr_user['tipo_manifestacao'] ) ? $cdbr_user['tipo_manifestacao'] : $_POST['tipo_manifestacao']; 
		$nome_instituicao			= isset( $cdbr_user['nome_instituicao'] )  ? $cdbr_user['nome_instituicao'] : $_POST['nome_instituicao'];
		$cnpj_instituicao			= isset( $cdbr_user['cnpj_instituicao'] )  ? $cdbr_user['cnpj_instituicao'] : $_POST['cnpj_instituicao'];
		
		$estado 					= isset( $cdbr_user['estado'] )  	? $cdbr_user['estado'] : $_POST['estado'];
		$municipio 					= isset( $cdbr_user['municipio'] )  ? $cdbr_user['municipio'] : $_POST['municipio'];

		$segmento 					= isset( $cdbr_user['segmento'] )  ? $cdbr_user['segmento'] : $_POST['segmento'];

		$accept_the_terms_of_site 	= ( cdbr_get_user_terms_current_site($user_ID) ) ? cdbr_get_user_terms_current_site($user_ID) : $_POST['accept_the_terms_of_site'];
		
		$disabled = "disabled";
	}

	//Check whether the user is already logged in
	if(!$user_ID) {

		if($_POST) {
			$register_errors = array();
			$data 			 = array();

			$user_login 				= sanitize_user($_POST['user_login']);
			$user_name 					= $_POST['user_name'];
			$user_email 	  			= esc_sql($_POST['user_email']);
			// $user_password 				= $_POST['user_password'];
			// $user_password_confirm  	= $_POST['user_password_confirm'];
			$user_cpf					= $_POST['user_cpf'];
			
			$tipo_manifestacao			= $_POST['tipo_manifestacao']; //individual or institucional
			$nome_instituicao			= isset( $_POST['nome_instituicao'] ) ? $_POST['nome_instituicao'] : '';
			$cnpj_instituicao			= isset( $_POST['cnpj_instituicao'] ) ? $_POST['cnpj_instituicao'] : '';
			
			$estado 					= isset( $_POST['estado'] ) ? $_POST['estado'] : '';
			$municipio 					= isset( $_POST['municipio'] ) ? $_POST['municipio'] : '';

			$segmento 					= $_POST['segmento'];

			$accept_the_terms_of_site 	= $_POST['accept_the_terms_of_site'];
		}

		$disabled = "";
	}		
		
	if($_POST) {

		if(!$user_ID) {

			// user_login
			if(empty($user_name)) {
				$register_errors['user_name'] = "Nome de usuário não pode ser vazio.";
			}

			// user_name
			if(empty($user_name)) {
				$register_errors['user_name'] = "Nome completo/Razão Social não pode ser vazio.";
			}

			// user_email
			if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $user_email)) {
				$register_errors['user_email'] =  "Por favor, informe um email válido.";
			}

			// password
	        // if(strlen($user_password)==0 )
	            // $register_errors['pass'] = 'A senha é obrigatória para a inscrição no site.';
	        
	        // if( $user_password != $user_password_confirm)
	            // $register_errors['pass_confirm'] = 'As senhas informadas não são iguais.';
		}

		// user_cpf or cnpj
		if(empty($user_cpf)) {
			$register_errors['user_cpf'] = "CPF é obrigatório";
		}elseif(cdbr_is_a_valid_cpf($user_cpf)!==true) {
			$register_errors['user_cpf'] = "CPF informado é inválido";
		}elseif( !cdbr_user_cpf_does_not_exist($user_cpf) ) {
			$register_errors['user_cpf'] = 'Já existe um usuário cadastrado com este CPF. <a href="' . wp_lostpassword_url() .'">Recuperar senha?</a>';
		}

		//tipo de manifestacao
		if( empty($tipo_manifestacao)) {
			$register_errors['manifestacao'] = "Tipo de manifestação é obrigatório.";
		}elseif( $tipo_manifestacao == 'institucional' ) {

			// Nome instituicao / Razao social
			if(empty($nome_instituicao)) {
				$register_errors['nome_instituicao'] = "O nome da instituicao é obrigatório.";
			}

			// CNPJ instituicao - TODO: verificar se CNPJ é obrigatório
			if( empty($cnpj_instituicao )) {
				$register_errors['cnpj_instituicao'] = "O CNPJ da instituicao é obrigatório.";
			}
			else if(!cdbr_is_a_valid_cnpj($cnpj_instituicao)) {
				$register_errors['cnpj_instituicao'] = "CNPJ informado é inválido";
			}
		}	

		//tipo de segmento
		if(empty($segmento)) {
			$register_errors['segmento'] = "Tipo de segmento é obrigatório.";
		}

		// estado
		if(empty($estado)) {
			$register_errors['estado'] = "Estado é obrigatório.";
		}

		// município
		if(empty($municipio)) {
			$register_errors['municipio'] = "Município é obrigatório.";
		}


	    // termos de uso
		if(empty($accept_the_terms_of_site)) {
			$register_errors['accept_the_terms_of_site'] = "Você deve concordar com os termos de uso do site.";
		}

	    // check and register
		if(!sizeof($register_errors)>0) {

			// se o usuário não tiver cadastrado, criar um novo
			if(!$user_ID) { 

				//gera a senha para envio por email
				$user_password = wp_generate_password( 8, false );

				$data['user_login'] = $user_login;
	            $data['user_pass'] = $user_password;
	            $data['user_email'] =  $user_email;
	            $data['first_name'] = $user_name;
	            $data['display_name'] = $user_name; 

	            $data['role'] = 'subscriber' ;
	            
	            $user_id = wp_insert_user($data);
	        }else {
	        	$user_id = $user_ID;
	        }

			if ( is_wp_error($user_id) ) {
				$register_errors['create'] = $user_id->get_error_message();
			} else {

				if(!$user_ID) { // se for um cadastro novo
					// enviar um email com informações da conta, envia senha por email
					cdbr_send_email_register( $user_email, $user_login, $user_password );
				}

				// salva os metadados
				update_user_meta($user_id, 'tipo_manifestacao', $tipo_manifestacao);
				update_user_meta($user_id, 'user_cpf', $user_cpf);
				update_user_meta($user_id, 'user_name', $user_name);
				update_user_meta($user_id, 'segmento', $segmento);

				// razao social é opcional
				if( !empty($nome_instituicao))
					update_user_meta($user_id, 'nome_instituicao', $nome_instituicao);

				// cnpj é opcional
				if( !empty($cnpj_instituicao))
					update_user_meta($user_id, 'cnpj_instituicao', $cnpj_instituicao);

				// salva os metados do buddypress - cidade e estado sao opcionais
				if( !empty($municipio) )
					cdbr_add_user_meta($user_id, 'cidade', $municipio);
				
				if( !empty( $estado) )
					cdbr_add_user_meta($user_id, 'estado', $estado);

				// termos de uso
				cdbr_update_user_terms_current_site($user_id);

				// adiciona o usuário no blog principal
				add_user_to_blog('1', $user_id, 'subscriber' );

				if(!$user_ID) { // mensagem para novo cadastro
					wp_safe_redirect($_SERVER['REQUEST_URI'] . '?sussa=success_new_register');
					 exit();
			    }else { // mensagem para atualização do cadastro
			    	wp_safe_redirect($_SERVER['REQUEST_URI'] . '?sussa=success_update_register');
			    	 exit();

			    }
			}
		}
	} 

get_header();

/*
 * se o usuário já estiver cadastro no culturadigital e já tiver os dados neste site
 * o sistema não deve mostrar o formulário novamente.
*/

$sussa =  isset( $_GET['sussa'] ) ? $_GET['sussa'] : "";

if( cdbr_current_user_updated_profile() ) { ?>
	<div class="wrapper section-inner">						
		<div class="content">
		    <?php if( $sussa == 'success_update_register' ) :  ?>
				<div class="success"><strong>Seu cadastro foi atualizado com sucesso! </strong><br>Para participar basta navegar nas opções no menu e deixar suas opiniões.</div>			
			<?php else: ?>
				<div class="success"><strong>Você já está cadastrado! </strong><br>Para participar basta navegar nas opções no menu e deixar suas opiniões.</div>
			<?php endif; ?>
		</div>
	</div>
<?php } elseif( !$user_ID && $sussa == 'success_new_register') {  ?> 
	<div class="wrapper section-inner">						
		<div class="content">       			        		                
			<div class="success"><strong>Você foi cadastrado com sucesso! </strong>
			<br>Uma senha foi gerada e enviada para o email informado no seu cadastro!
			<br>Acesse seu email e depois faça <a href="<?php echo wp_login_url( $redirect ); ?>">login</a> para participar!</div>
		</div>
	</div>		
<?php	
} else {
	 ?>
	<div class="wrapper section-inner">						

		<div class="content">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class("post"); ?>>

					<div class="post-header">							
					    <h2 class="post-title"><?php the_title(); ?></h2>
				    </div> 
				   				        			        		                
					<div class="post-content">
								                                        
						<?php the_content(); ?>

					</div>

					<?php if( isset($register_errors) ) : ?>
						<?php if (is_array($register_errors) && sizeof($register_errors) > 0): ?>
							<div class='messages'>
								<?php foreach ($register_errors as $e): ?>
									<div class="error"><?php echo $e; ?></div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					<?php endif; ?>

					<?php if( $user_ID ) : ?>
						<div class="success">Para participar desta consulta pública, você precisa atualizar o seu cadastro.</div>
					<?php endif; ?>

					<form method="post" id="register">

						<div class="form_item">
							<label for="user_login">Nome de usuário:</label>
							<span class="description">(Não inserir caracteres especiais e nem espaço)<span>
							<input id="user_login" type="text" required="required" name="user_login" class="text" value="<?php echo isset($user_login) ? $user_login : '';?>" <?php echo $disabled; ?> />
							<div id="user_login-error" class="field__error"></div>
						</div>

						<div class="form_item">
							<label for="user_email">Email:</label>
							<input id="user_email" type="text" required="required" name="user_email" class="text" value="<?php echo isset($user_email) ? $user_email : '';?>" <?php echo $disabled; ?> /> 
							<div id="user_email-error" class="field__error"></div>
						</div>

						<div class="form_item">
							<label for="user_name">Nome completo:</label>
							<input id="user_name" type="text" required="required" name="user_name" class="text" value="<?php echo isset($user_name) ? $user_name : '';?>" />
							<div id="user_name-error" class="field__error"></div>
						</div>

						<div class="form_item">
							<label for="user_cpf">CPF:</label>
							<span class="description">(<a href="#" class="nao_tenho_cpf">Não tenho CPF</a>)<span>
							<input id="user_cpf" type="text" required="required" name="user_cpf" class="text" value="<?php echo isset($user_cpf) ? $user_cpf : '';?>" />
							<div id="user_cpf-error" class="field__error"></div>
						</div>			
						
						<fieldset>
							<legend>Tipo de manifestação</legend>
							<div class="form_item">
								<label>
								  <input type="radio" name="tipo_manifestacao" value="individual" <?php if (isset($tipo_manifestacao) && $tipo_manifestacao == 'individual') echo 'checked'; ?>>
								  Individual
								</label>
								<label>
								  <input type="radio" name="tipo_manifestacao" value="institucional" <?php if (isset($tipo_manifestacao) && $tipo_manifestacao == 'institucional') echo 'checked'; ?>>
								  Institucional
								</label>
							<div id="tipo_manifestacao-error" class="field__error"></div>
							</div>

							<div id="instituicao" style="<?php echo empty($nome_instituicao) ? 'display:none': '';?>">
								<div class="form_item">
									<label for="nome_instituicao">Razão Social/Instituição:</label>
									<input id="nome_instituicao" type="text" name="nome_instituicao" class="text" value="<?php echo isset($nome_instituicao) ? $nome_instituicao : '';?>" />
									<div id="nome_instituicao-error" class="field__error"></div>
								</div>

								<div class="form_item">
									<label for="cnpj_instituicao">CNPJ:</label>
									<input id="cnpj_instituicao" type="text" name="cnpj_instituicao" class="text" value="<?php echo isset($cnpj_instituicao) ? $cnpj_instituicao : '';?>" />
									<div id="cnpj_instituicao-error" class="field__error"></div>
								</div>
							</div>
						</fieldset>

						<div class="form_item">
							<label for="estado">Estado:</label>
							<select id="estado" name="estado" id="estado">
	                            <option value=""> Selecione </option>
	                            <?php $states = cdbr_get_states(); ?>
	                            <?php foreach ($states as $s): ?>
	                                <option value="<?php echo $s->nome; ?>"  <?php if (isset($estado) && $estado == $s->nome) echo 'selected'; ?>  >
	                                    <?php echo $s->nome; ?>
	                                </option>
	                            <?php endforeach; ?>
	                        </select>
	                        <div id="estado-error" class="field__error"></div>
						</div>
					
						<div class="form_item">
							<label for="municipio">Município:</label>
							<select id="municipio" name="municipio" id="municipio">
	                            <option value="">Selecione</option>
	                        </select> 
	                        <div id="municipio-error" class="field__error"></div>
						</div>

						<div class="form_item">
							<label>Segmento:</label>
							<select required="required" name="segmento" id="segmento">
	                            <option value=""> Selecione </option>
	                            <?php $segmentos = cdbr_get_segmentos(); ?>
	                            <?php foreach ($segmentos as $key => $s ): ?>
	                                <option value="<?php echo $key; ?>"  <?php if (isset($segmento) && $segmento == $key) echo 'selected'; ?>>
	                                    <?php echo $s; ?>
	                                </option>
	                            <?php endforeach; ?>
	                        </select>
	                        <div id="segmento-error" class="field__error"></div>
						</div>

						<div class="form_item">
							<label>
							    <input type="checkbox" name="accept_the_terms_of_site" required="required" <?php print !empty($accept_the_terms_of_site) ? 'checked' : ''; ?>>
							    Li e concordo com os <a href="<?php echo site_url('/termos-de-uso/'); ?>">
							    termos de uso</a> do site
							 </label>
							 <div id="accept_the_terms_of_site-error" class="field__error"></div>
						</div>
						<br>

						<div class="textright">
							<input type="submit" id="submitbtn" class="blue-button"  name="submit" value="Registrar" />
						</div>
					</form>

				</div> <!-- end post -->
				<div class="clear"></div>
		<?php endwhile; endif; ?>
			
		</div> <!-- /content left -->
	</div>
	
	<?php  
} 

get_footer();

?>
