<?php

/**
 */
class CDBR_Theme_Options
{
    const PAGE_SLUG = 'cdbr-theme-options';
    const PAGE_TITLE = 'Opções do tema';
    const PAGE_NAME = 'cdbr-theme-options-page';

    const OPTIONS_GROUP = 'cdbr-theme-options-group';
    const OPTION_NAME = 'cdbr-theme-options';

    const SECTION_YES_VALUE = 'S';
    const SECTION_NO_VALUE = 'N';

    // SECTION 
    const SECTION_DISPLAY_COMMENTS_ID = 'cdbr-section-theme-options';
    const SECTION_DISPLAY_COMMENTS_TITLE = 'Configurar opções para o tema';

    const SECTION_DISPLAY_IN_PAGS_FIELD_ID = 'cdbr-allow-popup-msg-user-logged-in';
    const SECTION_ALLOW_POPUP_MSG_FIELD_TITLE = 'Ativar popup para usuários cadastrados?';

    const SECTION_ALLOW_POPUP_MSG_FIELD_ID = 'cdbr-allow-popup-msg-user-logged-in';
    const SECTION_ALLOW_POPUP_MSG_FIELD_TITLE = 'Ativar popup para usuários cadastrados?';


    const SETTINGS_SECTION_DISPLAY_IN_POSTS_FIELD_ID = 'cdbr-allow-display-in-posts-field';
    const SETTINGS_SECTION_DISPLAY_IN_POSTS_FIELD_TITLE = 'Ativar para todos os posts?';

    const SETTINGS_SECTION_DISPLAY_IN_POST_TYPE_FIELD_ID = 'cdbr-allow-display-in-post-type-field';
    const SETTINGS_SECTION_DISPLAY_IN_POST_TYPE_FIELD_TITLE = 'Ativar para qual tipo de post?';

    const SETTINGS_SECTION_DISPLAY_IN_PAGE_TEMPLATE_FIELD_ID = 'cdbr-allow-display-in-page-template-field';
    const SETTINGS_SECTION_DISPLAY_IN_PAGE_TEMPLATE_FIELD_TITLE = 'Ativar para qual de tipo de página?';

    // SECTION
    const SETTINGS_SECTION_CUSTOM_THEME_ID = 'cdbr-default-theme-css';
    const SETTINGS_SECTION_CUSTOM_THEME_TITLE = 'Tema Padrão';

    const SETTINGS_SECTION_DEFAULT_THEME_CSS_FIELD_ID = 'cdbr-default-theme-css-field';
    const SETTINGS_SECTION_DEFAULT_THEME_CSS_FIELD_TITLE = 'Personalizar CSS:';

    const SETTINGS_SECTION_CUSTOM_JS_FIELD_ID = 'cdbr-custom-js-field';
    const SETTINGS_SECTION_CUSTOM_JS_FIELD_TITLE = 'Customizar JS:';

     // SECTION
    const SETTINGS_SECTION_TERMS_SITE_ID = 'cdbr-terms-site';
    const SETTINGS_SECTION_TERMS_SITE_TITLE = 'Termos de uso do site';

    const SETTINGS_SECTION_DISPLAY_CONFIRM_TERMS_SITE_FIELD_ID = 'cdbr-confirm-terms-field';
    const SETTINGS_SECTION_DISPLAY_CONFIRM_TERMS_FIELD_TITLE = 'O usuário deve concordar com o termos de uso?';

    // const SETTINGS_SECTION_DISPLAY_WHAT_PAGE_TERMS_FIELD_ID = 'cdbr-what-page-terms-field';
    // const SETTINGS_SECTION_DISPLAY_WHAT_PAGE_TERMS_FIELD_TITLE = 'Qual a página do termos de uso?';

    const SETTINGS_SECTION_DISPLAY_TITLE_MSG_TERMS_FIELD_ID = 'cdbr-title-msg-terms-field';
    const SETTINGS_SECTION_DISPLAY_TITLE_MSG_TERMS_FIELD_TITLE = 'Qual o título da mensagem para concordar com o termos de uso?';

