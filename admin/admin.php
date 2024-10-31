<?php
/**
 * MTR_Selected_Tags_RSS_Admin
 */

if (!class_exists('MTR_Selected_Tags_RSS_Admin')) {
	class MTR_Selected_Tags_RSS_Admin {
	
		function __construct() {
			add_action('admin_menu', array(&$this, 'generate_navigation'));
			add_action('init', array(&$this, 'load_styles'));
			$this->generate_header();
			$this->generate_footer();
		}
		
		function generate_navigation()  {
			add_menu_page('Selected Tags RSS Settigs', 'Selected Tags RSS', 'add_users', MTR_SELECTED_TAGS_RSS_SETTINGS, array(&$this, 'process_pages'), MTR_SELECTED_TAGS_RSS_URLPATH.'admin/images/nav.png');
			add_submenu_page(MTR_SELECTED_TAGS_RSS_SETTINGS, 'Settings', 'Settings', 'add_users', MTR_SELECTED_TAGS_RSS_SETTINGS, array(&$this, 'process_pages')); 
			//add_submenu_page(MTR_SELECTED_TAGS_RSS_SETTINGS, 'Subscribers', 'Subscribers', 'add_users', MTR_SELECTED_TAGS_RSS_SUBSCRIBERS, array(&$this, 'process_pages')); 
		}
		
		function generate_header() {	
			$page_title = '';
			$page_button = '';
			
			switch ($_GET['page']) {
				default:
					$page_title = 'Settings';
				break;
			}
			
			DEFINE('MTR_SELECTED_TAGS_RSS_HEADER', '
				<div class="icon32"><br /></div>		
				<h2>
					Selected Tags RSS
					<span>
						by MTR Design
					</span> 
					'.$page_button.'
				</h2>
			');
		}

		function generate_footer() {
			DEFINE('MTR_SELECTED_TAGS_RSS_FOOTER', '
				<p>
					
					The plugin is developed by
					<a title="MTR Design" target="_blank" href="http://mtr-design.com/">MTR Design</a>.
				</p>
			');
		}
		
		function load_styles() {
			wp_enqueue_style('mtr-rss-feed', MTR_SELECTED_TAGS_RSS_URLPATH.'admin/css/styles.css', false, '', 'screen');
		}
		
		function process_pages(){
			
			// Change nav icon
			echo '	
				<script type="text/javascript">
					document.getElementById("toplevel_page_selected-tags-rss-settings").getElementsByTagName("img")[0].src = "'.MTR_SELECTED_TAGS_RSS_URLPATH.'admin/images/nav-current.png";
				</script>
			';
			
			// Include DB queries
			include_once(MTR_SELECTED_TAGS_RSS_DOCROOT.'/lib/db.php');

			switch ($_GET['page']) {
				// Settings tab
				case MTR_SELECTED_TAGS_RSS_SETTINGS:
				
					// Get settings
					$mtr_selected_tags_rss_db = new MTR_Selected_Tags_RSS_DB();
					$data = $mtr_selected_tags_rss_db->get_settings();
					
					if(isset($_POST['action']) && $_POST['action'] == 'settings_update') {
						$callback = $mtr_selected_tags_rss_db->update_settings($_POST);
						wp_redirect('?page='.MTR_SELECTED_TAGS_RSS_SETTINGS); 
						exit;
					}
					
					// Add view
					include_once(MTR_SELECTED_TAGS_RSS_DOCROOT.'/admin/settings.php');
					mtr_selected_tags_rss_settings($data, $errors);
					
				break;
				default:
					// Todo: subscribers table
				break;
			}
		}

	}
}