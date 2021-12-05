<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Growtype_Registration
 * @subpackage Growtype_Registration/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Growtype_Registration
 * @subpackage Growtype_Registration/admin
 * @author     Your Name <email@example.com>
 */
class Growtype_Registration_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $growtype_registration The ID of this plugin.
     */
    private $growtype_registration;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $growtype_registration The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($growtype_registration, $version)
    {
        $this->Growtype_Registration = $growtype_registration;
        $this->version = $version;

        add_action('admin_menu', array ($this, 'admin_menu'));
        add_action('admin_init', array ($this, 'growtype_registration_options_setting'));
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Growtype_Registration_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Growtype_Registration_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->Growtype_Registration, plugin_dir_url(__FILE__) . 'css/growtype-registration-admin.css', array (), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Growtype_Registration_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Growtype_Registration_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->Growtype_Registration, plugin_dir_url(__FILE__) . 'js/growtype-registration-admin.js', array ('jquery'), $this->version, false);

    }

    /**
     * Register the options page with the Wordpress menu.
     */
    function admin_menu()
    {
        add_options_page(
            'Growtype - Registration',
            'Growtype - Registration',
            'manage_options',
            'growtype-registration-options',
            array ($this, 'growtype_registration_options_content'),
            1
        );
    }

    function growtype_registration_options_content()
    {
        echo '<div class="wrap">
	<h1>Growtype - Registration plugin options</h1>
	<form method="post" action="options.php">';

        settings_fields('growtype_registration_options_settings'); // settings group name
        do_settings_sections('growtype-registration-options'); // just a page slug
        submit_button();

        echo '</form></div>';
    }

    function growtype_registration_options_setting()
    {
        add_settings_section(
            'growtype_registration_options_settings', // section ID
            'Login page details', // title (if needed)
            '', // callback function (if needed)
            'growtype-registration-options' // page slug
        );

        /**
         * Login page
         */
        register_setting(
            'growtype_registration_options_settings', // settings group name
            'growtype_registration_login_page', // option name
            'sanitize_text_field' // sanitization function
        );

        add_settings_field(
            'growtype_registration_login_page',
            'Login Page',
            array ($this, 'growtype_registration_login_page_callback'),
            'growtype-registration-options',
            'growtype_registration_options_settings'
        );

        /**
         * Login page template
         */
        register_setting(
            'growtype_registration_options_settings', // settings group name
            'growtype_registration_login_page_template', // option name
            'sanitize_text_field' // sanitization function
        );

        add_settings_field(
            'growtype_registration_login_page_template',
            'Login Page Template',
            array ($this, 'growtype_registration_login_page_template_callback'),
            'growtype-registration-options',
            'growtype_registration_options_settings'
        );

        /**
         * Register page
         */
        register_setting(
            'growtype_registration_options_settings', // settings group name
            'growtype_registration_signup_page', // option name
            'sanitize_text_field' // sanitization function
        );

        add_settings_field(
            'growtype_registration_signup_page',
            'Register page',
            array ($this, 'growtype_registration_signup_page_callback'),
            'growtype-registration-options',
            'growtype_registration_options_settings'
        );

        /**
         * Redirect after login
         */
        register_setting(
            'growtype_registration_options_settings', // settings group name
            'growtype_registration_redirect_after_login_page', // option name
            'sanitize_text_field' // sanitization function
        );

        add_settings_field(
            'growtype_registration_redirect_after_login_page',
            'Redirect after login to',
            array ($this, 'growtype_registration_redirect_after_login_page_callback'),
            'growtype-registration-options',
            'growtype_registration_options_settings'
        );
    }

    /**
     * Login page
     */
    function growtype_registration_login_page_callback()
    {
        $selected = get_option('growtype_registration_login_page');
        $pages = get_pages();
        ?>
        <select name='growtype_registration_login_page'>
            <?php
            foreach ($pages as $page) { ?>
                <option value='<?= $page->ID ?>' <?php selected($selected, $page->ID); ?>><?= __($page->post_title, "growtype-registration") ?></option>
            <?php } ?>
        </select>
        <?php
    }

    /**
     * Login page template
     */
    function growtype_registration_login_page_template_callback()
    {
        $selected = growtype_registration_get_login_template();
        $options = ['default', 'style-1', 'style-2'];
        ?>
        <select name='growtype_registration_login_page_template'>
            <?php
            foreach ($options as $option) { ?>
                <option value='<?= $option ?>' <?php selected($selected, $option); ?>><?= $option ?></option>
            <?php } ?>
        </select>
        <?php
    }

    /**
     * Register page
     */
    function growtype_registration_signup_page_callback()
    {
        $selected = get_option('growtype_registration_signup_page');
        $pages = get_pages();
        ?>
        <select name='growtype_registration_signup_page'>
            <?php
            foreach ($pages as $page) { ?>
                <option value='<?= $page->ID ?>' <?php selected($selected, $page->ID); ?>><?= __($page->post_title, "growtype-registration") ?></option>
            <?php } ?>
        </select>
        <?php
    }

    /**
     * Redirect after login page
     */
    function growtype_registration_redirect_after_login_page_callback()
    {
        $selected = get_option('growtype_registration_redirect_after_login_page');
        $pages = get_pages();
        ?>
        <select name='growtype_registration_redirect_after_login_page'>
            <option value='dashboard' <?php selected($selected, 'dashboard'); ?>>Dashboard</option>
            <?php
            foreach ($pages as $page) { ?>
                <option value='<?= $page->ID ?>' <?php selected($selected, $page->ID); ?>><?= __($page->post_title, "growtype-registration") ?></option>
            <?php } ?>
        </select>
        <?php
    }
}
