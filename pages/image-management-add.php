<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$udisg_errors = array();
$udisg_success = '';
$udisg_error_found = FALSE;

// Preset the form fields
$form = array(
	'udisg_path' 	=> '',
	'udisg_link' 	=> '',
	'udisg_target' 	=> '',
	'udisg_title' 	=> '',
	'udisg_order' 	=> '',
	'udisg_status' 	=> '',
	'udisg_type' 	=> ''
);

// Form submitted, check the data
if (isset($_POST['udisg_form_submit']) && $_POST['udisg_form_submit'] == 'yes')
{
	//	Just security thingy that wordpress offers us
	check_admin_referer('udisg_form_add');
	
	$form['udisg_path'] = isset($_POST['udisg_path']) ? esc_url_raw($_POST['udisg_path']) : '';
	if ($form['udisg_path'] == '')
	{
		$udisg_errors[] = __('Please enter the image path.', 'up-down-image-slideshow-gallery');
		$udisg_error_found = TRUE;
	}

	$form['udisg_link'] = isset($_POST['udisg_link']) ? esc_url_raw($_POST['udisg_link']) : '';
	if ($form['udisg_link'] == '')
	{
		$udisg_errors[] = __('Please enter the target link.', 'up-down-image-slideshow-gallery');
		$udisg_error_found = TRUE;
	}
	
	$form['udisg_target'] = isset($_POST['udisg_target']) ? sanitize_text_field($_POST['udisg_target']) : '';
	
	$form['udisg_title'] = isset($_POST['udisg_title']) ? sanitize_text_field($_POST['udisg_title']) : '';
	
	$form['udisg_order'] = isset($_POST['udisg_order']) ? intval($_POST['udisg_order']) : '';
	
	$form['udisg_status'] = isset($_POST['udisg_status']) ? sanitize_text_field($_POST['udisg_status']) : '';
	if($form['udisg_status'] != "YES" && $form['udisg_status'] != "NO")
	{
		$form['udisg_status'] = "YES";
	}
	
	$form['udisg_type'] = isset($_POST['udisg_type']) ? sanitize_text_field($_POST['udisg_type']) : '';

	//	No errors found, we can add this Group to the table
	if ($udisg_error_found == FALSE)
	{
		$sql = $wpdb->prepare(
			"INSERT INTO `".WP_udisg_TABLE."`
			(`udisg_path`, `udisg_link`, `udisg_target`, `udisg_title`, `udisg_order`, `udisg_status`, `udisg_type`)
			VALUES(%s, %s, %s, %s, %d, %s, %s)",
			array($form['udisg_path'], $form['udisg_link'], $form['udisg_target'], $form['udisg_title'], $form['udisg_order'], $form['udisg_status'], $form['udisg_type'])
		);
		$wpdb->query($sql);
		
		$udisg_success = __('New image details was successfully added.', 'up-down-image-slideshow-gallery');
		
		// Reset the form fields
		$form = array(
			'udisg_path' 	=> '',
			'udisg_link' 	=> '',
			'udisg_target' 	=> '',
			'udisg_title' 	=> '',
			'udisg_order' 	=> '',
			'udisg_status' 	=> '',
			'udisg_type' 	=> ''
		);
	}
}

if ($udisg_error_found == TRUE && isset($udisg_errors[0]) == TRUE)
{
	?>
	<div class="error fade">
		<p><strong><?php echo $udisg_errors[0]; ?></strong></p>
	</div>
	<?php
}
if ($udisg_error_found == FALSE && strlen($udisg_success) > 0)
{
	?>
	  <div class="updated fade">
		<p><strong><?php echo $udisg_success; ?> 
		<a href="<?php echo WP_UDISG_ADMIN_URL; ?>"><?php _e('Click here', 'up-down-image-slideshow-gallery'); ?></a> <?php _e('to view the details', 'up-down-image-slideshow-gallery'); ?></strong></p>
	  </div>
	  <?php
	}
