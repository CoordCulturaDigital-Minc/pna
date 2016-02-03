<?php
/*
	Template Name: Página de cadastro


• Podemos fazer alteração no formulário de cadastro da pessoa no Cultura Digital? Criar ou excluir campos específicos?
 
• Qual o campo no formulário que representa o nome que aparecerá nos comentários de determinada pessoa? Nome de usuário,...? É preciso que apareça sempre o nome completo da pessoa e não o apelido.
 
• Excluir o campo APELIDO*; endereço; CEP; Telefone; Celular; Biografia.
 
• É possível criar no início do formulário de cadastro as opções de pessoa física ou jurídica e depois criar campos para que se digite ou o CPF ou o CNPJ?
 
• É possível criar categorias na hora do cadastro? Empresa, Associação, Coletivo, Players, Editores, Usuário...?

*/

global $user_ID;

wp_enqueue_script('jquery-mask', get_stylesheet_directory_uri() . '/js/jquery.mask.min.js', array('jquery'));
wp_enqueue_script('cadastro', get_stylesheet_directory_uri() . '/js/cadastro.js', array('jquery'));
wp_localize_script('cadastro', 'vars', array( 'ajaxurl' => admin_url('admin-ajax.php') ));

//Check whether the user is already logged in
if (!$user_ID) {

	if($_POST) {
		$register_errors = array();
		$data 			 = array();

		$user_login 				= sanitize_user($_POST['user_login']);
		$user_name 					= $_POST['user_name'];
		$user_email 	  			= esc_sql($_POST['user_email']);
		$user_password 				= $_POST['user_password'];
		$user_password_confirm  	= $_POST['user_password_confirm'];
		$user_cpf					= $_POST['user_cpf'];
		
		$tipo_manifestacao			= $_POST['tipo_manifestacao']; //individual or institucional
		$nome_instituicao			= isset( $_POST['nome_instituicao'] ) ? $_POST['nome_instituicao'] : '';
		$cnpj_instituicao			= isset( $_POST['cnpj_instituicao'] ) ? $_POST['cnpj_instituicao'] : '';
		
		$pais 						= isset( $_POST['pais'] ) ? $_POST['pais'] : '';
		$estado 					= isset( $_POST['estado'] ) ? $_POST['estado'] : '';
		$municipio 					= isset( $_POST['municipio'] ) ? $_POST['municipio'] : '';

		$segmento 					= $_POST['segmento'];
		$categoria 					= $_POST['categoria'];

		$accept_the_terms_of_site 	= $_POST['accept_the_terms_of_site'];


		// user_login
		if(empty($user_name)) {
			$register_errors['user_name'] = "Nome de usuário não pode ser vazio.";
		}

		// user_name
		if(empty($user_name)) {
			$register_errors['user_name'] = "Nome completo/Razão Social não pode ser vazio.";
		}

		// user_cpf or cnpj
		if(empty($user_cpf)) {
			$register_errors['user_cpf'] = "CPF é obrigatório";
		}elseif(!cdbr_is_a_valid_cpf($user_cpf)) {
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

		}

		// CNPJ instituicao - TODO: verificar se CNPJ é obrigatório
		if(!empty($cnpj_instituicao)) {
			if(!cdbr_is_a_valid_cnpj($cnpj_instituicao)) {
				$register_errors['cnpj_instituicao'] = "CNPJ informado é inválido";
			}
		}
		
		// user_email
		if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $user_email)) {
			$register_errors['user_email'] =  "Por favor, informe um email válido.";
		}

		//tipo de categoria
		if(empty($categoria)) {
			$register_errors['categoria'] = "O tipo de categoria é obrigatório.";
		}

		//tipo de segmento
		if(empty($segmento)) {
			$register_errors['segmento'] = "Tipo de segmento é obrigatório.";
		}

		// pais
		if(empty($pais)) {
			$register_errors['pais'] = "O campo país é obrigatório.";
		}else {
			// se for o Brasil estado e município são obrigatórios
			if( $pais == 'Brasil') {

				// estado
				if(empty($estado)) {
					$register_errors['estado'] = "Estado é obrigatório.";
				}

				// município
				if(empty($municipio)) {
					$register_errors['municipio'] = "Município é obrigatório.";
				}
			}
		}

		// password
        if(strlen($user_password)==0 )
            $register_errors['pass'] = 'A senha é obrigatória para a inscrição no site.';
        
        if( $user_password != $user_password_confirm)
            $register_errors['pass_confirm'] = 'As senhas informadas não são iguais.';

        // termos de uso
		if(empty($accept_the_terms_of_site)) {
			$register_errors['accept_the_terms_of_site'] = "Você deve concordar com os termos de uso do site.";
		}
        
        // check and register
		if(!sizeof($register_errors)>0) {

			$data['user_login'] = $user_login;
            $data['user_pass'] = $user_password;
            $data['user_email'] =  $user_email;
            $data['first_name'] = $user_name;
            $data['display_name'] = $user_name; 

            $data['role'] = 'subscriber' ;
            
            $user_id = wp_insert_user($data);

            // $status = wp_create_user( $user_name, $user_password, $user_email );

			if ( is_wp_error($status) ) {
				$register_errors['create'] = $user_id->get_error_message();
			} else {

				//se criar o usuário wp_create_user retorna o id
				// $user_id = $status;

				// salva os metadados
				update_user_meta($user_id, 'tipo_manifestacao', $tipo_manifestacao);
				update_user_meta($user_id, 'user_cpf', $user_cpf);
				update_user_meta($user_id, 'user_name', $user_name);
				update_user_meta($user_id, 'categoria', $categoria);
				update_user_meta($user_id, 'segmento', $segmento);

				// razao social é opcional
				if( !empty($razao_social))
					update_user_meta($user_id, 'nome_instituicao', $razao_social);

				// cnpj é opcional
				if( !empty($cnpj))
					update_user_meta($user_id, 'cnpj_instituicao', $segmento);

				// salva os metados do buddypress - cidade e estado sao opcionais
				if( !empty($municipio) )
					cdbr_add_user_meta($user_id, 'cidade', $municipio);
				
				if( !empty($estado) )
					cdbr_add_user_meta($user_id, 'estado', $estado);

				// termos de uso
				cdbr_update_user_terms_current_site($user_id);

				// adiciona o usuário no blog principal
				add_user_to_blog('1', $user_id, 'subscriber' );

				// enviar um email com informações da conta, TODO: verificar se envia senha apenas pelo email
				cdbr_send_email_register( $user_email, $user_login, $user_password );
				
				// TODO: forçar login automático ou enviar senha para o email
				if ( is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )
		            $secure_cookie = false;
		        else
		            $secure_cookie = '';

		        $user = wp_signon(array('user_login' => $user_login, 'user_password' => $user_password), $secure_cookie);

		        if ( !is_wp_error($user) && !$reauth ) {

					wp_safe_redirect($_SERVER['REQUEST_URI']);
					
		            exit();
		        }
			}
		}
	}


	get_header();
 ?>

	<div class="wrapper section-inner">						

		<div class="content">

			<div id="post-<?php the_ID(); ?>" <?php post_class("post"); ?>>

				<div class="post-header">							
				    <h2 class="post-title"><?php the_title(); ?></h2>
			    </div> <!-- /post-header -->
			   				        			        		                
				<div class="post-content">
							                                        
					<?php the_content(); ?>

				</div> <!-- /post-content -->

				<?php if( isset($register_errors) ) : ?>
					<?php if (is_array($register_errors) && sizeof($register_errors) > 0): ?>
						<div class='messages'>
							<?php foreach ($register_errors as $e): ?>
								<div class="error"><?php echo $e; ?></div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>

				<form method="post" id="register">

					<div class="span-4">
						<label for="user_login">Nome de usuário:</label>
						<span class="description">(Não inserir caracteres especiais e nem espaço)<span>
						<input id="user_login" type="text" required="required" name="user_login" class="text" value="<?php echo isset($user_login) ? $user_login : '';?>" />
					</div>

					<div class="span-4">
						<label for="user_email">Email:</label>
						<input id="user_email" type="text" required="required" name="user_email" class="text" value="<?php echo isset($user_email) ? $user_email : '';?>" /> 
					</div>

					<div class="span-4">
						<label for="user_name">Nome completo:</label>
						<input id="user_name" type="text" required="required" name="user_name" class="text" value="<?php echo isset($user_name) ? $user_name : '';?>" />
					</div>

					<div class="span-4">
						<label for="user_cpf">CPF:</label>
						<input id="user_cpf" type="text" required="required" name="user_cpf" class="text" value="<?php echo isset($user_cpf) ? $user_cpf : '';?>" />
					</div>					
					<br>
					<div class="span-4">
						Problema ao cadastrar?<br>	Envie um email para: <a href="mailto:<?php echo get_option('admin_email'); ?>?Subject=Consulta%20Publica"><?php echo get_option('admin_email'); ?></a>.
					</div>
					<br>

					<fieldset>
						<legend>Tipo de manifestação</legend>
						<label>
						  <input type="radio" name="tipo_manifestacao" value="individual" <?php if (isset($tipo_manifestacao) && $tipo_manifestacao == 'individual') echo 'checked'; ?>>
						  Individual
						</label>
						<label>
						  <input type="radio" name="tipo_manifestacao" value="institucional" <?php if (isset($tipo_manifestacao) && $tipo_manifestacao == 'institucional') echo 'checked'; ?>>
						  Institucional
						</label>

						<div id="instituicao" style="<?php echo !isset($nome_instituicao) ? 'display:none': '';?>">
							<div class="span-4">
								<label for="nome_instituicao">Razão Social/Instituição:</label>
								<input id="nome_instituicao" type="text" required="required" name="nome_instituicao" class="text" value="<?php echo isset($nome_instituicao) ? $nome_instituicao : '';?>" />
							</div>

							<div class="span-4">
								<label for="cnpj_instituicao">CNPJ:</label>
								<input id="cnpj_instituicao" type="text" name="cnpj_instituicao" class="text" value="<?php echo isset($cnpj_instituicao) ? $cnpj_instituicao : '';?>" />
							</div>
						</div>
					</fieldset>

					<div class="span-4">
						<label>País:</label>
						<select required="required" name="pais" id="pais">
                            <option value=""> Selecione </option>
                            <?php $countries = cdbr_get_countries_array(); ?>
                            <?php foreach ($countries as $key => $country ): ?>
                                <option value="<?php echo $key; ?>"  <?php if (isset($_POST['pais']) && $_POST['pais'] == $key) echo 'selected'; ?>>
                                    <?php echo $country; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
					</div>
			
					<div id="endereco_nacional" style="<?php echo !isset($_POST["estado"]) ? 'display:none': '';?>">
						<div class="span-4">
							<label for="estado">Estado:</label>
							<select id="estado" required="required" name="estado" id="estado">
	                            <option value=""> Selecione </option>
	                            <?php $states = cdbr_get_states(); ?>
	                            <?php foreach ($states as $s): ?>
	                                <option value="<?php echo $s->sigla; ?>"  <?php if (isset($_POST['estado']) && $_POST['estado'] == $s->sigla) echo 'selected'; ?>  >
	                                    <?php echo $s->nome; ?>
	                                </option>
	                            <?php endforeach; ?>
	                        </select>
						</div>
					
						<div class="span-4">
							<label for="municipio">Município:</label>
							<select id="municipio" required="required" name="municipio" id="municipio">
	                            <option value="">Selecione</option>
	                        </select> 
						</div>
					</div>

					<div class="span-4">
						<label>Categoria:</label>
						<select required="required" name="categoria" id="categoria">
                            <option value=""> Selecione </option>
                            <?php $categorias = cdbr_get_categorias(); ?>
                            <?php foreach ($categorias as $key => $c ): ?>
                                <option value="<?php echo $key; ?>"  <?php if (isset($_POST['categoria']) && $_POST['categoria'] == $key) echo 'selected'; ?>>
                                    <?php echo $c; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
					</div>

					<div class="span-4">
						<label>Segmento ou setor de atuação:</label>
						<select required="required" name="segmento" id="segmento">
                            <option value=""> Selecione </option>
                            <?php $segmentos = cdbr_get_segmentos(); ?>
                            <?php foreach ($segmentos as $key => $s ): ?>
                                <option value="<?php echo $key; ?>"  <?php if (isset($_POST['segmento']) && $_POST['segmento'] == $key) echo 'selected'; ?>>
                                    <?php echo $s; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
					</div>

					<div class="span-4">
						<label for="user_password">Senha:</label>
						<input id="user_password" required="required" type="password" name="user_password" />
					</div>

					<div class="span-4">
						<label for="user_password_confirm">Confirme a senha:</label>
						<input id="user_password_confirm" required="required" type="password" name="user_password_confirm" />
					</div>

					<div class="span-4">
						<label>
						    <input type="checkbox" name="accept_the_terms_of_site">
						    Li e concordo com os <a href="<?php echo site_url('/termos-de-uso/'); ?>">
						    termos de uso</a> do site
						 </label>
					</div>

					<div class="textright">
						<input type="submit" id="submitbtn" class="blue-button"  name="submit" value="Registrar" />
					</div>
				</form>
			</div> <!-- end post -->
			<div class="clear"></div>
			
		</div> <!-- /content left -->
	</div>
	
	<?php  
	}
	else { // Verificar se já é usuário do culturadigital e pedir para preencher os dados faltantes.

		get_header(); ?>
		
		<div class="wrapper section-inner">						

			<div class="content">
				<div id="post-<?php the_ID(); ?>" <?php post_class('post');?>>
					<div class="success">Você foi cadastrado com sucesso! <br>Para participar basta acessar a <a href="<?php echo site_url('/metas/'); ?>" title="Página das metas">página das metas</a> e deixar as suas opiniões.</div>
				</div>
			</div>
		</div>
	<?php 
	} ?>

<?php
get_footer();
?>