    private static $SETTINGS_SECTION_YES_NO_VALID_VALUES = array(
        self::SECTION_YES_VALUE,
        self::SECTION_NO_VALUE
    );

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
        add_action('admin_notices', array($this, 'admin_notices'));

        add_action('wp_enqueue_scripts', array($this,'theme_enqueue_scripts'));
        add_action( 'init', array($this,'print_custom_css' ));
        add_action( 'wp_head', array($this, "print_custom_js"), PHP_INT_MAX); 

    }

    /**
     * Initializes plugin's options
     */
    public function init()
    {
        $this->options = get_option(self::OPTION_NAME, array());
    }

    /**
     * Enqueues plugin's admin styles
     */
    public function enqueue_styles()
    {

    }

    /**
     * Enqueues plugin's admin scripts
     */
    public function enqueue_scripts()
    {

    }
    /**
     * Enqueues plugin's theme scripts
     */
    public function theme_enqueue_scripts() {

        wp_register_style( 'side-comments-theme-custom', add_query_arg( array( 'sidecss' => 1 ), home_url() ), array ( 'side-comments-style' ) );
        wp_enqueue_style( 'side-comments-theme-custom' );
    }

    /**
     * Adds plugin's page to admin's side menu
     */
    public function add_plugin_page()
    {
        add_menu_page(
            self::PAGE_TITLE,
            self::PAGE_TITLE,
            'manage_options',
            self::PAGE_SLUG,
            array($this, 'create_admin_page'),
            'dashicons-admin-appearance'
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        //TODO: recuperar o HTML de outro local
        ?>
        <div class="wrap">
            <h2><?= self::PAGE_TITLE ?> </h2>

            <form method="post" action="options.php">
                <?php
                settings_fields(self::OPTIONS_GROUP);
                do_settings_sections(self::PAGE_NAME);
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            self::OPTIONS_GROUP,
            self::OPTION_NAME,
            array($this, 'input_validate')
        );

        add_settings_section(
            self::SECTION_DISPLAY_COMMENTS_ID,
            self::SECTION_DISPLAY_COMMENTS_TITLE,
            array($this, 'print_section_guests_interaction_info'),
            self::PAGE_NAME
        );

        add_settings_field(
            self::SECTION_ALLOW_POPUP_MSG_FIELD_ID,
            self::SECTION_ALLOW_POPUP_MSG_FIELD_TITLE,
            array($this, 'print_guests_interaction_field_callback'),
            self::PAGE_NAME,
            self::SECTION_DISPLAY_COMMENTS_ID
        );

        add_settings_field(
            self::SETTINGS_SECTION_DISPLAY_IN_POSTS_FIELD_ID,
            self::SETTINGS_SECTION_DISPLAY_IN_POSTS_FIELD_TITLE,
            array($this, 'print_display_in_posts_field_callback'),
            self::PAGE_NAME,
            self::SECTION_DISPLAY_COMMENTS_ID
        );

        add_settings_field(
            self::SETTINGS_SECTION_DISPLAY_IN_POST_TYPE_FIELD_ID,
            self::SETTINGS_SECTION_DISPLAY_IN_POST_TYPE_FIELD_TITLE,
            array($this, 'print_display_in_post_type_field_callback'),
            self::PAGE_NAME,
            self::SECTION_DISPLAY_COMMENTS_ID
        );

        add_settings_field(
            self::SETTINGS_SECTION_DISPLAY_IN_PAGE_TEMPLATE_FIELD_ID,
            self::SETTINGS_SECTION_DISPLAY_IN_PAGE_TEMPLATE_FIELD_TITLE,
            array($this, 'print_display_in_page_template_field_callback'),
            self::PAGE_NAME,
            self::SECTION_DISPLAY_COMMENTS_ID
        );

        add_settings_section(
            self::SETTINGS_SECTION_CUSTOM_THEME_ID,
            self::SETTINGS_SECTION_CUSTOM_THEME_TITLE,
            array($this, 'print_section_custom_theme'),
            self::PAGE_NAME
        );

        add_settings_field(
            self::SETTINGS_SECTION_DEFAULT_THEME_CSS_FIELD_ID,
            self::SETTINGS_SECTION_DEFAULT_THEME_CSS_FIELD_TITLE,
            array($this, 'print_default_theme_css_field_callback'),
            self::PAGE_NAME,
            self::SETTINGS_SECTION_CUSTOM_THEME_ID
        );

        add_settings_field(
            self::SETTINGS_SECTION_CUSTOM_JS_FIELD_ID,
            self::SETTINGS_SECTION_CUSTOM_JS_FIELD_TITLE,
            array($this, 'print_custom_js_field_callback'),
            self::PAGE_NAME,
            self::SETTINGS_SECTION_CUSTOM_THEME_ID
        );

        // new section
        add_settings_section(
            self::SETTINGS_SECTION_TERMS_SITE_ID,
            self::SETTINGS_SECTION_TERMS_SITE_TITLE,
            array($this, 'print_section_terms_site'),
            self::PAGE_NAME
        );

        add_settings_field(
            self::SETTINGS_SECTION_DISPLAY_CONFIRM_TERMS_SITE_FIELD_ID,
            self::SETTINGS_SECTION_DISPLAY_CONFIRM_TERMS_FIELD_TITLE,
            array($this, 'print_display_confirm_terms_site_field_callback'),
            self::PAGE_NAME,
            self::SETTINGS_SECTION_TERMS_SITE_ID
        );

        // add_settings_field(
        //     self::SETTINGS_SECTION_DISPLAY_WHAT_PAGE_TERMS_FIELD_ID,
        //     self::SETTINGS_SECTION_DISPLAY_WHAT_PAGE_TERMS_FIELD_TITLE,
        //     array($this, 'print_display_what_page_terms_callback'),
        //     self::PAGE_NAME,
        //     self::SETTINGS_SECTION_TERMS_SITE_ID
        // );

        add_settings_field(
            self::SETTINGS_SECTION_DISPLAY_TITLE_MSG_TERMS_FIELD_ID,
            self::SETTINGS_SECTION_DISPLAY_TITLE_MSG_TERMS_FIELD_TITLE,
            array($this, 'print_display_title_msg_terms_field_callback'),
            self::PAGE_NAME,
            self::SETTINGS_SECTION_TERMS_SITE_ID
        );
    }

    /**
     * Validates user input
     * @param $input
     * @return mixed|void
     */
    public function input_validate($input)
    {
        $validatedInput = array();
        
        if (isset($input[self::SECTION_ALLOW_POPUP_MSG_FIELD_ID])) {
            $value = $input[self::SECTION_ALLOW_POPUP_MSG_FIELD_ID];
            if (in_array($value, self::$SETTINGS_SECTION_YES_NO_VALID_VALUES)) {
                $validatedInput[self::SECTION_ALLOW_POPUP_MSG_FIELD_ID] = $value;
            } else {
                add_settings_error(self::OPTION_NAME, 'invalid_value', 'Por favor escolha uma opção válida no campo "' . self::SECTION_ALLOW_POPUP_MSG_FIELD_TITLE . '".', $type = 'error');
            }
        }

        if (isset($input[self::SETTINGS_SECTION_DISPLAY_IN_POSTS_FIELD_ID])) {
            $value = $input[self::SETTINGS_SECTION_DISPLAY_IN_POSTS_FIELD_ID];
            if (in_array($value, self::$SETTINGS_SECTION_YES_NO_VALID_VALUES)) {
                $validatedInput[self::SETTINGS_SECTION_DISPLAY_IN_POSTS_FIELD_ID] = $value;
            } else {
                add_settings_error(self::OPTION_NAME, 'invalid_value', 'Por favor escolha uma opção válida no campo "' . self::SETTINGS_SECTION_DISPLAY_IN_POSTS_FIELD_TITLE . '".', $type = 'error');
            }
        }

        if (isset($input[self::SETTINGS_SECTION_DISPLAY_IN_POST_TYPE_FIELD_ID])) {
            $value = $input[self::SETTINGS_SECTION_DISPLAY_IN_POST_TYPE_FIELD_ID];
             $validatedInput[self::SETTINGS_SECTION_DISPLAY_IN_POST_TYPE_FIELD_ID] = $value;
        }

        if (isset($input[self::SETTINGS_SECTION_DISPLAY_IN_PAGE_TEMPLATE_FIELD_ID])) {
            $value = $input[self::SETTINGS_SECTION_DISPLAY_IN_PAGE_TEMPLATE_FIELD_ID];
            $validatedInput[self::SETTINGS_SECTION_DISPLAY_IN_PAGE_TEMPLATE_FIELD_ID] = $value;
        }

        if (isset($input[self::SETTINGS_SECTION_DEFAULT_THEME_CSS_FIELD_ID])) {
            $value = $input[self::SETTINGS_SECTION_DEFAULT_THEME_CSS_FIELD_ID];
            $validatedInput[self::SETTINGS_SECTION_DEFAULT_THEME_CSS_FIELD_ID] = $value;
        }

        if (isset($input[self::SETTINGS_SECTION_CUSTOM_JS_FIELD_ID])) {
            $value = $input[self::SETTINGS_SECTION_CUSTOM_JS_FIELD_ID];
            $validatedInput[self::SETTINGS_SECTION_CUSTOM_JS_FIELD_ID] = $value;
        }


        if (isset($input[self::SETTINGS_SECTION_DISPLAY_CONFIRM_TERMS_SITE_FIELD_ID])) {
            $value = $input[self::SETTINGS_SECTION_DISPLAY_CONFIRM_TERMS_SITE_FIELD_ID];
            if (in_array($value, self::$SETTINGS_SECTION_YES_NO_VALID_VALUES)) {
                $validatedInput[self::SETTINGS_SECTION_DISPLAY_CONFIRM_TERMS_SITE_FIELD_ID] = $value;
            } else {
                add_settings_error(self::OPTION_NAME, 'invalid_value', 'Por favor escolha uma opção válida no campo "' . self::SETTINGS_SECTION_DISPLAY_CONFIRM_TERMS_FIELD_TITLE . '".', $type = 'error');
            }
        }

        // if (isset($input[self::SETTINGS_SECTION_DISPLAY_WHAT_PAGE_TERMS_FIELD_ID])) {
        //     $value = $input[self::SETTINGS_SECTION_DISPLAY_WHAT_PAGE_TERMS_FIELD_ID];
        //     $validatedInput[self::SETTINGS_SECTION_DISPLAY_WHAT_PAGE_TERMS_FIELD_ID] = $value;
        // }

         if (isset($input[self::SETTINGS_SECTION_DISPLAY_TITLE_MSG_TERMS_FIELD_ID])) {
            $value = $input[self::SETTINGS_SECTION_DISPLAY_TITLE_MSG_TERMS_FIELD_ID];
            $validatedInput[self::SETTINGS_SECTION_DISPLAY_TITLE_MSG_TERMS_FIELD_ID] = $value;
        }

        return apply_filters('wp_side_comments_input_validate', $validatedInput, $input);
    }

    /**
     * Displays the validation errors and update messages
     */
    function admin_notices()
    {
        settings_errors();
    }

    /**
     * Prints the guests interaction section text
     */
    public function print_section_guests_interaction_info()
    {
        //TODO: recuperar texto de outro lugar
        print 'Escolha os lugares onde deseja adicionar os comentários por parágrafo.';
    }

    /**
     * Prints the guests interaction section text
     */
    public function print_section_custom_theme()
    {
        //TODO: recuperar texto de outro lugar
        print 'Customize o tema do plugin. Se preencher o css o tema padrão será substituído';
    }

    /**
     * Prints the guests interaction section text
     */
    public function print_section_terms_site()
    {
        //TODO: recuperar texto de outro lugar
        print 'Marque e configure se deseja que os usuários confirmem a leitura do termo de uso.';
    }

    

    /**
     * Prints the value of allow guest interaction
     */
    public function print_guests_interaction_field_callback()
    {
        //TODO: recuperar HTML de outro local
        printf(
            '<span class="radio"><input type="radio" id="%s" name="%s[%s]" value="%s" %s>SIM</span>',
            self::SECTION_ALLOW_POPUP_MSG_FIELD_ID . '-allow',
            self::OPTION_NAME,
            self::SECTION_ALLOW_POPUP_MSG_FIELD_ID,
            self::SECTION_YES_VALUE,
            $this->isDisplayInPagsAllowed() ? 'checked' : ''
        );

        printf(
            '<span class="radio"><input type="radio" id="%s" name="%s[%s]" value="%s" %s>NÃO</span> ',
            self::SECTION_ALLOW_POPUP_MSG_FIELD_ID . '-deny',
            self::OPTION_NAME,
            self::SECTION_ALLOW_POPUP_MSG_FIELD_ID,
            self::SECTION_NO_VALUE,
            !$this->isDisplayInPagsAllowed() ? 'checked' : ''
        );
    }


     /**
     * Prints the value of display in posts
     */
    public function print_display_in_posts_field_callback()
    {
        //TODO: recuperar HTML de outro local
        printf(
            '<span class="radio"><input type="radio" id="%s" name="%s[%s]" value="%s" %s>SIM</span>',
            self::SETTINGS_SECTION_DISPLAY_IN_POSTS_FIELD_ID . '-allow',
            self::OPTION_NAME,
            self::SETTINGS_SECTION_DISPLAY_IN_POSTS_FIELD_ID,
            self::SECTION_YES_VALUE,
            $this->isDisplayInPostsAllowed() ? 'checked' : ''
        );

        printf(
            '<span class="radio"><input type="radio" id="%s" name="%s[%s]" value="%s" %s>NÃO</span> ',
            self::SETTINGS_SECTION_DISPLAY_IN_POSTS_FIELD_ID . '-deny',
            self::OPTION_NAME,
            self::SETTINGS_SECTION_DISPLAY_IN_POSTS_FIELD_ID,
            self::SECTION_NO_VALUE,
            !$this->isDisplayInPostsAllowed() ? 'checked' : ''
        );
    }

    /**
     * Prints the value of display in posts
     */
    public function print_display_in_post_type_field_callback()
    {
        //TODO: recuperar HTML de outro local
        $args = array(
           'public'   => true,
           '_builtin' => false
        );

        $output = 'names'; // names or objects, note names is the default
        $operator = 'and'; // 'and' or 'or'

        $post_types = get_post_types( $args, $output, $operator ); 

        printf( '<select id="%s" name="%s[%s]">',
            self::SETTINGS_SECTION_DISPLAY_IN_POST_TYPE_FIELD_ID,
            self::OPTION_NAME,
            self::SETTINGS_SECTION_DISPLAY_IN_POST_TYPE_FIELD_ID
        );
            echo '<option value="">Nenhum</option>';
            foreach ( $post_types  as $post_type ) {
             $obj = get_post_type_object( $post_type );
             printf('<option value="%s" %s>%s</option>', $post_type, $this->getDisplayPostTypeSelected() == $post_type ? 'selected' : '', $obj->labels->singular_name );
            }
        echo "</select>";
    }

     /**
     * Prints the value of display in page template
     */
    public function print_display_in_page_template_field_callback()
    {
        //TODO: recuperar HTML de outro local
        $templates = get_page_templates();


        printf( '<select id="%s" name="%s[%s]">',
            self::SETTINGS_SECTION_DISPLAY_IN_PAGE_TEMPLATE_FIELD_ID,
            self::OPTION_NAME,
            self::SETTINGS_SECTION_DISPLAY_IN_PAGE_TEMPLATE_FIELD_ID
        );

        echo '<option value="">Nenhum</option>';

        foreach ( $templates as $template_name => $template_filename ) {
            printf('<option value="%s" %s>%s (%s)</option>', $template_filename, $this->getDisplayPageTemplateSelected() == $template_filename ? 'selected' : '', $template_name, $template_filename );
        
         }
        echo "</select>";

    }    

    /**
     * Prints textarea to default theme
     */
    public function print_default_theme_css_field_callback()
    {
        
        //TODO: recuperar HTML de outro local
        printf( '<textarea cols="70" rows="20" id="%s" name="%s[%s]">%s</textarea>',
            self::SETTINGS_SECTION_DEFAULT_THEME_CSS_FIELD_ID,
            self::OPTION_NAME,
            self::SETTINGS_SECTION_DEFAULT_THEME_CSS_FIELD_ID,
            $this->getDefaultThemeCss()
        );
    }

    /**
     * Prints textarea to custom js
     */
    public function print_custom_js_field_callback()
    {
        
        //TODO: recuperar HTML de outro local
        printf( '<textarea cols="70" rows="20" id="%s" name="%s[%s]">%s</textarea>',
            self::SETTINGS_SECTION_CUSTOM_JS_FIELD_ID,
            self::OPTION_NAME,
            self::SETTINGS_SECTION_CUSTOM_JS_FIELD_ID,
            $this->getCustomJs()
        );
    }

     /**
     * Prints the question if user has confirm terms 
     */
    public function print_display_confirm_terms_site_field_callback()
    {
        //TODO: recuperar HTML de outro local
        printf(
            '<span class="radio"><input type="radio" id="%s" name="%s[%s]" value="%s" %s>SIM</span>',
            self::SETTINGS_SECTION_DISPLAY_CONFIRM_TERMS_SITE_FIELD_ID . '-allow',
            self::OPTION_NAME,
            self::SETTINGS_SECTION_DISPLAY_CONFIRM_TERMS_SITE_FIELD_ID,
            self::SECTION_YES_VALUE,
            $this->isConfirmTermsAllowed() ? 'checked' : ''
        );

        printf(
            '<span class="radio"><input type="radio" id="%s" name="%s[%s]" value="%s" %s>NÃO</span> ',
            self::SETTINGS_SECTION_DISPLAY_CONFIRM_TERMS_SITE_FIELD_ID . '-deny',
            self::OPTION_NAME,
            self::SETTINGS_SECTION_DISPLAY_CONFIRM_TERMS_SITE_FIELD_ID,
            self::SECTION_NO_VALUE,
            !$this->isConfirmTermsAllowed() ? 'checked' : ''
        );
    }

    /**
     * Prints input to custom title msg
     */
    public function print_display_title_msg_terms_field_callback()
    {
        
        //TODO: recuperar HTML de outro local
        printf( '<input type="text" id="%s" class="large-text" name="%s[%s]" value="%s"/>',
            self::SETTINGS_SECTION_DISPLAY_TITLE_MSG_TERMS_FIELD_ID,
            self::OPTION_NAME,
            self::SETTINGS_SECTION_DISPLAY_TITLE_MSG_TERMS_FIELD_ID,
            $this->getTitleMsgTerms()
        );
    }


    /**
     * Find the current template for comment's section
     *
     * @return string the template
     */
    public function getCurrentSectionTemplate()
    {
        if ($this->isCustomSectionTemplateEnabled()) {
            return $this->getStoredSectionTemplate();
        } else {
            return $this->getDefaultSectionTemplate();
        }
    }

    /**
     * Checks if display in pages is enabled
     *
     * @return bool returns TRUE if the user is able, FALSE otherwise
     */
    public function isDisplayInPagsAllowed()
    {
        return isset($this->options[self::SECTION_ALLOW_POPUP_MSG_FIELD_ID])
        && $this->options[self::SECTION_ALLOW_POPUP_MSG_FIELD_ID] == self::SECTION_YES_VALUE;
    }

    /**
     * Checks if display in posts is enabled
     *
     * @return bool returns TRUE if the user is able, FALSE otherwise
     */
    public function isDisplayInPostsAllowed()
    {
        return isset($this->options[self::SETTINGS_SECTION_DISPLAY_IN_POSTS_FIELD_ID])
        && $this->options[self::SETTINGS_SECTION_DISPLAY_IN_POSTS_FIELD_ID] == self::SECTION_YES_VALUE;
    }


    /**
     * Get which post type to display comments  
     * @return string
     */
    public function getDisplayPostTypeSelected()
    {
        if (isset($this->options[self::SETTINGS_SECTION_DISPLAY_IN_POST_TYPE_FIELD_ID])) {
            return $this->options[self::SETTINGS_SECTION_DISPLAY_IN_POST_TYPE_FIELD_ID];
        } 
    }

     /**
     * Get which page template to display comments  
     * @return string
     */
    public function getDisplayPageTemplateSelected()
    {
        if (isset($this->options[self::SETTINGS_SECTION_DISPLAY_IN_PAGE_TEMPLATE_FIELD_ID])) {
            return $this->options[self::SETTINGS_SECTION_DISPLAY_IN_PAGE_TEMPLATE_FIELD_ID];
        } 
    }


    /**
     * Get css custom
     * @return string
     */
    public function getDefaultThemeCss()
    {
        // TODO: verificar se é o melhor salvar separado do array

        if (isset($this->options[self::SETTINGS_SECTION_DEFAULT_THEME_CSS_FIELD_ID])) {
            return $this->options[self::SETTINGS_SECTION_DEFAULT_THEME_CSS_FIELD_ID];
        } 

    }

    /**
     * Get javascript custom
     * @return string
     */
    public function getCustomJs()
    {   
        // TODO: verificar se é o melhor salvar separado do array

        if (isset($this->options[self::SETTINGS_SECTION_CUSTOM_JS_FIELD_ID])) {
            return $this->options[self::SETTINGS_SECTION_CUSTOM_JS_FIELD_ID];
        } 
    }

     /**
     * If the query var is set, print the Simple Custom CSS rules.
     */
    public function print_custom_css() {
        // Only print CSS if this is a stylesheet request
        if( ! isset( $_GET['sidecss'] ) || intval( $_GET['sidecss'] ) !== 1 ) {
            return;
        }
        
        ob_start();
        header( 'Content-type: text/css' );
        $options     = $this->getDefaultThemeCss();
        $raw_content = !empty( $options ) ? $options : '';
        $content     = wp_kses( $raw_content, array( '\'', '\"' ) );
        $content     = str_replace( '&gt;', '>', $content );
        echo $content; //xss okay
        die();
    }


    public function print_custom_js() {
        // Only print CSS if this is a javascript request
        $script_source = htmlspecialchars_decode($this->getCustomJs());  
        
        echo <<<EOT
        <script type="text/javascript">
            {$script_source}
        </script>
EOT;
    }

    /**
     * 
     *
     * @return bool returns TRUE if the user is able, FALSE otherwise
     */
    public function isConfirmTermsAllowed()
    {
        return isset($this->options[self::SETTINGS_SECTION_DISPLAY_CONFIRM_TERMS_SITE_FIELD_ID])
        && $this->options[self::SETTINGS_SECTION_DISPLAY_CONFIRM_TERMS_SITE_FIELD_ID] == self::SECTION_YES_VALUE;
    }


    // /**
    //  *  
    //  * @return string
    //  */
    // public function getWhatPageTermsSelected()
    // {
    //     if (isset($this->options[self::SETTINGS_SECTION_DISPLAY_WHAT_PAGE_TERMS_FIELD_ID])) {
    //         return $this->options[self::SETTINGS_SECTION_DISPLAY_WHAT_PAGE_TERMS_FIELD_ID];
    //     } 
    // }

     /**
     *  Get message title its terms
     * @return string
     */
    public function getTitleMsgTerms()
    {
        if (isset($this->options[self::SETTINGS_SECTION_DISPLAY_TITLE_MSG_TERMS_FIELD_ID])) {
            return $this->options[self::SETTINGS_SECTION_DISPLAY_TITLE_MSG_TERMS_FIELD_ID];
        } 
    }
}

global $CDBRThemeOptions;
$CDBRThemeOptions = new CDBR_Theme_Options();
