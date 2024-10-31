<?php
class MTR_Selected_Tags_RSS_Search extends WP_Widget {

	const widget_title = 'Selected Tags RSS';

	function __construct() {
		parent::__construct(false, self::widget_title);
	}

	function widget($args, $instance) {
		extract($args);
		
		$tags = get_tags();
		if(isset($tags) && count($tags)) {
			echo $before_widget;

			$html .= '<div class="mtr_rss_tag_search">';
				
				$html .= (isset($instance['title']) && $instance['title'] ? $before_title.$instance['title'].$after_title : '');
				$html .= (isset($instance['description']) && $instance['description'] ? '<p>'.$instance['description'].'</p>' : '');
			
				$html .= '<div class="mtr_rss_tag_search_filter">';
					$html .= '<label for="mtr_rss_tag_search_filter">Tag Search</label>';			
					$html .= '<input id="mtr_rss_tag_search_filter" type="text" name="" class="search" />';			
				$html .= '</div>';
				
				$html .= '<div class="mtr_rss_tag_search_tags">';
					$html .= '<div class="head">Tags | <a title="Select all" class="select_all_tags" href="#">select all</a></div>';
					$html .= '<div class="tags">';
						foreach($tags as $k => $tag) {
							$html .= '<span class="tag_'.$tag->term_id.'"><a title="'.$tag->name.'" class="tag_'.$tag->term_id.'" href="'.$tag->slug.'">'.$tag->name.'</a></span> ';
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
			
			echo $html;
			echo $after_widget;
		}						
	}

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] 				= strip_tags($new_instance['title']);
		$instance['description'] 		= $new_instance['description'];
		return $instance;
    }

	function form($instance) {	
		$title			= (isset($instance['title']) && $instance['title']) ? esc_attr($instance['title']) : self::widget_title;
		$description	= (isset($instance['description']) && $instance['description']) ? esc_attr($instance['description']) : '';
		
		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('description'); ?>">Description:</label>
				<textarea class="widefat" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>"><?php echo $description; ?></textarea>
			</p>
		<?php
	}

}

function MTR_Selected_Tags_RSS_Widgets() {
	register_widget('MTR_Selected_Tags_RSS_Search');
}

