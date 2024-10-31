/*
 * Tag Search
 */
jQuery(document).ready(function($) {

	/*
	 * Create trim function for old version of IE
	 */	
	if(typeof String.prototype.trim !== 'function') {
		String.prototype.trim = function() {
			return this.replace(/^\s+|\s+$/g, ''); 
		}
	}

	/*
	 * Add some vars
	 */	
	var mtr_rss_tag_search_textfield = $('.mtr_rss_tag_search .search');
	var mtr_rss_tag_search_tags = $('.mtr_rss_tag_search .mtr_rss_tag_search_tags');
	var mtr_rss_tag_search_tags_cloud = $('.mtr_rss_tag_search .mtr_rss_tag_search_tags .tags');
	var mtr_rss_tag_search_selected_tags = $('.mtr_rss_tag_search .mtr_rss_tag_search_selected_tags');
	var mtr_rss_tag_search_selected_tags_cloud = $('.mtr_rss_tag_search .mtr_rss_tag_search_selected_tags .tags');
	var mtr_rss_tag_search_tag_word = $('.mtr_rss_tag_search .mtr_rss_tag_search_tags .tags span a');
	var mtr_rss_tag_search_selected_word = $('.mtr_rss_tag_search .mtr_rss_tag_search_selected_tags .tags span a');
	var mtr_rss_tag_search_rss_div = $('.mtr_rss_tag_search .mtr_rss_tag_search_rss_feed');
	var mtr_rss_tag_search_rss_div_link = $('.mtr_rss_tag_search .mtr_rss_tag_search_rss_feed a');
	var mtr_rss_tag_search_select_all_tags = $('.mtr_rss_tag_search .select_all_tags');
	var mtr_rss_tag_search_remove_all_tags = $('.mtr_rss_tag_search .remove_all_tags');
	
	/*
	 * Clear value of search textfield
	 */	
	mtr_rss_tag_search_textfield.val('');
	
	/*
	 * Search for tags
	 */	
	mtr_rss_tag_search_textfield.keyup(function(){
		
		// Get textfield value
		var $textfield = $(this).val().trim().toLowerCase();
		
		mtr_rss_tag_search_tag_word.each(function(){
		
			// Get tag data
			var $this = $(this);
			var $this_text = $this.text().toLowerCase();
			
			if($this_text.search($textfield) < 0) {
				$this.addClass('hidden');
			} else {
				$this.removeClass('hidden');
			}
			
		});
		
	});
	
	/*
	 * Add a tag to selected tags
	 */	
	mtr_rss_tag_search_tag_word.click(function(){
	
		// Get tag data
		var $this = $(this);
		var $this_id = $this.attr('class').replace(' selected', '');
		var $this_parent_element = $this.parents('span');
		var $this_html = $this_parent_element.html();
		
		if(mtr_rss_tag_search_selected_tags_cloud.find('a.'+$this_id).length < 1) {
		
			// Add tag to selected tags
			mtr_rss_tag_search_selected_tags_cloud.append('<span>'+$this_html+'</span>');
			
		} else {
		
			// Remove tag from selected tags
			mtr_rss_tag_search_selected_tags_cloud.find('a.'+$this_id).click();
			
		}
		
		// Generate new RSS feed
		generate_rss_feed();
		
		return false;
		
	});

	/*
	 * Remove a tag from selected tags
	 */	
	mtr_rss_tag_search_selected_word.live('click', function(){
	
		// Get tag data
		var $this = $(this);
		var $this_parent_element = $this.parents('span');
		
		// Remove tag
		$this_parent_element.remove();
		
		// Generate new RSS feed
		generate_rss_feed();
		
		return false;
	});
	
	/*
	 * Select all visible tags
	 */	
	mtr_rss_tag_search_select_all_tags.click(function(){
	
		mtr_rss_tag_search_tag_word.each(function(){
		
			// Get tag data
			var $this = $(this);
			
			if(!$this.hasClass('hidden')) {
			
				// Get tag data
				var $this_id = $this.attr('class').replace(' selected', '');
				var $this_parent_element = $this.parents('span');
				var $this_html = $this_parent_element.html();

				if(mtr_rss_tag_search_selected_tags_cloud.find('a.'+$this_id).length < 1) {
				
					// Add tag to selected tags
					mtr_rss_tag_search_selected_tags_cloud.append('<span>'+$this_html+'</span>');
					
				}
				
			}
		});
		
		// Generate new RSS feed
		generate_rss_feed();
		
		return false;
	});
	
	/*
	 * Remove all selected tags
	 */	
	mtr_rss_tag_search_remove_all_tags.click(function(){
	
		// Reset selected tags
		var mtr_rss_tag_search_selected_word = $('.mtr_rss_tag_search .mtr_rss_tag_search_selected_tags .tags span a');
		
		mtr_rss_tag_search_selected_word.each(function(){
		
			// Get tag data
			var $this = $(this);
			
			if(!$this.hasClass('hidden')) {
			
				// Get tag data
				var $this_parent_element = $this.parents('span');
				var $this_id = $this.attr('class');
				
				// Remove tag
				$this_parent_element.remove();
				
				// Add tag to tag cloud
				mtr_rss_tag_search_tags_cloud.find('span.'+$this_id).removeClass('hidden');
		
			}
			
		});
		
		// Generate new RSS feed
		generate_rss_feed();
		
		return false;
	});
	
	/*
	 * Generate new RSS feed
	 */	
	function generate_rss_feed() {
	
		// Add some vars
		var $tags = '';
		var $formatted_tags = '';
		
		// Reset selected tags
		var mtr_rss_tag_search_selected_word = $('.mtr_rss_tag_search .mtr_rss_tag_search_selected_tags .tags span a');
		var mtr_rss_tag_search_selected_word_length = mtr_rss_tag_search_selected_word.length;
		
		if(mtr_rss_tag_search_selected_word.length > 0) {
			mtr_rss_tag_search_rss_div.removeClass('hidden');
			mtr_rss_tag_search_selected_tags.removeClass('hidden');
		} else {
			mtr_rss_tag_search_rss_div.addClass('hidden');
			mtr_rss_tag_search_selected_tags.addClass('hidden');
		}
		
		// Remove selected class of all tags
		mtr_rss_tag_search_tag_word.removeClass('selected');
		
		mtr_rss_tag_search_selected_word.each(function(){
		
			// Get tag data
			var $this = $(this);
			var $this_href = $this.attr('href');
			var $this_class = $this.attr('class');
			var $this_text = $this.text();
			
			// Append string to vars
			$tags += $this_href + '|';
			$formatted_tags += $this_text + '|';
			
			// Add selected class
			mtr_rss_tag_search_tags_cloud.find('a.' + $this_class).addClass('selected');
			
		});			

		// Add params to RSS link
		mtr_rss_tag_search_rss_div_link.attr('tags', $tags);
		mtr_rss_tag_search_rss_div_link.attr('formatted_tags', $formatted_tags);
	}
	
	/*
	 * Send POST request and redirect to RSS feed
	 */	
	$('.mtr_rss_tag_search .mtr_rss_tag_search_rss_feed a').live('click', function(){
		var params = {
			tags: $(this).attr('tags'),
			formatted_tags: $(this).attr('formatted_tags')
		}
		
		$.post(selected_tags_rss_wp_webroot+'?plugin='+selected_tags_rss_folder, params, function(response) {	
			window.location = selected_tags_rss_wp_webroot+selected_tags_rss_permalink+response;
		});
		
		return false;
	});
	
});