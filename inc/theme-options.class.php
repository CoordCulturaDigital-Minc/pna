<?php

/**
 */
class PNA_Theme_Options
{
    const PAGE_SLUG = 'cdbr-theme-options';
    const PAGE_TITLE = 'Opções do tema';
    const PAGE_NAME = 'cdbr-theme-options-page';

    const OPTIONS_GROUP = 'cdbr-theme-options-group';
    const OPTION_NAME = 'cdbr-theme-options';

    const SECTION_YES_VALUE = 'S';
    const SECTION_NO_VALUE = 'N';

    // SECTION THEME OPTIONS
    const SECTION_DISPLAY_COMMENTS_ID = 'cdbr-section-theme-options';
    const SECTION_DISPLAY_COMMENTS_TITLE = 'Opções para o tema';

    //FIELDS 
    const ALLOW_POPUP_MSG_FIELD_ID = 'cdbr-allow-popup-msg-user-logged-in';
    const ALLOW_POPUP_MSG_FIELD_TITLE = 'Ativar mensagem popup de comentários movidos para usuários cadastrados?';

    // SECTION PERSONALIZE THEME
    const SECTION_CUSTOM_THEME_ID = 'cdbr-section-custom-theme';
    const SECTION_CUSTOM_THEME_TITLE = 'Customizar Tema';

    // FIELDS
    const DEFAULT_THEME_CSS_FIELD_ID = 'cdbr-default-theme-css-field';
    const DEFAULT_THEME_CSS_FIELD_TITLE = 'Personalizar CSS:';

    private static $OPTIONS_YES_NO_VALID_VALUES = array(
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

        wp_register_style( 'cdbr-theme-custom', add_query_arg( array( 'cdbrcss' => 1 ), home_url() ) );
        wp_enqueue_style( 'cdbr-theme-custom' );
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
            array($this, 'print_section_theme_options'),
            self::PAGE_NAME
        );

        add_settings_field(
            self::ALLOW_POPUP_MSG_FIELD_ID,
            self::ALLOW_POPUP_MSG_FIELD_TITLE,
            array($this, 'print_allow_popup_msg_field_callback'),
            self::PAGE_NAME,
            self::SECTION_DISPLAY_COMMENTS_ID
        );

        add_settings_section(
            self::SECTION_CUSTOM_THEME_ID,
            self::SECTION_CUSTOM_THEME_TITLE,
            array($this, 'print_section_custom_theme'),
            self::PAGE_NAME
        );

        add_settings_field(
            self::DEFAULT_THEME_CSS_FIELD_ID,
            self::DEFAULT_THEME_CSS_FIELD_TITLE,
            array($this, 'print_default_theme_css_field_callback'),
            self::PAGE_NAME,
            self::SECTION_CUSTOM_THEME_ID
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
        
        if (isset($input[self::ALLOW_POPUP_MSG_FIELD_ID])) {
            $value = $input[self::ALLOW_POPUP_MSG_FIELD_ID];
            if (in_array($value, self::$OPTIONS_YES_NO_VALID_VALUES)) {
                $validatedInput[self::ALLOW_POPUP_MSG_FIELD_ID] = $value;
            } else {
                add_settings_error(self::OPTION_NAME, 'invalid_value', 'Por favor escolha uma opção válida no campo "' . self::ALLOW_POPUP_MSG_FIELD_TITLE . '".', $type = 'error');
            }
        }

        if (isset($input[self::DEFAULT_THEME_CSS_FIELD_ID])) {
            $value = $input[self::DEFAULT_THEME_CSS_FIELD_ID];
            $validatedInput[self::DEFAULT_THEME_CSS_FIELD_ID] = $value;
        }

        return apply_filters('cdbr_input_validate', $validatedInput, $input);
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
    public function print_section_theme_options()
    {
        //TODO: recuperar texto de outro lugar
        print 'Personalize as opções do tema.';
    }

    /**
     * Prints the guests interaction section text
     */
    public function print_section_custom_theme()
    {
        //TODO: recuperar texto de outro lugar
        print 'Customize o tema. Se preencher o css do tema padrão será substituído';
    }

    /**
     * Prints the value of allow guest interaction
     */
    public function print_allow_popup_msg_field_callback()
    {
        //TODO: recuperar HTML de outro local
        printf(
            '<span class="radio"><input type="radio" id="%s" name="%s[%s]" value="%s" %s>SIM</span>',
            self::ALLOW_POPUP_MSG_FIELD_ID . '-allow',
            self::OPTION_NAME,
            self::ALLOW_POPUP_MSG_FIELD_ID,
            self::SECTION_YES_VALUE,
            $this->isPopupMsgAllowed() ? 'checked' : ''
        );

        printf(
            '<span class="radio"><input type="radio" id="%s" name="%s[%s]" value="%s" %s>NÃO</span> ',
            self::ALLOW_POPUP_MSG_FIELD_ID . '-deny',
            self::OPTION_NAME,
            self::ALLOW_POPUP_MSG_FIELD_ID,
            self::SECTION_NO_VALUE,
            !$this->isPopupMsgAllowed() ? 'checked' : ''
        );
    }

    /**
     * Prints textarea to default theme
     */
    public function print_default_theme_css_field_callback()
    {
        
        //TODO: recuperar HTML de outro local
        printf( '<textarea cols="70" rows="20" id="%s" name="%s[%s]">%s</textarea>',
            self::DEFAULT_THEME_CSS_FIELD_ID,
            self::OPTION_NAME,
            self::DEFAULT_THEME_CSS_FIELD_ID,
            $this->getDefaultThemeCss()
        );
    }

    /**
     * Checks if display in pages is enabled
     *
     * @return bool returns TRUE if the user is able, FALSE otherwise
     */
    public function isPopupMsgAllowed()
    {
        return isset($this->options[self::ALLOW_POPUP_MSG_FIELD_ID])
        && $this->options[self::ALLOW_POPUP_MSG_FIELD_ID] == self::SECTION_YES_VALUE;
    }

    /**
     * Get css custom
     * @return string
     */
    public function getDefaultThemeCss()
    {
        // TODO: verificar se é o melhor salvar separado do array

        if (isset($this->options[self::DEFAULT_THEME_CSS_FIELD_ID])) {
            return $this->options[self::DEFAULT_THEME_CSS_FIELD_ID];
        } 

    }


     /**
     * If the query var is set, print the Simple Custom CSS rules.
     */
    public function print_custom_css() {
        // Only print CSS if this is a stylesheet request
        if( ! isset( $_GET['cdbrcss'] ) || intval( $_GET['cdbrcss'] ) !== 1 ) {
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


}

global $PNAThemeOptions;
$PNAThemeOptions = new PNA_Theme_Options();
