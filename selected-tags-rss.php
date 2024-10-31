<?php
/*
Plugin Name: Selected Tags RSS
Plugin URI: http://mtr-design.com
Description: Enable seprate RSS feed covering different combinations of tags. Each feed will only list entries that match at least one of the feed's tags.
Version: 1.3
Author: MTR Design
Author URI: http://mtr-design.com
*/

if (!class_exists('MTR_Selected_Tags_RSS')) {
	class MTR_Selected_Tags_RSS {

		/*
		 * Class construct 
		 */		
		function __construct() {
			ob_start(); 
			$this->define_constant();
			
			// Register plugin hooks
			register_activation_hook(MTR_SELECTED_TAGS_RSS_DOCROOT.'/'.MTR_SELECTED_TAGS_RSS_FILENAME, array(&$this, 'activate') );
			register_deactivation_hook(MTR_SELECTED_TAGS_RSS_DOCROOT.'/'.MTR_SELECTED_TAGS_RSS_FILENAME, array(&$this, 'deactivate') );
			register_uninstall_hook(MTR_SELECTED_TAGS_RSS_DOCROOT.'/'.MTR_SELECTED_TAGS_RSS_FILENAME, array('MTR_Selected_Tags_RSS', 'uninstall') );
			
			// Include widget file
			require_once(MTR_SELECTED_TAGS_RSS_DOCROOT.'/widgets/widgets.php');
			add_action('widgets_init', 'MTR_Selected_Tags_RSS_Widgets');

			if(is_admin()) {
				require_once(MTR_SELECTED_TAGS_RSS_DOCROOT.'/admin/admin.php');
				$mtr_selected_tags_rss_admin = new MTR_Selected_Tags_RSS_Admin();
			} else {
				
				add_filter('the_content', array(&$this, 'add_mtr_rss_tag_search'));
			
				add_action('init', array(&$this, 'load_share'));
				add_action('init', array(&$this, 'generate_feed_rewrite'));
				add_action('init', array(&$this, 'view_feed'));
				add_action('init', array(&$this, 'add_subscriber'));
				
				add_shortcode('selected-tags-rss-post', array(&$this, 'shortcode_mtr_selected_tags_rss_post'));
				add_shortcode('selected-tags-rss-search', array(&$this, 'shortcode_mtr_selected_tags_rss_search'));
			
			}
		}

		/*
		 * Add subscriber 
		 */	
		function add_subscriber($content) { 
			if(isset($_GET['plugin']) && $_GET['plugin'] == MTR_SELECTED_TAGS_RSS_FOLDER && isset($_POST['tags']) && $_POST['tags'] && isset($_POST['formatted_tags']) && $_POST['formatted_tags']) {
				
				// Create unique hash
				$_POST['hash'] = md5($_POST['tags'].time());
				
				// Include DB queries
				include_once(MTR_SELECTED_TAGS_RSS_DOCROOT.'/lib/db.php');
				$mtr_selected_tags_rss_db = new MTR_Selected_Tags_RSS_DB();
				$data = $mtr_selected_tags_rss_db->add_subscriber($_POST);

				// Return hash to AJAX
				exit($_POST['hash']);
				
			}	
		}
		
		/*
		 * Add markup for tag search and rss feed link
		 */	
		function add_mtr_rss_tag_search($content) {
			global $post;
					
			include_once(MTR_SELECTED_TAGS_RSS_DOCROOT.'/lib/db.php');
			$mtr_selected_tags_rss_db = new MTR_Selected_Tags_RSS_DB();
			$data = $mtr_selected_tags_rss_db->get_settings();
						
			$html = '';
			if(isset($post->ID)) {
				if($post->post_type == 'page') {
					if(isset($data['settings'][0]->page_paths)) {
						$paths = unserialize($data['settings'][0]->page_paths);
						if(is_array($paths) && count($paths)) {
							if(in_array((int)$post->ID, $paths)) {
								$tags = get_tags();
								if(isset($tags) && count($tags)) {
									$html .= '<div class="mtr_rss_tag_search">';
											
										$html .= '<div class="mtr_rss_tag_search_filter">';
											$html .= '<label for="mtr_rss_tag_search_filter">Tag Search</label>';			
											$html .= '<input id="mtr_rss_tag_search_filter" type="text" name="" class="search" />';			
										$html .= '</div>';
				
										$html .= '<div class="mtr_rss_tag_search_tags">';
											$html .= '<div class="head">Tags | <a title="Select all" class="select_all_tags" href="#">select all</a></div>';
											$html .= '<div class="tags">';
												foreach($tags as $k => $tag) {
													$html .= '<span class="tag_'.$tag->term_id.'"><a title="'.$tag->name.'" class="tag_'.$tag->term_id.'" href="'.$tag->slug.'">'.$tag->name.'</a></span>';
												}
											$html .= '</div>';
										$html .= '</div>';
												
										$html .= '<div class="mtr_rss_tag_search_selected_tags hidden">';	
											$html .= '<div class="head">Selected Tags | <a title="Remove all" class="remove_all_tags" href="#">remove all</a></div>';
											$html .= '<div class="tags"></div>';
										$html .= '</div>';
												
										$html .= '<div class="mtr_rss_tag_search_rss_feed hidden">';	
											$html .= '<a title="Generate RSS Feed" href="#">Generate RSS Feed</a>';
										$html .= '</div>';
				
									$html .= '</div>';
								}	
							}
						}
					}
				} else if($post->post_type == 'post' && is_single()) {
					if(isset($data['settings'][0]->show_rss_posts)) {
						if($data['settings'][0]->show_rss_posts == 'yes') {
							$post_tags = wp_get_post_tags($post->ID);
							if(is_array($post_tags) && count($post_tags)) {
								$rss_link = array();
								foreach($post_tags as $tag) {
									$rss_link['formatted_tags'][] = $tag->name;
									$rss_link['tags'][] = $tag->slug;
								}
								$html .= '<div class="mtr_rss_tag_search">';
										
									$html .= '<div class="mtr_rss_tag_search_rss_feed is_single">';	
										$html .= '<a title="'.esc_attr($data['settings'][0]->link_label).'" href="#" tags="'.implode('|', $rss_link['tags']).'|" formatted_tags="'.implode('|', $rss_link['formatted_tags']).'|">'.esc_attr($data['settings'][0]->link_label).'</a>';
									$html .= '</div>';
										
								$html .= '</div>';
							}
						}
					}
				}
			}					
			return $content.$html;
		}
		
		/*
		 * Add markup for rss feed link into template engine
		 */	
		function get_selected_tags_RSS() {
			global $post;

			if($post->post_type == 'post') {
				$post_tags = wp_get_post_tags($post->ID);
				if(is_array($post_tags) && count($post_tags)) {
					
					include_once(MTR_SELECTED_TAGS_RSS_DOCROOT.'/lib/db.php');
					$mtr_selected_tags_rss_db = new MTR_Selected_Tags_RSS_DB();
					$data = $mtr_selected_tags_rss_db->get_settings();
			
					$rss_link = array();
					foreach($post_tags as $tag) {
						$rss_link['formatted_tags'][] = $tag->name;
						$rss_link['tags'][] = $tag->slug;
					}
					$html .= '<div class="mtr_rss_tag_search">';
										
						$html .= '<div class="mtr_rss_tag_search_rss_feed is_single">';	
						$html .= '<a title="'.esc_attr($data['settings'][0]->link_label).'" href="#" tags="'.implode('|', $rss_link['tags']).'|" formatted_tags="'.implode('|', $rss_link['formatted_tags']).'|">'.esc_attr($data['settings'][0]->link_label).'</a>';
						$html .= '</div>';
										
					$html .= '</div>';
				}
			}
			
			return $html;
		}

		/*
		 * Add markup for rss feed link
		 */			
		function shortcode_mtr_selected_tags_rss_post($atts){
			global $post;
			$html = '';
					
			extract(shortcode_atts(array(
				'title' => 'Generate RSS feed with these tags'
			), $atts));
					
			if($post->post_type == 'post' && is_single()) {
				$post_tags = wp_get_post_tags($post->ID);
				if(is_array($post_tags) && count($post_tags)) {
					$rss_link = array();
					foreach($post_tags as $tag) {
						$rss_link['formatted_tags'][] = $tag->name;
						$rss_link['tags'][] = $tag->slug;
					}
					$html .= '<div class="mtr_rss_tag_search">';
							
						$html .= '<div class="mtr_rss_tag_search_rss_feed is_single">';	
							$html .= '<a title="'.$title.'" href="#" tags="'.implode('|', $rss_link['tags']).'|" formatted_tags="'.implode('|', $rss_link['formatted_tags']).'|">'.$title.'</a>';
						$html .= '</div>';
										
					$html .= '</div>';
				}
			}
					
			return $html;
		}
		
		/*
		 * Add markup for tag search
		 */			
		function shortcode_mtr_selected_tags_rss_search($atts){
			global $post;
			$html = '';
					
			extract(shortcode_atts(array(
				'title' => 'Generate RSS feed with these tags'
			), $atts));
					
			if(isset($post->ID)) { 
				if($post->post_type == 'page') {
				$tags = get_tags(); 
					if(isset($tags) && count($tags)) { 
						$html .= '<div class="mtr_rss_tag_search">';
											
							$html .= '<div class="mtr_rss_tag_search_filter">';
								$html .= '<label for="mtr_rss_tag_search_filter">Tag Search</label>';			
								$html .= '<input id="mtr_rss_tag_search_filter" type="text" name="" class="search" />';			
							$html .= '</div>';
					
							$html .= '<div class="mtr_rss_tag_search_tags">';
								$html .= '<div class="head">Tags | <a title="Select all" class="select_all_tags" href="#">select all</a></div>';
								$html .= '<div class="tags">';
									foreach($tags as $k => $tag) {
										$html .= '<span class="tag_'.$tag->term_id.'"><a title="'.$tag->name.'" class="tag_'.$tag->term_id.'" href="'.$tag->slug.'">'.$tag->name.'</a></span>';
									}
								$html .= '</div>';
							$html .= '</div>';
													
							$html .= '<div class="mtr_rss_tag_search_selected_tags hidden">';	
								$html .= '<div class="head">Selected Tags | <a title="Remove all" class="remove_all_tags" href="#">remove all</a></div>';
								$html .= '<div class="tags"></div>';
							$html .= '</div>';
													
							$html .= '<div class="mtr_rss_tag_search_rss_feed hidden">';	
								$html .= '<a title="'.$title.'" href="#">'.$title.'</a>';
							$html .= '</div>';
				
						$html .= '</div>';
					}	
				}
			}

			return $html;					
		}		
			
		/*
		 * Create RSS url address
		 */			
		function generate_feed_rewrite() {
			// Add rewrite rules
			add_rewrite_tag('%selected-tags-rss%','[^/]+');
			add_rewrite_rule('feed/tags/([^/]+)$', 'index.php?selected-tags-rss=$matches[1]', 'top');
			
			// Flush rules			
			global $wp_rewrite;
			$wp_rewrite->flush_rules(false);  
		}
		
		/*
		 * View RSS feed by users
		 */		
		function view_feed() {
			
			$uri = array();
			$have_permalink = true;
			$show_feed = false;
			
			if(isset($_GET['selected-tags-rss']) && !empty($_GET['selected-tags-rss'])) {
				$have_permalink = false;	
				$show_feed = true;	
				$uri['request_el'][0] = $_GET['selected-tags-rss'];
			} else {
				// Explode url and get params
				$uri['request_uri'] = $_SERVER['REQUEST_URI'];
				$uri['request_uri_array'] = explode('/', $uri['request_uri']); 
				
				// 0 = hash, 1 = tag, 2 = feed
				$uri['request_el'][0] = end($uri['request_uri_array']);
				$uri['request_el'][1] = prev($uri['request_uri_array']);
				$uri['request_el'][2] = prev($uri['request_uri_array']);
				
				if($uri['request_el'][2] == 'feed' && $uri['request_el'][1] == 'tag') {
					$show_feed = true;	
				}
			}
			
			if($show_feed && !empty($uri['request_el'][0])) {
	
				// Include DB queries
				include_once(MTR_SELECTED_TAGS_RSS_DOCROOT.'/lib/db.php');
				$mtr_selected_tags_rss_db = new MTR_Selected_Tags_RSS_DB();

				// Get RSS feed		
				$data = $mtr_selected_tags_rss_db->get_subscriber($uri['request_el'][0]);
				
				// Get error if empty feed			
				if(!isset($data[0]) || (isset($data[0]) && !$data[0]->tags)) {
					wp_redirect('/'); 
					exit;
				}
				
				$tags = unserialize($data[0]->tags);
				
				// Get error if empty slugs	
				if(!isset($tags['slugs']) || (isset($tags['slugs']) && count($tags['slugs']) < 1)) {
					wp_redirect('/'); 
					exit;
				}
				
				// Get error if empty tags	
				if(!isset($tags['tags']) || (isset($tags['tags']) && count($tags['tags']) < 1)) {
					wp_redirect('/'); 
					exit;
				}
				
				// Convert array to string
				$tags['slugs'] = implode(',', $tags['slugs']);
				$tags['tags'] = implode(', ', $tags['tags']);
				
				// Get posts from tags		
				query_posts(array('tag' => $tags['slugs']));
					
				// Change RSS feed title	
				add_filter('wp_title_rss', 'feed_title');
				define('MTR_SELECTED_TAGS_RSS_FEED_TITLE', $tags['tags']);			
				
				function feed_title($data) {
					return sprintf(' - Selected Tags RSS Feed (%1$s)', MTR_SELECTED_TAGS_RSS_FEED_TITLE);
				}
				
				// Include RSS markup
				include(ABSPATH.WPINC.'/feed-rss2.php');
				
				exit;
				
			}
		}

		/*
		 * Load styles and scripts
		 */			
		function load_share() {
			wp_enqueue_style('mtr-rss-tag-search-styles', MTR_SELECTED_TAGS_RSS_URLPATH.'share/css/styles.css', false, '', 'screen');
			
			wp_enqueue_script('jquery');
			wp_enqueue_script('mtr-rss-tag-search-js-common', MTR_SELECTED_TAGS_RSS_URLPATH.'share/jscripts/common.js', array('jquery'));

			add_action('wp_head', array(&$this, 'add_custom_script'));
		}

		/*
		 * Load custom header scripts
		 */			
		function add_custom_script() {
			?>
				<script>	
					var selected_tags_rss_folder = '<?php echo MTR_SELECTED_TAGS_RSS_FOLDER ?>';
					var selected_tags_rss_wp_webroot = '<?php echo get_bloginfo('siteurl') ?>/';
					var selected_tags_rss_permalink = '<?php echo (get_option('permalink_structure')) ? 'feed/tag/' : '?selected-tags-rss=' ?>';
				</script>
			<?php
		}

		/*
		 * Activate function
		 */			
		function activate() {
			// Include DB queries
			require_once(MTR_SELECTED_TAGS_RSS_DOCROOT.'/admin/install.php');
			$mtr_selected_tags_install = new MTR_Selected_Tags_RSS_Install();
			
			// Start activation process
			$mtr_selected_tags_install->activate();
		}

		/*
		 * Deactivate function
		 */		
		function deactivate() {
			// Include DB queries
			require_once(MTR_SELECTED_TAGS_RSS_DOCROOT.'/admin/install.php');
			$mtr_selected_tags_install = new MTR_Selected_Tags_RSS_Install();
			
			// Start deactivation process
			$mtr_selected_tags_install->deactivate();
		}

		/*
		 * Uninstall function
		 */	
		function uninstall() {
			// Include DB queries
			require_once(MTR_SELECTED_TAGS_RSS_DOCROOT.'/admin/install.php');
			$mtr_selected_tags_install = new MTR_Selected_Tags_RSS_Install();
			
			// Start uninstall process
			$mtr_selected_tags_install->uninstall();
		}

		/*
		 * Define some constants
		 */			
		function define_constant() {
			define('MTR_SELECTED_TAGS_RSS_DOCROOT', 	dirname(__FILE__));
			define('MTR_SELECTED_TAGS_RSS_FOLDER', 		basename(dirname(__FILE__)));
			define('MTR_SELECTED_TAGS_RSS_URLPATH', 	trailingslashit(plugins_url(MTR_SELECTED_TAGS_RSS_FOLDER)));
			define('MTR_SELECTED_TAGS_RSS_FILENAME',	basename(__FILE__));
			define('MTR_SELECTED_TAGS_RSS_SETTINGS', 	MTR_SELECTED_TAGS_RSS_FOLDER.'/settings');
			define('MTR_SELECTED_TAGS_RSS_SUBSCRIBERS', MTR_SELECTED_TAGS_RSS_FOLDER.'/subscribers');
		}
		
		/*
		 * Class destruct
		 */	
		function __destruct() {
			ob_flush();
		}

	}
}

$mtr_selected_tags_rss = new MTR_Selected_Tags_RSS();

function get_selected_tags_RSS() {
	global $mtr_selected_tags_rss;	
	return $mtr_selected_tags_rss->get_selected_tags_RSS();
}
