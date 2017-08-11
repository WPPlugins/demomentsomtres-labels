<?php

    /**
     * @package DeMomentSomTres_Labels
     */
    /*
     Plugin Name: DeMomentSomTres Labels
     Plugin URI: http://demomentsomtres.com/en/wordpress-plugins/demomentsomtres-labels/
     Description: DeMomentSomTres Labels provides label printing based on CSV, HTML and CSS
     Version: 1.4
     Author: DeMomentSomTres
     Author URI: http://www.DeMomentSomTres.com
     License: GPLv2 or later
     */

    /*
     This program is free software; you can redistribute it and/or
     modify it under the terms of the GNU General Public License
     as published by the Free Software Foundation; either version 2
     of the License, or (at your option) any later version.

     This program is distributed in the hope that it will be useful,
     but WITHOUT ANY WARRANTY; without even the implied warranty of
     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     GNU General Public License for more details.

     You should have received a copy of the GNU General Public License
     along with this program; if not, write to the Free Software
     Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
     */

    require_once (dirname(__FILE__) . '/lib/class-tgm-plugin-activation.php');

    define('DMS3LBL_TEXT_DOMAIN', 'DeMomentSomTres-Labels');

    $dms3_labels = new DeMomentSomTresLabels();

    class DeMomentSomTresLabels {
        const TEXT_DOMAIN = DMS3LBL_TEXT_DOMAIN;
        const OPTIONS = 'dms3labels';
        const OPTIONS_TITAN = 'dms3labels_options';
        const OPTIONSFILES = 'dms3_labels_options_files';
        const OPTIONSLABELS = 'dms3_labels_options_labels';
        const MENU_SLUG = 'dms3_labels';
        const MENUCONFIG_SLUG = 'dms3_labels_config';
        const MENUPARAM_SLUG = 'dms3_labels_files';
        const PAGE = 'dms3_labels';
        const PAGEFILES = 'dms3_labels_files';
        const PAGELABELS = 'dms3_labels_print';
        const SECTION_GENERAL = 'general';
        const SECTION_FORMATS = 'formats';
        const SECTION_FILES = 'files';
        const SECTION_LABELS = 'labels';
        const OPTION_NUM = 'number';
        const OPTION_NAME = 'name';
        const OPTION_ID = 'columnaID';
        const OPTION_COLS = 'colsperrow';
        const OPTION_ROWS = 'rowsperpage';
        const OPTION_HTML = 'html';
        const OPTION_FILE = 'fileid';
        const OPTION_LABELS = 'labels';
        const OPTION_FORMAT = 'format';
        const OPTION_CSS = 'css';
        const OPTION_CAPABILITY = 'capability';
        //v1.1+
        const OPTION_HEADER = 'header';
        //+v1.3

        private $pluginURL;
        private $pluginPath;
        private $langDir;

        /**
         * @since 1.0
         */
        function __construct() {
            $this -> pluginURL = plugin_dir_url(__FILE__);
            $this -> pluginPath = plugin_dir_path(__FILE__);
            $this -> langDir = dirname(plugin_basename(__FILE__)) . '/languages';

            add_action('plugins_loaded', array(
                $this,
                'plugin_init'
            ));
            add_action('tgmpa_register', array(
                $this,
                'required_plugins'
            ));
            // add_action('tf_create_options', array(
            // $this,
            // 'administracio'
            // ));
            add_action('admin_menu', array(
                $this,
                'admin_menu'
            ));
            add_action('admin_init', array(
                $this,
                'admin_init'
            ));
            add_action('init', array(
                $this,
                'download'
            ));
            add_shortcode('dms3L', array(
                $this,
                'dms3L'
            ));
        }

        /**
         * @since 1.0
         */
        function plugin_init() {
            load_plugin_textdomain(DMS3LBL_TEXT_DOMAIN, false, $this -> langDir);
        }

        /**
         * @since 1.1
         */
        function required_plugins() {
            $plugins = array(
                // array(
                // 'name' => 'Titan Framework',
                // 'slug' => 'titan-framework',
                // 'required' => true
                // ),
                array(
                    'name' => 'DeMomentSomTres Tools',
                    'slug' => 'demomentsomtres-tools',
                    'required' => true
                ), );
            $config = array(
                'default_path' => '', // Default absolute path to pre-packaged plugins.
                'menu' => 'tgmpa-install-plugins', // Menu slug.
                'has_notices' => true, // Show admin notices or not.
                'dismissable' => false, // If false, a user cannot dismiss the nag message.
                'dismiss_msg' => __('Some plugins are missing!', self::TEXT_DOMAIN), // If 'dismissable' is false, this message will be output at top of nag.
                'is_automatic' => false, // Automatically activate plugins after installation or not.
                'message' => __('This are the required plugins', self::TEXT_DOMAIN), // Message to output right before the plugins table.
                'strings' => array(
                    'page_title' => __('Install Required Plugins', self::TEXT_DOMAIN),
                    'menu_title' => __('Install Plugins', self::TEXT_DOMAIN),
                    'installing' => __('Installing Plugin: %s', self::TEXT_DOMAIN), // %s = plugin name.
                    'oops' => __('Something went wrong with the plugin API.', self::TEXT_DOMAIN),
                    'notice_can_install_required' => _n_noop('This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                    'notice_can_install_recommended' => _n_noop('This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                    'notice_cannot_install' => _n_noop('Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                    'notice_can_activate_required' => _n_noop('The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                    'notice_can_activate_recommended' => _n_noop('The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                    'notice_cannot_activate' => _n_noop('Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                    'notice_ask_to_update' => _n_noop('The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                    'notice_cannot_update' => _n_noop('Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                    'install_link' => _n_noop('Begin installing plugin', 'Begin installing plugins', self::TEXT_DOMAIN),
                    'activate_link' => _n_noop('Begin activating plugin', 'Begin activating plugins', self::TEXT_DOMAIN),
                    'return' => __('Return to Required Plugins Installer', self::TEXT_DOMAIN),
                    'plugin_activated' => __('Plugin activated successfully.', self::TEXT_DOMAIN),
                    'complete' => __('All plugins installed and activated successfully. %s', self::TEXT_DOMAIN), // %s = dashboard link.
                    'nag_type' => 'error' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
                )
            );
            tgmpa($plugins, $config);
        }

        /**
         * @since v2.0
         */
        function administracio() {
            $titan = TitanFramework::getInstance(self::OPTIONS);
            $panel = $titan -> createAdminPanel(array(
                'name' => __('Etiquetes', self::TEXT_DOMAIN),
                'title' => __('DeMomentSomTres Etiquetes', self::TEXT_DOMAIN),
                'desc' => __("Imprimeix etiquetes de producte", self::TEXT_DOMAIN),
                'icon' => 'dashicons-tag',
                'id' => self::MENU_SLUG,
                'capability' => 'read',
            ));
            $panelfiles = $panel -> createAdminPanel(array(
                'name' => __('Fitxers', self::TEXT_DOMAIN),
                'title' => __('DeMomentSomTres Etiquetes - Fitxers', self::TEXT_DOMAIN),
                'desc' => __("Càrrega dels fitxers de preus", self::TEXT_DOMAIN),
                'id' => self::MENUPARAM_SLUG,
                'capability' => 'manage_options',
            ));
            $panelconfig = $panel -> createAdminPanel(array(
                'name' => __('Configuració', self::TEXT_DOMAIN),
                'title' => __('DeMomentSomTres Etiquetes - Configuració', self::TEXT_DOMAIN),
                'desc' => __("Paràmetres generals de configuració", self::TEXT_DOMAIN),
                'id' => self::MENUCONFIG_SLUG,
                'capability' => 'manage_options',
            ));

            $tabconfiggeneral = $panelconfig -> createTab(array(
                'name' => __('General', self::TEXT_DOMAIN),
                'title' => __('Paràmetres generals', self::TEXT_DOMAIN),
                'desc' => __("Permet administrar els paràmetres generals de l'aplicació.", self::TEXT_DOMAIN) . '<br/>' . "<strong>" . __("Canvis en aquesta secció tenen impacte en les altres pestanyes.", self::TEXT_DOMAIN) . "</strong>",
                'id' => 'main',
            ));
            $tabconfiggeneral -> createOption(array(
                'name' => __('Nombre de configuracions i fitxers', self::TEXT_DOMAIN),
                'id' => self::OPTION_NUM,
                'type' => 'number',
                'min' => 0,
                'max' => 20,
            ));
            $tabconfiggeneral -> createOption(array(
                'name' => __('Estils CSS comuns', self::TEXT_DOMAIN),
                'id' => self::OPTION_CSS,
                'type' => 'textarea',
                'is_code' => true,
            ));
            $num = $this -> getNumber2();
            $params = $this -> getParams();
            $tabconfigs = array();
            $tabfiles = array();
            for ($i = 0; $i < $num; $i++) :
                $tabconfigs[$i] = $panelconfig -> createTab(array(
                    'name' => $i,
                    'title' => $i,
                    'id' => 'format-' . $i,
                ));
                $tabconfigs[$i] -> createOption(array(
                    'name' => __('Nom del format', self::TEXT_DOMAIN),
                    'id' => self::OPTION_NAME . "-$i",
                    'type' => 'text',
                ));
                $tabconfigs[$i] -> createOption(array(
                    'name' => __("Columna d'identificador", self::TEXT_DOMAIN),
                    'desc' => __('Número de la columna que conté el camp que identifica el producte', self::TEXT_DOMAIN),
                    'id' => self::OPTION_ID . "-$i",
                    'type' => 'number',
                    'default' => 1,
                    'max' => 20,
                ));
                $tabconfigs[$i] -> createOption(array(
                    'name' => __("Columnes d'etiquetes per fila"),
                    'desc' => __("Nombre de columnes a cada fila d'etiquetes", self::TEXT_DOMAIN),
                    'id' => self::OPTION_COLS . "-$i",
                    'type' => 'number',
                    'default' => 1,
                    'max' => 20,
                ));
                $tabconfigs[$i] -> createOption(array(
                    'name' => __("Files d'etiquetes per pàgina", self::TEXT_DOMAIN),
                    'desc' => __("Nombre de files a cada pàgina d'etiquetes", self::TEXT_DOMAIN),
                    'id' => self::OPTION_ROWS . "-$i",
                    'type' => 'number',
                    'default' => 1,
                    'max' => 20,
                ));
                $tabconfigs[$i] -> createOption(array(
                    'name' => __("Especificació del format d'etiqueta", self::TEXT_DOMAIN),
                    'desc' => __("Estructura completa d'una de les etiquetes d'aquest format. Per indicar el camp 3 del fitxer cal usar el shortcode [dms3L id=3]", self::TEXT_DOMAIN) . "<br/>" . __("El shortcode [dms3L] admet el paràmetre iconclass per retornar &lt;span class='icon <em>iconclass</em>'&gt;&lt;/span&gt; quan el camp contingui dades.", self::TEXT_DOMAIN),
                    'id' => self::OPTION_HTML . "-$i",
                    'type' => 'textarea',
                    'is_code' => true,
                ));
                $tabfiles[$i] = $panelfiles -> createTab(array(
                    'name' => $params[self::OPTION_NAME . "-$i"],
                    'title' => $params[self::OPTION_NAME . "-$i"],
                    'id' => 'file-' . $i,
                ));
                $tabfiles[$i] -> createOption(array(
                    'name' => __("Fitxer", self::TEXT_DOMAIN),
                    'id' => self::OPTION_FILE . "-$i",
                    'type' => 'upload',
                    'placeholder' => $params[self::OPTION_FILE . "-$i"],
                ));
            endfor;
            $panel -> createOption(array(
                'type' => "save",
                'save' => __("Desa els canvis", self::TEXT_DOMAIN),
                'use_reset' => false
            ));
            $panelfiles -> createOption(array(
                'type' => "save",
                'save' => __("Desa els canvis", self::TEXT_DOMAIN),
                'use_reset' => false
            ));
            $panelconfig -> createOption(array(
                'type' => "save",
                'save' => __("Desa els canvis", self::TEXT_DOMAIN),
                'use_reset' => false
            ));
        }

        /**
         * @since 2.0
         */
        function getNumber2() {
            $oldoptions = unserialize(get_option(self::OPTIONS_TITAN));
            $number = !empty($oldoptions[self::OPTION_NUM]) ? $oldoptions[self::OPTION_NUM] : 0;
            return $number;
        }

        /**
         * @since 2.0
         */
        function getParams() {
            $oldoptions = unserialize(get_option(self::OPTIONS_TITAN));
            return $oldoptions;
        }

        /**
         * @since 1.0
         */
        function admin_menu() {
            add_menu_page(__('DeMomentSomTres Etiquetes', self::TEXT_DOMAIN), __('Etiquetes', self::TEXT_DOMAIN), 'read', self::MENU_SLUG, array(
                $this,
                'admin_page_labels'
            ), 'dashicons-tag');
            add_submenu_page(self::MENU_SLUG, __('DeMomentSomTres Etiquetes - Fitxers', self::TEXT_DOMAIN), __('Fitxers', self::TEXT_DOMAIN), 'manage_options', self::MENUPARAM_SLUG, array(
                $this,
                'admin_page_files'
            ));
            add_submenu_page(self::MENU_SLUG, __('DeMomentSomTres Etiquetes - Configuració', self::TEXT_DOMAIN), __('Configuració', self::TEXT_DOMAIN), 'manage_options', self::MENUCONFIG_SLUG, array(
                $this,
                'admin_page'
            ));
        }

        /**
         * @since 1.0
         */
        function admin_page() {
            echo '<div class="wrap"><h2>' . __('DeMomentSomTres Etiquetes', self::TEXT_DOMAIN) . '</h2><form action="options.php" method="post">';
            settings_errors(self::OPTIONS);
            settings_fields(self::OPTIONS);
            do_settings_sections(self::PAGE);
            echo '<input name="Submit" class="button button-primary" type="submit" value="' . __('Desar', self::TEXT_DOMAIN) . '"/>';
            echo '</form></div>';
            // echo '<div style="background-color:#eee;display:none;"><h2>' . __('Options', self::TEXT_DOMAIN) . '</h2><pre style="font-size:0.8em;">';
            // print_r(get_option(self::OPTIONS));
            // echo '</pre></div>';
        }

        /**
         * @since 1.0
         */
        function admin_page_files() {
            echo '<div class="wrap"><h2>' . __('DeMomentSomTres Etiquetes - Càrrega de fitxers', self::TEXT_DOMAIN) . '</h2><form action="options.php" method="post" enctype="multipart/form-data">';
            settings_errors(self::OPTIONSFILES);
            settings_fields(self::OPTIONSFILES);
            do_settings_sections(self::PAGEFILES);
            echo '<input name="Submit" class="button button-primary" type="submit" value="' . __('Desa els canvis', self::TEXT_DOMAIN) . '"/>';
            echo '</form></div>';
            // echo '<div style="background-color:#eee;"><h2>' . __('Options', self::TEXT_DOMAIN) . '</h2><pre style="font-size:0.8em;">';
            // print_r(get_option(self::OPTIONSFILES));
            // echo '</pre></div>';
        }

        /**
         * @since 1.0
         */
        function admin_page_labels() {
            wp_enqueue_script('dms3-labels', $this -> pluginURL . "/js/demomentsomtres-labels.js");
            echo '<div class="wrap"><h2>' . __('DeMomentSomTres Etiquetes - Impressió', self::TEXT_DOMAIN) . '</h2>';
            settings_errors(self::OPTIONSLABELS);
            $num = $this -> get_number();
            for ($i = 0; $i < $num; $i++) :
                $capability = $this -> get_format_capability($i);
                if (!$capability || current_user_can($capability)) :
                    echo '<form action="options.php?dms3LabelDownload" method="post" id="form-' . $i . '">';
                    settings_fields(self::OPTIONSLABELS . '_' . $i);
                    do_settings_sections(self::PAGELABELS . '_' . $i);
                    echo '<input name="Submit" class="button button-primary" type="submit" formtarget="_blank" value="' . sprintf(__('Imprimeix etiquetes %s', self::TEXT_DOMAIN), $this -> get_format_name($i)) . '"/>';
                    echo '</form>';
                endif;
            endfor;
            echo '</div>';
        }

        /**
         * @since 1.0
         */
        function admin_init() {
            $num = $this -> get_number();
            register_setting(self::OPTIONS, self::OPTIONS, array(
                $this,
                'admin_validate_options'
            ));
            register_setting(self::OPTIONSFILES, self::OPTIONSFILES, array(
                $this,
                'admin_validate_options_files'
            ));
            for ($i = 0; $i < $num; $i++) :
                register_setting(self::OPTIONSLABELS . '_' . $i, self::OPTIONSLABELS . '_' . $i, array(
                    $this,
                    'admin_validate_options_labels'
                ));
            endfor;

            add_settings_section(self::SECTION_GENERAL, __('General', self::TEXT_DOMAIN), array(
                $this,
                'admin_section_general'
            ), self::PAGE);
            add_settings_field(self::OPTION_NUM, __('Nombre de configuracions i fitxers', self::TEXT_DOMAIN), array(
                $this,
                'admin_field_number'
            ), self::PAGE, self::SECTION_GENERAL);
            add_settings_field(self::OPTION_CSS, __('Estils CSS comuns', self::TEXT_DOMAIN), array(
                $this,
                'admin_field_css'
            ), self::PAGE, self::SECTION_GENERAL);

            for ($i = 0; $i < $num; $i++) :
                add_settings_section(self::SECTION_FORMATS . '_' . $i, sprintf(__('Format %s', self::TEXT_DOMAIN), $this -> get_format_name($i)), array(
                    $this,
                    'admin_section_formats'
                ), self::PAGE, array('index' => $i));
                add_settings_field(self::OPTION_NAME . '_' . $i, __('Nom del format', self::TEXT_DOMAIN), array(
                    $this,
                    'admin_field_format_name'
                ), self::PAGE, self::SECTION_FORMATS . '_' . $i, array('index' => $i));
                add_settings_field(self::OPTION_ID . '_' . $i, __("Columna d'identificador", self::TEXT_DOMAIN), array(
                    $this,
                    'admin_field_format_id'
                ), self::PAGE, self::SECTION_FORMATS . '_' . $i, array('index' => $i));
                add_settings_field(self::OPTION_HEADER . '_' . $i, __("Files de capçalera", self::TEXT_DOMAIN), array(
                    $this,
                    'admin_field_format_header'
                ), self::PAGE, self::SECTION_FORMATS . '_' . $i, array('index' => $i));
                add_settings_field(self::OPTION_COLS . '_' . $i, __('Columnes d&apos;etiquetes per fila', self::TEXT_DOMAIN), array(
                    $this,
                    'admin_field_format_cols'
                ), self::PAGE, self::SECTION_FORMATS . '_' . $i, array('index' => $i));
                add_settings_field(self::OPTION_ROWS . '_' . $i, __('Files d&apos;etiquetes per pàgina', self::TEXT_DOMAIN), array(
                    $this,
                    'admin_field_format_rows'
                ), self::PAGE, self::SECTION_FORMATS . '_' . $i, array('index' => $i));
                add_settings_field(self::OPTION_HTML . '_' . $i, __("Especificació del format d'etiqueta", self::TEXT_DOMAIN), array(
                    $this,
                    'admin_field_format_html'
                ), self::PAGE, self::SECTION_FORMATS . '_' . $i, array('index' => $i));
                add_settings_field(self::OPTION_CAPABILITY . '_' . $i, __("Capability", self::TEXT_DOMAIN), array(
                    $this,
                    'admin_field_format_capability'
                ), self::PAGE, self::SECTION_FORMATS . '_' . $i, array('index' => $i));
            endfor;

            add_settings_section(self::SECTION_FILES, __('Fitxers', self::TEXT_DOMAIN), array(
                $this,
                'admin_section_files'
            ), self::PAGEFILES);
            for ($i = 0; $i < $num; $i++) :
                add_settings_field(self::OPTION_FILE . '_' . $i, sprintf(__('Fitxer %s', self::TEXT_DOMAIN), $this -> get_format_name($i)), array(
                    $this,
                    'admin_field_file'
                ), self::PAGEFILES, self::SECTION_FILES, array('index' => $i));
            endfor;

            for ($i = 0; $i < $num; $i++) :
                // $capability = $this -> get_format_capability($i);
                // if (!$capability || current_user_can($capability)) :
                add_settings_section(self::SECTION_LABELS . '_' . $i, sprintf(__('Etiquetes %s %s', self::TEXT_DOMAIN), $this -> get_format_name($i), $capability), array(
                    $this,
                    'admin_section_labels'
                ), self::PAGELABELS . '_' . $i, array('index' => $i));
                add_settings_field(self::OPTION_LABELS . '_' . $i, __('Elements', self::TEXT_DOMAIN) . "<br/><button class='button button-primary tots'>" . __("Tots", self::TEXT_DOMAIN) . "</button><br/><button class='button button-primary cap'>" . __("Cap", self::TEXT_DOMAIN) . "</button>", array(
                    $this,
                    'admin_field_labels'
                ), self::PAGELABELS . '_' . $i, self::SECTION_LABELS . '_' . $i, array('index' => $i));
                // endif;
            endfor;
        }

        /**
         * @since 1.0
         */
        function admin_validate_options($input = array()) {
            $inputCSS = $input[self::OPTION_CSS];
            $inputFormats = $input[self::SECTION_FORMATS];
            $input = DeMomentSomTresTools::adminHelper_esc_attr($input);
            foreach ($inputFormats as $k => $data) :
                $input[self::SECTION_FORMATS][$k][self::OPTION_HTML] = $data[self::OPTION_HTML];
            endforeach;
            $input[self::OPTION_CSS] = $inputCSS;
            return $input;
        }

        /**
         * @since 1.0
         */
        function admin_validate_options_labels($input = array()) {
            echo '<pre>';
            print_r($input);
            exit ;
            return;
        }

        /**
         * @since 1.0
         */
        function admin_validate_options_files($input = array()) {
            $options = get_option(self::OPTIONSFILES);
            $num = $this -> get_number();
            // echo '<pre>' . print_r($_FILES, true) . '</pre>';
            // exit ;
            for ($i = 0; $i < $num; $i++) :
                $theFile = $_FILES[$i];
                // if a files was uploaded
                if (0 == $theFile['error']) :
                    // if it is an csv
                    // if (preg_match('/(txt|csv)$/', $theFile['type'])) : // MQB- v1.1
                    // MQB+ v1.1 start
                    $filetype = wp_check_filetype($theFile['name']);
                    $type = $filetype["ext"];
                    if ($type != "csv" || $type != "txt") :
                        // MQB+ v1.1 end
                        $override = array('test_form' => false);
                        // save the file, and store an array, containing its location in $file
                        $file = wp_handle_upload($theFile, $override);
                        if ($file && !isset($file["error"])) :
                            $options[$i] = $file['url'];
                        else :
                            add_settings_error(self::OPTIONSFILES, "GENERAL", sprintf(__("ERROR: %s", self::TEXT_DOMAIN), $file['error']), "error");
                        endif;
                    else :
                        add_settings_error(self::OPTIONSFILES, "NOCSVORTXT", sprintf(__("Estàs provant de carregar un fitxer %s que no és .csv ni .txt!", self::TEXT_DOMAIN), $type), "error");
                    endif;
                endif;
            endfor;
            return $options;
        }

        /**
         * @since 1.0
         */
        function admin_section_general() {
            echo '<p>' . __('Configuració dels paràmetres generals de l&apos;aplicació', self::TEXT_DOMAIN) . '</p>';
            echo '<p><strong>' . __('Canvis en aquesta secció tenen conseqüència sobre la secció següent tan bon punt es desa', self::TEXT_DOMAIN) . '</strong></p>';
        }

        /**
         * @since 1.0
         */
        function admin_section_formats() {
            echo '<p>' . __('Paràmetres d&apos;aquesta visualització', self::TEXT_DOMAIN) . '</p>';
        }

        /**
         * @since 1.0
         */
        function admin_section_files() {
            echo '<p>' . __('Carrega els fitxers de preus.', self::TEXT_DOMAIN) . '</p>';
            echo '<p><strong>' . __('ATENCIÓ: els fitxers han d&apos;estar en format de caràcter ISO-8859-1 que és l&apos;estàndard de Windows.', self::TEXT_DOMAIN) . '</strong></p>';
            echo '<p>' . __('Recorda que pots inserir el contingut &lt;br/&gt; per forçar un canvi de línia en un camp', self::TEXT_DOMAIN) . '</p>';
        }

        /**
         * @since 1.0
         */
        function admin_section_labels() {
            echo '<p>' . __('Escolliu els elements per generar les etiquetes', self::TEXT_DOMAIN) . '</p>';
        }

        /**
         * @since 1.0
         */
        function admin_field_number() {
            $name = self::OPTION_NUM;
            $value = $this -> get_number();
            DeMomentSomTresTools::adminHelper_inputArray(self::OPTIONS, $name, $value, array('type' => 'number'));
        }

        /**
         * @since 1.1
         */
        function admin_field_css() {
            $name = self::OPTION_CSS;
            $value = $this -> get_css();
            DeMomentSomTresTools::adminHelper_inputArray(self::OPTIONS, $name, $value, array('type' => 'textarea'));
        }

        /**
         * @since 1.0
         */
        function admin_field_format_name(array $args) {
            $i = $args['index'];
            $prefix = self::OPTIONS . '[' . self::SECTION_FORMATS . ']' . '[' . $i . ']';
            $name = self::OPTION_NAME;
            $value = DeMomentSomTresTools::get_option($prefix, $name);
            DeMomentSomTresTools::adminHelper_inputArray($prefix, $name, $value, array());
        }

        /**
         * @since 1.0
         */
        function admin_field_format_id(array $args) {
            $i = $args['index'];
            $prefix = self::OPTIONS . '[' . self::SECTION_FORMATS . ']' . '[' . $i . ']';
            $name = self::OPTION_ID;
            $value = DeMomentSomTresTools::get_option($prefix, $name);
            DeMomentSomTresTools::adminHelper_inputArray($prefix, $name, $value, array('type' => "number"));
            echo "<p style='font-size:75%;'>" . __("Número de la columna que conté el camp que identifica el producte", self::TEXT_DOMAIN) . '</p>';
        }

        /**
         * @since 1.3
         */
        function admin_field_format_header(array $args) {
            $i = $args['index'];
            $prefix = self::OPTIONS . '[' . self::SECTION_FORMATS . ']' . '[' . $i . ']';
            $name = self::OPTION_HEADER;
            $value = DeMomentSomTresTools::get_option($prefix, $name);
            DeMomentSomTresTools::adminHelper_inputArray($prefix, $name, $value, array('type' => "number"));
            echo "<p style='font-size:75%;'>" . __("Nombre de files a ignorar al principi del fitxer", self::TEXT_DOMAIN) . '</p>';
        }

        /**
         * @since 1.0
         */
        function admin_field_format_cols(array $args) {
            $i = $args['index'];
            $prefix = self::OPTIONS . '[' . self::SECTION_FORMATS . ']' . '[' . $i . ']';
            $name = self::OPTION_COLS;
            $value = DeMomentSomTresTools::get_option($prefix, $name);
            DeMomentSomTresTools::adminHelper_inputArray($prefix, $name, $value, array('type' => "number"));
            echo "<p style='font-size:75%;'>" . __("Nombre de columnes a cada fila d'etiquetes", self::TEXT_DOMAIN) . '</p>';
        }

        /**
         * @since 1.0
         */
        function admin_field_format_rows(array $args) {
            $i = $args['index'];
            $prefix = self::OPTIONS . '[' . self::SECTION_FORMATS . ']' . '[' . $i . ']';
            $name = self::OPTION_ROWS;
            $value = DeMomentSomTresTools::get_option($prefix, $name);
            DeMomentSomTresTools::adminHelper_inputArray($prefix, $name, $value, array('type' => "number"));
            echo "<p style='font-size:75%;'>" . __("Nombre de files a cada pàgina d'etiquetes", self::TEXT_DOMAIN) . '</p>';
        }

        /**
         * @since 1.0
         */
        function admin_field_format_HTML(array $args) {
            $i = $args['index'];
            $prefix = self::OPTIONS . '[' . self::SECTION_FORMATS . ']' . '[' . $i . ']';
            $name = self::OPTION_HTML;
            $value = DeMomentSomTresTools::get_option($prefix, $name);
            DeMomentSomTresTools::adminHelper_inputArray($prefix, $name, $value, array(
                'type' => 'textarea',
                'rows' => 5
            ));
            echo "<p style='font-size:75%;'>" . __("Estructura completa d'una de les etiquetes d'aquest format. Per indicar el camp 3 del fitxer cal usar el shortcode [dms3L id=3]", self::TEXT_DOMAIN) . '</p>';
            echo "<p style='font-size:75%;'>" . __("El shortcode [dms3L] admet el paràmetre iconclass per retornar &lt;span class='icon <em>iconclass</em>'&gt;&lt;/span&gt; quan el camp contingui dades.", self::TEXT_DOMAIN) . '</p>';
            echo "<p style='font-size:75%;'>" . __("El shortcode [dms3L] admet també els paràmetres <em>classyes</em> i <em>classno</em> per retornar ' class=\"l'string vinculada al paràmetre\" quan el camp contingui dades o no.", self::TEXT_DOMAIN) . '</p>';
        }

        /**
         * @since 1.1
         */
        function admin_field_format_capability(array $args) {
            $i = $args['index'];
            $prefix = self::OPTIONS . '[' . self::SECTION_FORMATS . ']' . '[' . $i . ']';
            $name = self::OPTION_CAPABILITY;
            $value = DeMomentSomTresTools::get_option($prefix, $name);
            DeMomentSomTresTools::adminHelper_inputArray($prefix, $name, $value, array());
            echo "<p style='font-size:75%;'>" . __("Capability necessària per poder veure les etiquetes d'aquest format.", self::TEXT_DOMAIN) . "<br/>" . __("Si es deixa buit l'accés serà sense restriccions.") . '</p>';
        }

        /**
         * @since 1.0
         */
        function admin_field_file(array $args) {
            $i = $args['index'];
            $prefix = self::OPTIONSFILES;
            $name = $i;
            $value = $this -> get_file($i);
            echo $value . '<br/>';
            _e('Substituir el fitxer per', self::TEXT_DOMAIN);
            DeMomentSomTresTools::adminHelper_inputArray('', $i, '', array('type' => 'file'));
        }

        /**
         * @since 1.0
         */
        function admin_field_labels(array $args) {
            $i = $args['index'];
            $prefix = self::OPTIONSLABELS . '_' . $i;
            DeMomentSomTresTools::adminHelper_inputArray($prefix, self::OPTION_FORMAT, $i, array('type' => 'hidden'));
            $records = $this -> csvToArray($i);
            $id = $this -> get_format_columnID($i);
            $c = 1;
            $colsperrow = 8;
            $rowstart = "<div style='clear:both;display:table-row;'>";
            echo $rowstart;
            foreach ($records as $n => $record) :
                echo "<div style='display:table-cell;border:1px solid #c5c5c5;text-align:center;'>";
                DeMomentSomTresTools::adminHelper_inputArray($prefix . '[records]', $n, '', array('type' => 'checkbox'));
                echo '<br/>' . $record[$id];
                echo "</div>";
                if (0 == $c % $colsperrow) :
                    echo "</div>";
                    echo $rowstart;
                endif;
                $c++;
            endforeach;
            echo "</div>";
        }

        /**
         * @since 1.0
         */
        function csvToArray($i) {
            $file = $this -> get_file($i);
            if ($file) :
                $header = $this -> get_format_header($i);
                //v1.3+
                $text = file_get_contents($file);
                $lines = explode(chr(13) . chr(10), $text);
                foreach ($lines as $n => $line) :
                    $fila = str_getcsv($line, ";");
                    $novafila = array();
                    $filaPlena = FALSE;
                    foreach ($fila as $i => $t) :
                        if ($t) :
                            $t = str_replace(chr(128), '', $t);
                            $trad = htmlentities($t, ENT_COMPAT | ENT_HTML401, "ISO-8859-1");
                            $trad = str_replace("&lt;br/&gt;", "<br/>", $trad);
                            $novafila[$i] = $trad;
                            $filaPlena = TRUE;
                        endif;
                    endforeach;
                    if ($filaPlena && $n > $header) ://v1.3 updated
                        $csv[] = $novafila;
                    endif;
                endforeach;
            else :
                $csv = array();
            endif;
            return $csv;
        }

        /**
         * @since 1.0
         */
        function get_file($i) {
            $prefix = self::OPTIONSFILES;
            $name = $i;
            $value = DeMomentSomTresTools::get_option($prefix, $name);
            return $value;
        }

        /**
         * @since 1.0
         */
        function get_number() {
            if (!class_exists("DeMomentSomTresTools")) :// v1.2+
                return 0;
            // v1.2+
            endif;                                       // v1.2+
            $name = self::OPTION_NUM;
            return DeMomentSomTresTools::get_option(self::OPTIONS, $name);
        }

        /**
         * @since 1.1
         */
        function get_css() {
            $name = self::OPTION_CSS;
            return DeMomentSomTresTools::get_option(self::OPTIONS, $name);
        }

        /**
         * @since 1.0
         */
        function get_format_name($i) {
            $prefix = self::OPTIONS . '[' . self::SECTION_FORMATS . ']' . '[' . $i . ']';
            $name = self::OPTION_NAME;
            $value = DeMomentSomTresTools::get_option($prefix, $name);
            if (!$value) :
                $value = '' . $i;
            endif;
            return $value;
        }

        /**
         * @since 1.0
         */
        function get_format_html($i) {
            $prefix = self::OPTIONS . '[' . self::SECTION_FORMATS . ']' . '[' . $i . ']';
            $name = self::OPTION_HTML;
            $value = DeMomentSomTresTools::get_option($prefix, $name);
            return $value;
        }

        /**
         * @since 1.0
         */
        function get_format_cols($i) {
            $prefix = self::OPTIONS . '[' . self::SECTION_FORMATS . ']' . '[' . $i . ']';
            $name = self::OPTION_COLS;
            $value = DeMomentSomTresTools::get_option($prefix, $name);
            return $value;
        }

        /**
         * @since 1.0
         */
        function get_format_rows($i) {
            $prefix = self::OPTIONS . '[' . self::SECTION_FORMATS . ']' . '[' . $i . ']';
            $name = self::OPTION_ROWS;
            $value = DeMomentSomTresTools::get_option($prefix, $name);
            return $value;
        }

        /**
         * @since 1.0
         */
        function get_format_columnID($i) {
            $prefix = self::OPTIONS . '[' . self::SECTION_FORMATS . ']' . '[' . $i . ']';
            $name = self::OPTION_ID;
            $value = DeMomentSomTresTools::get_option($prefix, $name) - 1;
            return $value;
        }

        /**
         * @since 1.3
         */
        function get_format_header($i) {
            $prefix = self::OPTIONS . '[' . self::SECTION_FORMATS . ']' . '[' . $i . ']';
            $name = self::OPTION_HEADER;
            $value = DeMomentSomTresTools::get_option($prefix, $name, 0) - 1;
            return $value;
        }

        /**
         * Returns a capability if a capabilty is defined for the format. Otherwise FALSE is returned.
         * @since 1.1
         */
        function get_format_capability($i) {
            $prefix = self::OPTIONS . '[' . self::SECTION_FORMATS . ']' . '[' . $i . ']';
            $name = self::OPTION_CAPABILITY;
            $value = DeMomentSomTresTools::get_option($prefix, $name);
            if ($value == "") :
                return false;
            else :
                return $value;
            endif;
        }

        /**
         * @since 1.0
         */
        function dms3L($atts) {
            extract(shortcode_atts(array(
                'id' => '1',
                'r' => 0,
                'f' => 0,
                'iconclass' => '',
                'classyes' => '',
                'classno' => ''
            ), $atts));
            $csv = $this -> csvToArray($f);
            $record = $csv[$r];
            $field = $record[$id - 1];
            if ($classyes || $classno) :
                if ($field!="") :
                    return " class='$classyes' ";
                else :
                    return " class='$classno' ";
                endif;
                endif;
                if ($iconclass) :
                    if ($field) :
                        return "<span class='icon $iconclass'></span>";
                    endif;
                endif;
            return $field;
        }

        /**
         * @since 1.0
         */
        function download() {
            if (isset($_GET['dms3LabelDownload'])) :
                $title = get_bloginfo('name') . ' - ' . __("impressi&oacute; d'etiquetes");
                $num = $this -> get_number();
                $style = $this -> get_css();
                for ($i = 0; $i < $num; $i++) :
                    if (isset($_POST[self::OPTIONSLABELS . '_' . $i])) :
                        $format = $i;
                        $records = array_keys($_POST[self::OPTIONSLABELS . '_' . $i]['records']);
                        break;
                    endif;
                endfor;
                if ($i < $num) :
                    echo "<html>";
                    echo "<head>";
                    echo '<meta http-equiv="content-type" content="text/html;charset=UTF-8">';
                    echo "<title>$title</title>";
                    echo "<style>$style</style>";
                    echo "</head>";
                    echo "<body style='margin:0;'>";
                    echo $this -> generar_etiquetes($format, $records);
                    echo '<script type="text/javascript">';
                    echo 'print();';
                    echo '</script>';
                    echo "</body>";
                    echo "</html>";
                endif;
                die();
            endif;
        }

        /**
         * @since 1.0
         */
        function generar_etiquetes($format, $records) {
            $template = $this -> get_format_html($format);
            $cols = $this -> get_format_cols($format);
            $rows = $this -> get_format_rows($format);
            if ($cols == 0)
                $cols = 100;
            if ($rows == 0)
                $rows = 100;

            $csv = $this -> csvToArray($format);

            $preLabel = array();

            foreach ($records as $r) :
                $theTemplate = str_replace('[dms3L', "[dms3L f=$format r=$r", $template);
                $preLabel[] = $theTemplate;
            endforeach;

            $rowstart = "<div style='clear:both;position:relative;'>";
            $rowstart_page_break = "<div style='clear:both;position:relative;page-break-before:always;'>";
            $rowend = '</div>';
            $c = 1;
            $r = 1;
            $output = $rowstart;
            foreach ($preLabel as $shc) :
                $output .= do_shortcode($shc);
                if (0 == $c % $cols) :
                    $output .= $rowend;
                    if (0 == $r % $rows) :
                        $output .= $rowstart_page_break;
                    else :
                        $output .= $rowstart;
                    endif;
                    $r++;
                endif;
                $c++;
            endforeach;
            $output .= $rowend;
            return $output;
        }

    }
?>