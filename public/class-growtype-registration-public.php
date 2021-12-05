<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Growtype_Registration
 * @subpackage Growtype_Registration/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Growtype_Registration
 * @subpackage Growtype_Registration/public
 * @author     Your Name <email@example.com>
 */
class Growtype_Registration_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $growtype_registration    The ID of this plugin.
	 */
	private $growtype_registration;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $growtype_registration       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $growtype_registration, $version ) {
		$this->Growtype_Registration = $growtype_registration;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
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
		wp_enqueue_style( $this->Growtype_Registration, plugin_dir_url( __FILE__ ) . 'styles/growtype-registration.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

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

		wp_enqueue_script( $this->Growtype_Registration, plugin_dir_url( __FILE__ ) . 'scripts/growtype-registration.js', array( 'jquery' ), $this->version, false );

	}

}
