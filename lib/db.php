<?php
/**
 * MTR_Selected_Tags_RSS_DB
 * All db queries
 */

if (!class_exists('MTR_Selected_Tags_RSS_DB')) {
	class MTR_Selected_Tags_RSS_DB {
		
		/*
		 * Class construct 
		 */	
		function __construct() {
			global $wpdb;

			// Define DB tables
			$this->db 				= $wpdb;
			$this->db_settings 		= $wpdb->prefix.'mtr_selected_tags_rss_settings';
			$this->db_subscribers 	= $wpdb->prefix.'mtr_selected_tags_rss_subscribers';
		}

		/*
		 * Subscribers
		 */
		function add_subscriber($_data) {
			if(isset($_data['tags']) && isset($_data['formatted_tags'])) {
				if(!empty($_data['tags']) && !empty($_data['formatted_tags'])) {
					
					$tags = array();
					$tags['slugs'] = explode('|', esc_attr($_data['tags']));
					$tags['tags'] = explode('|', esc_attr($_data['formatted_tags']));
					
					unset($tags['slugs'][count($tags['slugs']) - 1]);
					unset($tags['tags'][count($tags['tags']) - 1]);
					
					$query = '
						INSERT INTO `'.$this->db_subscribers.'` 
						SET
							`hash` = %s,
							`host` = %s,
							`tags` = %s,
							`date` = NOW()
					';
					$this->db->query($this->db->prepare($query, $_data['hash'], $_SERVER['REMOTE_ADDR'], serialize($tags)));
				}
			}
			return true;
		}

		function get_subscriber($_hash) {
			if(isset($_hash) && !empty($_hash)) {
				$query = 'SELECT * FROM `'.$this->db_subscribers.'` WHERE `hash` = %s';
				return $this->db->get_results($this->db->prepare($query, $_hash));
			}
		}
		
		/*
		 * Settings
		 */
		function update_settings($_data) {
			if(isset($_data['show_rss_posts']) && !empty($_data['show_rss_posts'])) {
				$query = '
					UPDATE `'.$this->db_settings.'` 
					SET
						`link_label` = %s,
						`page_paths` = %s,
						`show_rss_posts` = %s
					WHERE `id` = 1
				';
				$this->db->query($this->db->prepare($query, $_data['link_label'], serialize($_data['page_paths']), $_data['show_rss_posts']));
			}
			return true;
		}
		
		function get_settings() {
			$query = 'SELECT ns.* FROM  `'.$this->db_settings.'` AS ns WHERE ns.id = 1';
			$settings = $this->db->get_results($query);
			
			return array('settings' => $settings);
		}	
		
	}
}