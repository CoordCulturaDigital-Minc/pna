<?php
/*
Function Name: Widget Login
Plugin URI: http://culturadigital.br
Version: 0.1
Author: Cleber Santos
Author URI: http://culturadigital.br
*/

class widget_login extends WP_Widget
{	
	function __construct()
	{
		$widget_args = array('classname' => 'widget_login', 'description' => __( 'Login') );
		parent::__construct('login', __('Login'), $widget_args);
	}

	function widget($args, $instance)
	{
		extract($args);
		global $user_ID;
		
		$title = apply_filters('widget_title', empty($instance['title']) ? 'Entrar' : $instance['title']);

	    if(empty($user_ID)) :
		
		echo $before_widget
			 .$before_title
			 .$title
			 .$after_title;        
        ?>
        <form name="loginform" id="loginform" action="<?php print wp_login_url( $_SERVER['REQUEST_URI'] ); ?>" method="post">
            <fieldset>
                <label for="userLogin" class="login">Entrar:</label>
                <div class="formfield">
                    <div class="login inputDefault">
                    	<input type="text" name="log" id="userLogin" placeholder="Nome de usuário" tabindex="10" /></div>
                    <div class="pw inputDefault"><input type="password" name="pwd" class="userPass" placeholder="Senha" tabindex="20" /></div>
                    <div class="clearfix"></div>
                    <div class="forever">
                        <a  href="<?php print wp_lostpassword_url(); ?>" title="Esqueci minha senha">Recuperar senha</a>
                        <div class="clearfix"></div>
                        <a href="<?php print get_bloginfo('url'); ?>/cadastro" title="Esqueci minha senha">Não sou cadastrado</a>
                    </div>
                    <div class="clearfix"></div>
                    
                    <input name="submit" type="submit" id="submit" class="userSubmit submitDefault submit" value="Entrar">
                </div>
            </fieldset>
        </form>
        <?php
		echo $after_widget;
		
		$before_widget_ = strstr('#', $before_widget);
		$before_pos = strpos('"', $before_widget_);
		$before_widget_ = substr($before_widget_, 0, ($before_pos - 1));

		else:
		
			echo $before_widget
				 .$before_title
				 .'Minha conta'
				 .$after_title;
				 
			global $bp;
			
			?>
	        <div class="panel">
		        <div class="bp-login-widget-user-link">Olá <?php echo bp_core_get_userlink( bp_loggedin_user_id() ); ?></div>
		        <div class="clearfix"></div>
		        
		        <div class="bp-login-widget-user-avatar">
					<a href="<?php echo bp_loggedin_user_domain(); ?>">
						<?php bp_loggedin_user_avatar( 'type=thumb&width=50&height=50' ); ?>
					</a>
				</div>

				<div class="bp-login-widget-user-links">
					<div class="bp-login-widget-user-logout"><a class="logout" href="<?php echo wp_logout_url( bp_get_requested_url() ); ?>"><?php _e( 'Log Out', 'buddypress' ); ?></a></div>
				</div>
			</div>

	        <?php
			echo $after_widget;
		endif;
	}

	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		
		if( $instance != $new_instance )
			$instance = $new_instance;
		
		return $instance;
	}

	function form($instance)
	{
	    $title = esc_attr( $instance['title'] );
	?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>">Título:</label>
				<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" maxlength="26" value="<?php echo $title; ?>" class="widefat" />
			</p>
        <?php
	}
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("widget_login");'));
