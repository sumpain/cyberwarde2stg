<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if( !class_exists( 'cyOtpLogin' ) ) {

class cyOtpLogin   {
        /**
         * Construct the plugin object
         */
        public function __construct()  {
			// Installation and uninstallation hooks
			//register_activation_hook(__FILE__, array(&$this, 'otpl_activate'));
			//register_deactivation_hook(__FILE__, array(&$this, 'otpl_deactivate'));
			//backend hooks action
//			add_filter("plugin_action_links_".plugin_basename(__FILE__), array(&$this,'otpl_settings_link'));
			//add_filter("plugin_action_links_opt-login", array(&$this,'otpl_settings_link'));
			add_action('admin_init', array(&$this, 'otpl_admin_init'));
			add_action('admin_menu', array(&$this, 'otpl_add_menu'));
			add_action( 'admin_bar_menu', array(&$this,'toolbar_link_to_otpl'), 999 );
            
        } // END public function __construct
		/**
		 * hook to add link under adminmenu bar
		 */		
		public function toolbar_link_to_otpl( $wp_admin_bar ) {
			
			$user = wp_get_current_user();
			if ( !current_user_can( 'administrator' ) && is_admin() ) {
				return;
			}
			
			$args = array(
				'id'    => 'otpl_menu_bar',
				'title' => 'OTP Login',
				'href'  => admin_url('options-general.php?page=otp-login'),
				'meta'  => array( 'class' => 'otpl-toolbar-page' )
			);
			$wp_admin_bar->add_node( $args );
			//second lavel
			$wp_admin_bar->add_node( array(
				'id'    => 'otpl-second-sub-item',
				'parent' => 'otpl_menu_bar',
				'title' => 'Settings',
				'href'  => admin_url('options-general.php?page=otp-login'),
				'meta'  => array(
					'title' => __('Settings','otp-login'),
					'target' => '_self',
					'class' => 'otpl_menu_item_class'
				),
			));
		}
		/**
		 * hook into WP's admin_init action hook
		 */
		public function otpl_admin_init()
		{
			// Set up the settings for this plugin
			$this->otpl_init_settings();
			// Possibly do additional admin_init tasks
		} // END public static function activate
		/**
		 * Initialize some custom settings
		 */     
		public function otpl_init_settings()
		{
			// register the settings for this plugin
			register_setting('otpl', 'otpl_enable');
			register_setting('otpl', 'otpl_redirect_url');
			register_setting('otpl', 'otpl_message');
			register_setting('otpl', 'otpl_register_url');
			register_setting('otpl', 'otpl_login_welcomemessage', array('default' => 'Enter your email and click "verify your email" to receive a verification code.'));
			register_setting('otpl', 'otpl_login_verifymessage', array('default' => 'Check your email inbox for your verification code and enter it below.'));
			register_setting('otpl', 'otpl_login_attempt', array('default' => '20'));
			register_setting('otpl', 'otpl_login_locktime', array('default' => '10'));
			register_setting('otpl', 'otpl_login_buttonstepone', array('default' => 'verify your email'));
			register_setting('otpl', 'otpl_login_buttonsteptwo', array('default' => 'Log In'));
			register_setting('otpl', 'otpl_login_emailsubject', array('default' => 'Here is your one-time verification code for {siteinfo}'));
			register_setting('otpl', 'otpl_login_emailtemplate',array('default' => '<table width="50%" cellpadding="0" cellspacing="0" align="center" bgcolor="f5f5f5">
								 <tr>
									<td>
										<table width="650" align="center">
											<tr>
												<td>
													<p class="font_18 pd_lft_25">We have received a one time password request.</p>
													<p class="font_17">Your new OTP is <strong>{otp}</strong></p>
													
														<p class="font_17">Website {website}</p>
												</td>
											</tr>
										</table>

									<table  width="100%" height="40" bgcolor="c5c5c5"  align="center" cellpadding="0" cellspacing="0">
											<tr>
												<td valign="top" align="center">
												<p>This email powered by : <a href="https://www.wp-experts.in">WP-EXPERTS.IN</a></p>
												</td>
											</tr>
										</table>
										</td>
									</tr>
							</table>'));
			register_setting('otpl', 'otpl_login_otp_match', array('default' => 'OTP Matched'));
			register_setting('otpl', 'otpl_login_otp_notmatch', array('default' => 'OTP does not match.'));
			register_setting('otpl', 'otpl_login_otp_resend', array('default' => 'Resend OTP'));
			register_setting('otpl', 'otpl_login_otp_error', array('default' => 'OTP does not exist'));
			register_setting('otpl', 'otpl_login_not_logged_in', array('default' => 'OTP Matched but not logged in'));
			
		} // END public function otpl_init_settings()
		/**
		 * add a menu
		 */     
		public function otpl_add_menu()
		{
			add_options_page('OTP Login Settings', 'OTP Login', 'manage_options', 'otp-login', array(&$this, 'otpl_settings_page'));
		} // END public function add_menu()

		/**
		 * Menu Callback
		 */     
		public function otpl_settings_page()
		{
			if (!current_user_can('manage_options')) {
               wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'otp-login'));
            }

			// Render the settings template
			include_once('lib/settings.php');

			//include(sprintf("%s/css/admin.css", dirname(__FILE__)));
			// Style Files
			//wp_enqueue_style( 'otpl_admin_style', get_stylesheet_directory_uri() .  '/css/otpl-admin.css');
//			wp_enqueue_style( 'otpl_admin_style' );
			// JS files
			//wp_enqueue_script('otpl_admin_script', 'https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js', array('jquery'));
//            wp_enqueue_script('otpl_admin_script');
		} // END public function plugin_settings_page()
        /**
         * Activate the plugin
         */
        public function otpl_activate()
        {
            // Do nothing
        } // END public static function activate
    
        /**
         * Deactivate the plugin
         */     
        public function otpl_deactivate()
        {
            // Do nothing
        } // END public static function deactivate
        // Add the settings link to the plugins page
		public function otpl_settings_link($links)
		{ 
			$settings_link = '<a href="options-general.php?page=otp-login">Settings</a>'; 
			array_unshift($links, $settings_link); 
			return $links; 
		}
    } // END class wp_optimize_site
} // END if(!class_exists('OtpLogin')

if(class_exists('cyOtpLogin'))
{
    // instantiate the plugin class
    $cyOtpLogintemplate = new cyOtpLogin();
}

