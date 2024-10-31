<?php
/**
 * MTR_Selected_Tags_RSS_Install
 */
 
if (!class_exists('MTR_Selected_Tags_RSS_Install')) {
	class MTR_Selected_Tags_RSS_Install {
		
		var $htaccess = '';
		
		function __construct() {
			include_once(MTR_SELECTED_TAGS_RSS_DOCROOT.'/lib/db.php');
			$mtr_selected_tags_rss_db = new MTR_Selected_Tags_RSS_DB();
			
			$this->db 				= $mtr_selected_tags_rss_db->db;
			$this->db_settings 		= $mtr_selected_tags_rss_db->db_settings;
			$this->db_subscribers 	= $mtr_selected_tags_rss_db->db_subscribers;
		}
		
		function activate() {
			
			$this->db->query("
				CREATE TABLE IF NOT EXISTS `".$this->db_settings."` (
				  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `link_label` varchar(255) NOT NULL DEFAULT 'Generate RSS feed with these tags',
				  `page_paths` text NOT NULL,
				  `show_rss_posts` set('no','yes') NOT NULL DEFAULT 'no',
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
			");
			
			$this->db->query("
				INSERT INTO `".$this->db_settings."` VALUES ('1', 'Generate RSS feed with these tags', '', 'no');
			");
			
			$this->db->query("
				CREATE TABLE IF NOT EXISTS `".$this->db_subscribers."` (
				  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `hash` varchar(32) NOT NULL,
				  `host` varchar(100) NOT NULL,
				  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `tags` text NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=172 ;
			");
			
		}
		
		function uninstall() {
			$this->db->query('DROP TABLE IF EXISTS `'.$this->db_settings.'`');
			$this->db->query('DROP TABLE IF EXISTS `'.$this->db_subscribers.'`');
		}

		function deactivate() {
			return false;
		}
		
	}
}
