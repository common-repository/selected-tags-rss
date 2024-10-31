<?php
/**
 * mtr_rss_tag_search_settings()
 *
 * @return mixed content
 */
 
function mtr_selected_tags_rss_settings($data, $errors) {
	$pages = get_pages(array('post_type' => 'page', 'number' => 0, 'post_status' => 'publish,private,draft,pending'));
	$tags = get_tags();
	$path = array();
	if(isset($data['settings'][0]->page_paths ) && $data['settings'][0]->page_paths) {
		$path = unserialize($data['settings'][0]->page_paths );
	}
?>
<?php if(!count($tags)) { ?>
	<div id="message" class="error">
		<p>
			<strong>- No tags</strong><br />
		</p>
	</div>
<?php } ?>
<div id="mtr-selected-tags-rss" class="wrap">
	<?php echo MTR_SELECTED_TAGS_RSS_HEADER ?>
	<div id="poststuff">
		<?php if(isset($errors) && $errors) { ?>
			<div id="message" class="error">
				<p>
					<?php foreach($errors as $error) { ?>
						<strong>- <?php echo $error ?></strong><br />
					<?php } ?>
				</p>
			</div>
		<?php } ?>
		<form action="" method="post" onsubmit="selectAllOptions('db_path')">
			<div id="post-body" class="metabox-holder columns-1">
				<div id="post-body-content">
					<div class="postbox">
						<h3 class="hndle"><span>Settings</span></h3>
						<div class="inside">
							<div class="form-field form-required">
								<p>
									<label for="db_link_label">RSS link label:</label><br />
									<input type="text" name="link_label" id="link_label" value="<?php echo esc_attr($data['settings'][0]->link_label) ?>" />
								</p>
								<p>
									<label for="db_show_rss_posts">Show RSS Selector in Post:</label><br />
									<select id="db_show_rss_posts" name="show_rss_posts" style="width: 100%">
										<option<?php echo ($data['settings'][0]->show_rss_posts == 'no') ? ' selected="selected"' : '' ?> value="no">No</option>
										<option<?php echo ($data['settings'][0]->show_rss_posts == 'yes') ? ' selected="selected"' : '' ?> value="yes">Yes</option>
									</select>
								</p>
								<p>
									<table border="0">
										<tr>
											<td>
												<label for="db_path">Select pages:</label><br />
												<select style="width: 300px; height: 100px;" id="db_pages" name="pages" multiple="multiple">
													<?php foreach($pages as $page) { ?>
														<?php if((is_array($path) && !in_array((int)$page->ID, $path)) || !is_array($path)) { ?>
															<option value="<?php echo (int)$page->ID ?>"><?php echo htmlspecialchars($page->post_title) ?></option>
														<?php } ?>
													<?php } ?>
												</select>
												<p class="description">Disabled</p>
											</td>
											<td align="center" valign="middle">
												<input type="button" value="&gt;" onclick="moveOptions('db_pages', 'db_path');" /><br />
												<input type="button" value="&lt;" onclick="moveOptions('db_path', 'db_pages');" />
											</td>
											<td>
												<label for="db_path">&nbsp;</label><br />
												<select style="width: 300px; height: 100px;" id="db_path" name="page_paths[]" multiple="multiple">
													<?php foreach($pages as $page) { ?>
														<?php if(is_array($path) && in_array((int)$page->ID, $path)) { ?>
															<option value="<?php echo (int)$page->ID ?>"><?php echo htmlspecialchars($page->post_title) ?></option>
														<?php } ?>
													<?php } ?>
												</select>
												<p class="description">Enabled</p>
											</td>
										</tr>
									</table>
								</p>
							</div>
							<div class="clear"></div>
						</div>
					</div>
					<input type="hidden" value="settings_update" name="action">
					<input type="submit" value="Update" class="button-primary" name="submit">
				</div>
		</form>

	</div>
	<div class="clear"></div>
</div>
<script type="text/javascript">
	function moveOption(fromID, toID, idx) {    
		if (isNaN(parseInt(idx))) {
			var i = document.getElementById( fromID ).selectedIndex;
		} else {
			var i = idx;
		}
		var o = document.getElementById( fromID ).options[ i ];
		var theOpt = new Option( o.text, o.value, false, false );
		document.getElementById( toID ).options[document.getElementById( toID ).options.length] = theOpt;
		document.getElementById( fromID ).options[ i ] = null;
	}
	function moveOptions(fromID, toID) {
		for (var x = document.getElementById( fromID ).options.length - 1; x >= 0 ; x--) {
			if (document.getElementById( fromID ).options[x].selected == true) {
				moveOption( fromID, toID, x );
			}
		}
	}
	function selectAllOptions(selStr) {
		var selObj = document.getElementById(selStr);
		for (var i=0; i<selObj.options.length; i++) {
			selObj.options[i].selected = true;
		}
	}
</script>
<?php echo MTR_SELECTED_TAGS_RSS_FOOTER ?>
<?php } ?>