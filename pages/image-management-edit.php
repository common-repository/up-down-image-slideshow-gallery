<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$did = isset($_GET['did']) ? intval($_GET['did']) : '0';
if(!is_numeric($did)) { die('<p>Are you sure you want to do this?</p>'); }

// First check if ID exist with requested ID
$sSql = $wpdb->prepare(
	"SELECT COUNT(*) AS `count` FROM ".WP_udisg_TABLE."
	WHERE `udisg_id` = %d",
	array($did)
);
$result = '0';
$result = $wpdb->get_var($sSql);

if ($result != '1')
{
	?><div class="error fade"><p><strong><?php _e('Oops, selected details doesnt exist', 'up-down-image-slideshow-gallery'); ?></strong></p></div><?php
}
else
{
	$udisg_errors = array();
	$udisg_success = '';
	$udisg_error_found = FALSE;
	
	$sSql = $wpdb->prepare("
		SELECT *
		FROM `".WP_udisg_TABLE."`
		WHERE `udisg_id` = %d
		LIMIT 1
		",
		array($did)
	);
	$data = array();
	$data = $wpdb->get_row($sSql, ARRAY_A);
	
	// Preset the form fields
	$form = array(
		'udisg_path' 	=> $data['udisg_path'],
		'udisg_link' 	=> $data['udisg_link'],
		'udisg_target' 	=> $data['udisg_target'],
		'udisg_title' 	=> $data['udisg_title'],
		'udisg_order' 	=> $data['udisg_order'],
		'udisg_status' 	=> $data['udisg_status'],
		'udisg_type' 	=> $data['udisg_type']
	);
}
// Form submitted, check the data
if (isset($_POST['udisg_form_submit']) && $_POST['udisg_form_submit'] == 'yes')
{
	//	Just security thingy that wordpress offers us
	check_admin_referer('udisg_form_edit');
	
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
	if($form['udisg_target'] != "_blank" && $form['udisg_target'] != "_parent" && $form['udisg_target'] != "_self" && $form['udisg_target'] != "_new")
	{
		$form['udisg_target'] = "_blank";
	}
	
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
		$sSql = $wpdb->prepare(
				"UPDATE `".WP_udisg_TABLE."`
				SET `udisg_path` = %s,
				`udisg_link` = %s,
				`udisg_target` = %s,
				`udisg_title` = %s,
				`udisg_order` = %d,
				`udisg_status` = %s,
				`udisg_type` = %s
				WHERE udisg_id = %d
				LIMIT 1",
				array($form['udisg_path'], $form['udisg_link'], $form['udisg_target'], $form['udisg_title'], $form['udisg_order'], $form['udisg_status'], $form['udisg_type'], $did)
			);
		$wpdb->query($sSql);
		
		$udisg_success = __('Image details was successfully updated.', 'up-down-image-slideshow-gallery');
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
      <h3><?php _e('Update image details', 'up-down-image-slideshow-gallery'); ?></h3>
      <label for="tag-image"><?php _e('Enter image path', 'up-down-image-slideshow-gallery'); ?></label>
      <input name="udisg_path" type="text" id="udisg_path" value="<?php echo $form['udisg_path']; ?>" size="80" />
	  <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload Image">
      <p><?php _e('Where is the picture located on the internet', 'up-down-image-slideshow-gallery'); ?></p>
      <label for="tag-link"><?php _e('Enter target link', 'up-down-image-slideshow-gallery'); ?></label>
      <input name="udisg_link" type="text" id="udisg_link" value="<?php echo $form['udisg_link']; ?>" size="80" />
      <p><?php _e('When someone clicks on the picture, where do you want to send them', 'up-down-image-slideshow-gallery'); ?></p>
      <label for="tag-target"><?php _e('Enter target option', 'up-down-image-slideshow-gallery'); ?></label>
      <select name="udisg_target" id="udisg_target">
        <option value='_blank' <?php if($form['udisg_target']=='_blank') { echo 'selected' ; } ?>>_blank</option>
        <option value='_parent' <?php if($form['udisg_target']=='_parent') { echo 'selected' ; } ?>>_parent</option>
        <option value='_self' <?php if($form['udisg_target']=='_self') { echo 'selected' ; } ?>>_self</option>
        <option value='_new' <?php if($form['udisg_target']=='_new') { echo 'selected' ; } ?>>_new</option>
      </select>
      <p><?php _e('Do you want to open link in new window?', 'up-down-image-slideshow-gallery'); ?></p>
      <label for="tag-title"><?php _e('Enter image reference', 'up-down-image-slideshow-gallery'); ?></label>
      <input name="udisg_title" type="text" id="udisg_title" value="<?php echo $form['udisg_title']; ?>" size="80" />
      <p><?php _e('Enter image reference. This is only for reference.', 'up-down-image-slideshow-gallery'); ?></p>
      <label for="tag-select-gallery-group"><?php _e('Select gallery type', 'up-down-image-slideshow-gallery'); ?></label>
      <select name="udisg_type" id="udisg_type">
        <option value='GROUP1' <?php if($form['udisg_type']=='GROUP1') { echo 'selected' ; } ?>>Group1</option>
        <option value='GROUP2' <?php if($form['udisg_type']=='GROUP2') { echo 'selected' ; } ?>>Group2</option>
        <option value='GROUP3' <?php if($form['udisg_type']=='GROUP3') { echo 'selected' ; } ?>>Group3</option>
        <option value='GROUP4' <?php if($form['udisg_type']=='GROUP4') { echo 'selected' ; } ?>>Group4</option>
        <option value='GROUP5' <?php if($form['udisg_type']=='GROUP5') { echo 'selected' ; } ?>>Group5</option>
        <option value='GROUP6' <?php if($form['udisg_type']=='GROUP6') { echo 'selected' ; } ?>>Group6</option>
        <option value='GROUP7' <?php if($form['udisg_type']=='GROUP7') { echo 'selected' ; } ?>>Group7</option>
        <option value='GROUP8' <?php if($form['udisg_type']=='GROUP8') { echo 'selected' ; } ?>>Group8</option>
        <option value='GROUP9' <?php if($form['udisg_type']=='GROUP9') { echo 'selected' ; } ?>>Group9</option>
        <option value='GROUP0' <?php if($form['udisg_type']=='GROUP0') { echo 'selected' ; } ?>>Group0</option>
		<option value='Widget' <?php if($form['udisg_type']=='Widget') { echo 'selected' ; } ?>>Widget</option>
		<option value='Sample' <?php if($form['udisg_type']=='Sample') { echo 'selected' ; } ?>>Sample</option>
      </select>
      <p><?php _e('This is to group the images. Select your slideshow group.', 'up-down-image-slideshow-gallery'); ?></p>
      <label for="tag-display-status"><?php _e('Display status', 'up-down-image-slideshow-gallery'); ?></label>
      <select name="udisg_status" id="udisg_status">
        <option value='YES' <?php if($form['udisg_status']=='YES') { echo 'selected' ; } ?>>Yes</option>
        <option value='NO' <?php if($form['udisg_status']=='NO') { echo 'selected' ; } ?>>No</option>
      </select>
      <p><?php _e('Do you want the picture to show in your galler?', 'up-down-image-slideshow-gallery'); ?></p>
      <label for="tag-display-order"><?php _e('Display order', 'up-down-image-slideshow-gallery'); ?></label>
      <input name="udisg_order" type="text" id="udisg_order" size="10" value="<?php echo $form['udisg_order']; ?>" maxlength="3" />
      <p><?php _e('What order should the picture be played in. should it come 1st, 2nd, 3rd, etc.', 'up-down-image-slideshow-gallery'); ?></p>
      <input name="udisg_id" id="udisg_id" type="hidden" value="">
      <input type="hidden" name="udisg_form_submit" value="yes"/>
      <p class="submit">
        <input name="publish" lang="publish" class="button-primary" value="<?php _e('Submit', 'up-down-image-slideshow-gallery'); ?>" type="submit" />
        <input name="publish" lang="publish" class="button-primary" onclick="udisg_redirect()" value="<?php _e('Cancel', 'up-down-image-slideshow-gallery'); ?>" type="button" />
        <input name="Help" lang="publish" class="button-primary" onclick="udisg_help()" value="<?php _e('Help', 'up-down-image-slideshow-gallery'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('udisg_form_edit'); ?>
    </form>
</div>
</div>