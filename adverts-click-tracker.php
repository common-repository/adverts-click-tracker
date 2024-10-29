<?php
/*
 * Plugin Name: Wordpress Adverts Plugin - Track Link & Button Clicks
 * Plugin URI: https://extend-wp.com/wordpress-adverts-click-tracker/
 * Description: Create and display classified adverts  anywhere on your WordPress website with a shortcode, click tracking feature - check the statistics.
 * Version: 1.3
 * Author: extendWP
 * Author URI: https://extend-wp.com
 * License: GPL2
 * Created On: 27-09-2018
 * Updated On: 21-12-2023
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


include_once( plugin_dir_path(__FILE__) ."/class-admin.php"); 
 
 class AdvertsClickTracker extends AdvertsClickTrackerAdmin{
		
		public $plugin = 'AdvertsClickTracker';		
		public $name = 'Adverts Click Tracker';
		public $shortName = 'Adverts Click Tracker';
		public $slug = 'adverts-click-tracker';
		public $dashicon = 'dashicons-editor-table';
		public $proUrl = 'https://extend-wp.com/product/wordpress-adverts-click-tracker/';
		public $menuPosition ='50';
		public $localizeBackend;
		public $localizeFrontend;
		public $description = 'Create and display classified adverts anywhere on your website with a shortcode, track the click links and check the statistics.';
		
		
 
		public function __construct() {
			
			add_action('wp_enqueue_scripts', array($this, 'FrontEndScripts') );
			add_action('admin_enqueue_scripts', array($this, 'BackEndScripts') );
			add_filter('widget_text', 'do_shortcode');			
			add_action('admin_menu', array($this, 'SettingsPage') );
			add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this, 'Links') );
			

			
			register_activation_hook( __FILE__,  array($this, 'onActivation') );
			add_action('plugins_loaded', 'translate');			
			add_action("init", array($this,"createPostType_Cat" ) );
			add_action("init", array($this,"createTables" ) );
			
			add_action("admin_init", array($this, 'adminPanels') );	
			add_action("admin_init", array($this,"metaBox" ) );
			add_action("save_post", array($this,"saveFields" ) );
			
			add_shortcode('displayAds', array($this,'displayAds'));
			add_shortcode('adStats', array($this,'adStats'));
						
			add_action( 'wp_footer', array($this,'advertClickEvent') );
			add_action( 'wp_ajax_advert_click_counter', array($this,'advert_click_counter') );
			add_action( 'wp_ajax_nopriv_advert_click_counter', array($this,'advert_click_counter') );
			
			add_action( 'before_delete_post', array($this,'deleteAds') );
			add_action("admin_init", array($this,"deleteAdStats" ) );
			
			add_action("admin_footer", array($this,"proModal" ) );
		}

		
		public function translate() {
			load_plugin_textdomain( $this->plugin, false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
		}

		public function onActivation(){
			require_once(ABSPATH .'/wp-admin/includes/plugin.php');
			$pro = "/adverts-click-tracker-pro/adverts-click-tracker-pro.php";
			deactivate_plugins($pro);	
		}
		
		public function BackEndScripts(){
			wp_enqueue_style( $this->plugin."adminCss", plugins_url( "/css/backend.css", __FILE__ ) );	
			wp_enqueue_style( $this->plugin."adminCss");	
			
			wp_enqueue_script('jquery');
            wp_enqueue_script( 'jquery-ui-datepicker' ); // enqueue datepicker from WP
		    wp_enqueue_style( 'jquery-ui-style', plugins_url( "/css/jquery-ui.css", __FILE__ ), true);
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-accordion');
			wp_enqueue_style( 'wp-color-picker' ); 			
			wp_enqueue_media();
			if( ! wp_script_is( $this->plugin."_fa", 'enqueued' ) ) {
				wp_enqueue_style( $this->plugin."_fa", plugins_url( '/css/font-awesome.min.css', __FILE__ ));
			}		
			wp_enqueue_script( $this->plugin."adminJs", plugins_url( "/js/backend.js", __FILE__ ) , array('jquery','jquery-ui-core','jquery-ui-accordion','wp-color-picker') , null, true);	
						
			$this->localizeBackend = array( 
				'plugin_url' => plugins_url( '', __FILE__ ),
				'siteUrl'	=>	site_url(),
				'plugin_wrapper'=> $this->plugin,
				//'nonce' => wp_create_nonce( 'wp_rest' )		
			);		
			wp_localize_script($this->plugin."adminJs", $this->plugin , $this->localizeBackend );
			wp_enqueue_script( $this->plugin."adminJs");
		}
		
		public function FrontEndScripts(){
			wp_enqueue_style( $this->plugin."css", plugins_url( "/css/frontend.css", __FILE__ ) );	
			wp_enqueue_style( $this->plugin."css");
				
			wp_enqueue_script('jquery');
			
			wp_enqueue_script( $this->plugin."js", plugins_url( "/js/frontend.js", __FILE__ ) , array('jquery') , null, true);	
			
			$this->localizeFrontend = array( 
				'plugin_url' => plugins_url( '', __FILE__ ),
				'siteUrl'	=>	site_url(),
				'plugin_wrapper'=> $this->plugin,
			);		
			wp_localize_script($this->plugin."js", $this->plugin , $this->localizeFrontend );
			wp_enqueue_script( $this->plugin."js");
		}		
		
		public function SettingsPage(){
			//add_menu_page($this->name, $this->name , 'administrator', $this->slug, array($this, 'init') , $this->dashicon, $this->menuPosition );	
			add_submenu_page( 'edit.php?post_type=webd_adverts', $this->shortName. " Settings", 'Settings', 'manage_options', $this->slug, array($this, 'init') );	
			add_submenu_page( 'edit.php?post_type=webd_adverts', $this->shortName. " Category", 'Category - PRO', 'manage_options', $this->slug."-Category", array($this, 'initStats') );
			add_submenu_page( 'edit.php?post_type=webd_adverts', $this->shortName. " Statistics", 'Statistics', 'manage_options', $this->slug."-Stats", array($this, 'initStats') );
			
			
		}
		
		public function Links($links){
			$mylinks[] =  '<a href="' . admin_url( "admin.php?page=".$this->slug ) . '">Settings</a>';
			$mylinks[] = "<a href='".$this->proUrl."' target='_blank'>PRO Version</a>";
			return array_merge( $links, $mylinks );			
		}

		public function init(){
			print "<div class='".$this->plugin."'>";
			$this->adminHeader();
			$this->adminSettings();
			$this->adminFooter();
			print "</div>";
		}
		public function initStats(){
			print "<div class='".$this->plugin."'>";
			$this->adminHeader();
			echo "<h3>". __('Statistics',$this->plugin) ."</h3>";
			echo do_shortcode( '[adStats]' );
			
			$this->adminFooter();
			print "</div>";
		}
		
 }
 $initialize = new AdvertsClickTracker();