?>
<script type="text/javascript">
jQuery(document).ready(function($){
    $('#upload-btn').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            console.log(uploaded_image);
            var img_imageurl = uploaded_image.toJSON().url;
            // Let's assign the url value to the input field
            $('#udisg_path').val(img_imageurl);
        });
    });
});
</script>
<?php
wp_enqueue_script('jquery'); // jQuery
wp_enqueue_media(); // This will enqueue the Media Uploader script
?>
<div class="form-wrap">
	<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
	<h2><?php _e('Up down image slideshow gallery', 'up-down-image-slideshow-gallery'); ?></h2>
	<form name="udisg_form" method="post" action="#" onsubmit="return udisg_submit()"  >
      <h3><?php _e('Add new image details', 'up-down-image-slideshow-gallery'); ?></h3>
      <label for="tag-image"><?php _e('Enter image path (URL)', 'up-down-image-slideshow-gallery'); ?></label>
      <input name="udisg_path" type="text" id="udisg_path" value="" size="80" />
	  <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload Image">
      <p><?php _e('Where is the picture located on the internet', 'up-down-image-slideshow-gallery'); ?> (ex: http://www.gopiplus.com/work/wp-content/uploads/pluginimages/250x167/250x167_2.jpg)</p>
      <label for="tag-link"><?php _e('Enter target link', 'up-down-image-slideshow-gallery'); ?></label>
      <input name="udisg_link" type="text" id="udisg_link" value="#" size="80" />
      <p><?php _e('When someone clicks on the picture, where do you want to send them', 'up-down-image-slideshow-gallery'); ?></p>
      <label for="tag-target"><?php _e('Enter target option', 'up-down-image-slideshow-gallery'); ?></label>
      <select name="udisg_target" id="udisg_target">
        <option value='_blank'>_blank</option>
        <option value='_parent'>_parent</option>
        <option value='_self'>_self</option>
        <option value='_new'>_new</option>
      </select>
      <p><?php _e('Do you want to open link in new window?', 'up-down-image-slideshow-gallery'); ?></p>
      <label for="tag-title"><?php _e('Enter image reference', 'up-down-image-slideshow-gallery'); ?></label>
      <input name="udisg_title" type="text" id="udisg_title" value="" size="80" />
      <p><?php _e('Enter image reference. This is only for reference.', 'up-down-image-slideshow-gallery'); ?></p>
      <label for="tag-select-gallery-group"><?php _e('Select gallery type', 'up-down-image-slideshow-gallery'); ?></label>
      <select name="udisg_type" id="udisg_type">
        <option value='GROUP1'>Group1</option>
        <option value='GROUP2'>Group2</option>
        <option value='GROUP3'>Group3</option>
        <option value='GROUP4'>Group4</option>
        <option value='GROUP5'>Group5</option>
        <option value='GROUP6'>Group6</option>
        <option value='GROUP7'>Group7</option>
        <option value='GROUP8'>Group8</option>
        <option value='GROUP9'>Group9</option>
        <option value='GROUP0'>Group0</option>
		<option value='Widget'>Widget</option>
		<option value='Sample'>Sample</option>
      </select>
      <p><?php _e('This is to group the images. Select your slideshow group.', 'up-down-image-slideshow-gallery'); ?></p>
      <label for="tag-display-status"><?php _e('Display status', 'up-down-image-slideshow-gallery'); ?></label>
      <select name="udisg_status" id="udisg_status">
        <option value='YES'>Yes</option>
        <option value='NO'>No</option>
      </select>
      <p><?php _e('Do you want the picture to show in your galler?', 'up-down-image-slideshow-gallery'); ?></p>
      <label for="tag-display-order"><?php _e('Display order', 'up-down-image-slideshow-gallery'); ?></label>
      <input name="udisg_order" type="text" id="udisg_order" size="10" value="1" maxlength="3" />
      <p><?php _e('What order should the picture be played in. should it come 1st, 2nd, 3rd, etc.', 'up-down-image-slideshow-gallery'); ?></p>
      <input name="udisg_id" id="udisg_id" type="hidden" value="">
      <input type="hidden" name="udisg_form_submit" value="yes"/>
      <p class="submit">
        <input name="publish" lang="publish" class="button-primary" value="<?php _e('Submit', 'up-down-image-slideshow-gallery'); ?>" type="submit" />
        <input name="publish" lang="publish" class="button-primary" onclick="udisg_redirect()" value="<?php _e('Cancel', 'up-down-image-slideshow-gallery'); ?>" type="button" />
        <input name="Help" lang="publish" class="button-primary" onclick="udisg_help()" value="<?php _e('Help', 'up-down-image-slideshow-gallery'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('udisg_form_add'); ?>
    </form>
</div>
</div>