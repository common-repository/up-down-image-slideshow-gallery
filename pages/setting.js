function udisg_submit()
{
	if(document.udisg_form.udisg_path.value=="")
	{
		alert(udisg_adminscripts.udisg_path);
		document.udisg_form.udisg_path.focus();
		return false;
	}
	else if(document.udisg_form.udisg_link.value=="")
	{
		alert(udisg_adminscripts.udisg_link);
		document.udisg_form.udisg_link.focus();
		return false;
	}
	else if(document.udisg_form.udisg_target.value=="")
	{
		alert(udisg_adminscripts.udisg_target);
		document.udisg_form.udisg_target.focus();
		return false;
	}
	//	else if(document.udisg_form.udisg_title.value=="")
	//	{
	//		alert("Please enter the image title.");
	//		document.udisg_form.udisg_title.focus();
	//		return false;
	//	}
	else if(document.udisg_form.udisg_order.value=="")
	{
		alert(udisg_adminscripts.udisg_order);
		document.udisg_form.udisg_order.focus();
		return false;
	}
	else if(isNaN(document.udisg_form.udisg_order.value))
	{
		alert(udisg_adminscripts.udisg_order);
		document.udisg_form.udisg_order.focus();
		return false;
	}
	else if(document.udisg_form.udisg_status.value=="")
	{
		alert(udisg_adminscripts.udisg_status);
		document.udisg_form.udisg_status.focus();
		return false;
	}
	else if(document.udisg_form.udisg_type.value=="")
	{
		alert(udisg_adminscripts.udisg_type);
		document.udisg_form.udisg_type.focus();
		return false;
	}
}

function udisg_delete(id)
{
	if(confirm(udisg_adminscripts.udisg_delete))
	{
		document.frm_udisg_display.action="options-general.php?page=up-down-image-slideshow-gallery&ac=del&did="+id;
		document.frm_udisg_display.submit();
	}
}	

function udisg_redirect()
{
	window.location = "options-general.php?page=up-down-image-slideshow-gallery";
}

function udisg_help()
{
	window.open("http://www.gopiplus.com/work/2011/04/25/wordpress-plugin-up-down-image-slideshow-script/");
}