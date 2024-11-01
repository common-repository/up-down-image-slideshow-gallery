<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
// Form submitted, check the data
if (isset($_POST['frm_udisg_display']) && $_POST['frm_udisg_display'] == 'yes')
{
	$did = isset($_GET['did']) ? intval($_GET['did']) : '0';
	if(!is_numeric($did)) { die('<p>Are you sure you want to do this?</p>'); }
	
	$udisg_success = '';
	$udisg_success_msg = FALSE;
	
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
		// Form submitted, check the action
		if (isset($_GET['ac']) && $_GET['ac'] == 'del' && isset($_GET['did']) && $_GET['did'] != '')
		{
			//	Just security thingy that wordpress offers us
			check_admin_referer('udisg_form_show');
			
			//	Delete selected record from the table
			$sSql = $wpdb->prepare("DELETE FROM `".WP_udisg_TABLE."`
					WHERE `udisg_id` = %d
					LIMIT 1", $did);
			$wpdb->query($sSql);
			
			//	Set success message
			$udisg_success_msg = TRUE;
			$udisg_success = __('Selected record was successfully deleted.', 'up-down-image-slideshow-gallery');
		}
	}
	
	if ($udisg_success_msg == TRUE)
	{
		?><div class="updated fade"><p><strong><?php echo $udisg_success; ?></strong></p></div><?php
	}
}
?>
<div class="wrap">
  <div id="icon-edit" class="icon32 icon32-posts-post"></div>
    <h2><?php _e('Up down image slideshow gallery', 'up-down-image-slideshow-gallery'); ?>
	<a class="add-new-h2" href="<?php echo WP_UDISG_ADMIN_URL; ?>&amp;ac=add"><?php _e('Add New', 'up-down-image-slideshow-gallery'); ?></a></h2>
    <div class="tool-box">
	<?php
		$sSql = "SELECT * FROM `".WP_udisg_TABLE."` order by udisg_type, udisg_order";
		$myData = array();
		$myData = $wpdb->get_results($sSql, ARRAY_A);
		?>
		<form name="frm_udisg_display" method="post">
      <table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
			<th scope="col"><?php _e('Reference/Title', 'left-right-image-slideshow-gallery'); ?></th>
			<th scope="col"><?php _e('Group', 'left-right-image-slideshow-gallery'); ?></th>
            <th scope="col"><?php _e('Image', 'left-right-image-slideshow-gallery'); ?></th>
			<th scope="col"><?php _e('URL', 'left-right-image-slideshow-gallery'); ?></th>
            <th scope="col"><?php _e('Order', 'left-right-image-slideshow-gallery'); ?></th>
            <th scope="col"><?php _e('Display', 'left-right-image-slideshow-gallery'); ?></th>
			<th scope="col"><?php _e('Target', 'left-right-image-slideshow-gallery'); ?></th>
          </tr>
        </thead>
		<tfoot>
          <tr>
			<th scope="col"><?php _e('Reference/Title', 'left-right-image-slideshow-gallery'); ?></th>
			<th scope="col"><?php _e('Group', 'left-right-image-slideshow-gallery'); ?></th>
            <th scope="col"><?php _e('Image', 'left-right-image-slideshow-gallery'); ?></th>
			<th scope="col"><?php _e('URL', 'left-right-image-slideshow-gallery'); ?></th>
            <th scope="col"><?php _e('Order', 'left-right-image-slideshow-gallery'); ?></th>
            <th scope="col"><?php _e('Display', 'left-right-image-slideshow-gallery'); ?></th>
			<th scope="col"><?php _e('Target', 'left-right-image-slideshow-gallery'); ?></th>
          </tr>
        </tfoot>
		<tbody>
			<?php 
			$i = 0;
			if(count($myData) > 0 )
			{
				foreach ($myData as $data)
				{
					?>
					<tr class="<?php if ($i&1) { echo'alternate'; } else { echo ''; }?>">
						<td>
						<strong><?php echo esc_html(stripslashes($data['udisg_title'])); ?></strong>
						<div class="row-actions">
							<span class="edit"><a title="Edit" href="<?php echo WP_UDISG_ADMIN_URL; ?>&amp;ac=edit&amp;did=<?php echo $data['udisg_id']; ?>"><?php _e('Edit', 'up-down-image-slideshow-gallery'); ?></a> | </span>
							<span class="trash"><a onClick="javascript:udisg_delete('<?php echo $data['udisg_id']; ?>')" href="javascript:void(0);"><?php _e('Delete', 'up-down-image-slideshow-gallery'); ?></a></span> 
						</div>
						</td>
						<td><?php echo esc_html(stripslashes($data['udisg_type'])); ?></td>
						<td><a href="<?php echo esc_html($data['udisg_path']); ?>" target="_blank"><img src="<?php echo WP_UDISG_PLUGIN_URL; ?>/inc/image-icon.png"  /></a></td>
						<td><a href="<?php echo esc_html($data['udisg_link']); ?>" target="_blank"><img src="<?php echo WP_UDISG_PLUGIN_URL; ?>/inc/link-icon.gif"  /></a></td>
						<td><?php echo esc_html(stripslashes($data['udisg_order'])); ?></td>
						<td><?php echo esc_html(stripslashes($data['udisg_status'])); ?></td>
						<td><?php echo esc_html(stripslashes($data['udisg_target'])); ?></td>
					</tr>
					<?php 
					$i = $i+1; 
				}
			}
			else
			{
				?><tr><td colspan="7" align="center"><?php _e('No records available', 'up-down-image-slideshow-gallery'); ?></td></tr><?php 
			}
			?>
		</tbody>
        </table>
		<?php wp_nonce_field('udisg_form_show'); ?>
		<input type="hidden" name="frm_udisg_display" value="yes"/>
      </form>	
	  <div class="tablenav bottom">
		  <a href="<?php echo WP_UDISG_ADMIN_URL; ?>&amp;ac=add"><input class="button action" type="button" value="<?php _e('Add New', 'up-down-image-slideshow-gallery'); ?>" /></a>
		  <a href="<?php echo WP_UDISG_ADMIN_URL; ?>&amp;ac=set"><input class="button action" type="button" value="<?php _e('Widget setting', 'up-down-image-slideshow-gallery'); ?>" /></a>
		  <a target="_blank" href="<?php echo WP_UDISG_FAV; ?>"><input class="button action" type="button" value="<?php _e('Help', 'up-down-image-slideshow-gallery'); ?>" /></a>
		  <a target="_blank" href="<?php echo WP_UDISG_FAV; ?>"><input class="button button-primary" type="button" value="<?php _e('Short Code', 'up-down-image-slideshow-gallery'); ?>" /></a>
	  </div>
	</div>
</div>