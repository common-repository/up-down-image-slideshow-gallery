<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
  <div class="form-wrap">
    <div id="icon-edit" class="icon32 icon32-posts-post"><br>
    </div>
    <h2><?php _e('Up down image slideshow gallery', 'up-down-image-slideshow-gallery'); ?></h2>
	<h3><?php _e('Widget setting', 'up-down-image-slideshow-gallery'); ?></h3>
    <?php
	$udisg_title = get_option('udisg_title');
	$udisg_width = get_option('udisg_width');
	$udisg_height = get_option('udisg_height');
	$udisg_pause = get_option('udisg_pause');
	$udisg_cycles = get_option('udisg_cycles');
	$udisg_persist = get_option('udisg_persist');
	$udisg_slideduration = get_option('udisg_slideduration');
	$udisg_random = get_option('udisg_random');
	$udisg_type = get_option('udisg_type');
	
	if (isset($_POST['udisg_submit'])) 
	{
		//	Just security thingy that wordpress offers us
		check_admin_referer('udisg_form_setting');
			
		$udisg_title = stripslashes(sanitize_text_field($_POST['udisg_title']));
		$udisg_width = intval($_POST['udisg_width']);
		$udisg_height = intval($_POST['udisg_height']);
		$udisg_pause = intval($_POST['udisg_pause']);
		$udisg_cycles = intval($_POST['udisg_cycles']);
		$udisg_persist = stripslashes(sanitize_text_field($_POST['udisg_persist']));
		$udisg_slideduration = intval($_POST['udisg_slideduration']);
		$udisg_random = stripslashes(sanitize_text_field($_POST['udisg_random']));
		$udisg_type = stripslashes(sanitize_text_field($_POST['udisg_type']));
		
		if(!is_numeric($udisg_width) || $udisg_width == 0) { $udisg_width = 250; }
		if(!is_numeric($udisg_height) || $udisg_height == 0) { $udisg_height = 200; }
		if(!is_numeric($udisg_pause) || $udisg_pause == 0) { $udisg_pause = 2000; }
		if(!is_numeric($udisg_cycles) || $udisg_cycles == 0) { $udisg_cycles = 15; }
		if(!is_numeric($udisg_slideduration) || $udisg_slideduration == 0) { $udisg_slideduration = 300; }
		
		if($udisg_persist != "true" && $udisg_persist != "false")
		{
			$udisg_persist = "true";
		} 
		
		if($udisg_random != "YES" && $udisg_random != "NO")
		{
			$udisg_random = "YES";
		}
		
		update_option('udisg_title', $udisg_title );
		update_option('udisg_width', $udisg_width );
		update_option('udisg_height', $udisg_height );
		update_option('udisg_pause', $udisg_pause );
		update_option('udisg_cycles', $udisg_cycles );
		update_option('udisg_persist', $udisg_persist );
		update_option('udisg_slideduration', $udisg_slideduration );
		update_option('udisg_random', $udisg_random );
		update_option('udisg_type', $udisg_type );
		
		?>
		<div class="updated fade">
			<p><strong><?php _e('Details successfully updated.', 'up-down-image-slideshow-gallery'); ?></strong></p>
		</div>
		<?php
	}
	?>
    <form name="udisg_form" method="post" action="">
      
	  <label for="tag-title"><?php _e('Enter widget title', 'up-down-image-slideshow-gallery'); ?></label>
      <input name="udisg_title" id="udisg_title" type="text" value="<?php echo $udisg_title; ?>" size="80" />
      <p><?php _e('Enter widget title, Only for widget.', 'up-down-image-slideshow-gallery'); ?></p>
      
	  <label for="tag-width"><?php _e('Width (Only number)', 'up-down-image-slideshow-gallery'); ?></label>
      <input name="udisg_width" id="udisg_width" type="text" value="<?php echo $udisg_width; ?>" />
      <p><?php _e('Widget Width (only number). (Example: 250)', 'up-down-image-slideshow-gallery'); ?></p>
      
	  <label for="tag-height"><?php _e('Height of each image', 'up-down-image-slideshow-gallery'); ?></label>
      <input name="udisg_height" id="udisg_height" type="text" value="<?php echo $udisg_height; ?>" />
      <p><?php _e('Widget Height (only number). (Example: 200)', 'up-down-image-slideshow-gallery'); ?></p>
	  
	  <label for="tag-height"><?php _e('Pause', 'up-down-image-slideshow-gallery'); ?></label>
      <input name="udisg_pause" id="udisg_pause" type="text" value="<?php echo $udisg_pause; ?>" />
      <p><?php _e('Only Number / Pause time of the slideshow in milliseconds.', 'up-down-image-slideshow-gallery'); ?></p>
	  
	  <label for="tag-height"><?php _e('Cycles', 'up-down-image-slideshow-gallery'); ?></label>
      <input name="udisg_cycles" id="udisg_cycles" type="text" value="<?php echo $udisg_cycles; ?>" />
      <p><?php _e('Gallery will automatically start the slideshow and it will stop number of cycle mentioned in this property. (only number)', 'up-down-image-slideshow-gallery'); ?></p>
	  
	  <label for="tag-height"><?php _e('Persist', 'up-down-image-slideshow-gallery'); ?></label>
      <input name="udisg_persist" id="udisg_persist" type="text" value="<?php echo $udisg_persist; ?>" />
      <p></p>
	  
	  <label for="tag-height"><?php _e('Slide duration', 'up-down-image-slideshow-gallery'); ?></label>
      <input name="udisg_slideduration" id="udisg_slideduration" type="text" value="<?php echo $udisg_slideduration; ?>" />
      <p><?php _e('Slideshow transition duration in milliseconds.', 'up-down-image-slideshow-gallery'); ?></p>
	  
	  <label for="tag-height"><?php _e('Random', 'up-down-image-slideshow-gallery'); ?></label>
      <input name="udisg_random" id="udisg_random" type="text" value="<?php echo $udisg_random; ?>" />
      <p><?php _e('(YES/NO)', 'up-down-image-slideshow-gallery'); ?></p>
      
	  <label for="tag-height"><?php _e('Select your gallery group (Gallery  Type)', 'up-down-image-slideshow-gallery'); ?></label>
	  <select name="udisg_type" id="udisg_type">
        <option value='GROUP1' <?php if($udisg_type=='GROUP1') { echo 'selected' ; } ?>>Group1</option>
        <option value='GROUP2' <?php if($udisg_type=='GROUP2') { echo 'selected' ; } ?>>Group2</option>
        <option value='GROUP3' <?php if($udisg_type=='GROUP3') { echo 'selected' ; } ?>>Group3</option>
        <option value='GROUP4' <?php if($udisg_type=='GROUP4') { echo 'selected' ; } ?>>Group4</option>
        <option value='GROUP5' <?php if($udisg_type=='GROUP5') { echo 'selected' ; } ?>>Group5</option>
        <option value='GROUP6' <?php if($udisg_type=='GROUP6') { echo 'selected' ; } ?>>Group6</option>
        <option value='GROUP7' <?php if($udisg_type=='GROUP7') { echo 'selected' ; } ?>>Group7</option>
        <option value='GROUP8' <?php if($udisg_type=='GROUP8') { echo 'selected' ; } ?>>Group8</option>
        <option value='GROUP9' <?php if($udisg_type=='GROUP9') { echo 'selected' ; } ?>>Group9</option>
        <option value='GROUP0' <?php if($udisg_type=='GROUP0') { echo 'selected' ; } ?>>Group0</option>
		<option value='Widget' <?php if($udisg_type=='Widget') { echo 'selected' ; } ?>>Widget</option>
		<option value='Sample' <?php if($udisg_type=='Sample') { echo 'selected' ; } ?>>Sample</option>
      </select>
      <p><?php _e('This field is to group the images. Select your group name to fetch the images for widget.', 'up-down-image-slideshow-gallery'); ?></p>
      
	  <input name="udisg_submit" id="udisg_submit" class="button-primary" value="<?php _e('Submit', 'up-down-image-slideshow-gallery'); ?>" type="submit" />
	  <input name="publish" lang="publish" class="button-primary" onclick="udisg_redirect()" value="<?php _e('Cancel', 'up-down-image-slideshow-gallery'); ?>" type="button" />
        <input name="Help" lang="publish" class="button-primary" onclick="udisg_help()" value="<?php _e('Help', 'up-down-image-slideshow-gallery'); ?>" type="button" />
	  <?php wp_nonce_field('udisg_form_setting'); ?>
    </form>
  </div>
</div>
