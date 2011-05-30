/*
$Id: start_textarea.js,v 1.1 2010/05/21 08:32:31 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*/

/*
Common wrappers for TinyMCE HTML editor
*/

function enableEditor(id, name) {
  if (!$("#"+id))
    return false;

	if (localBrowser == 'Opera' && localVersion == '9.00') {
		var sx = document.body.scrollLeft;
		var sy = document.body.scrollTop;
	}

	tinyMCE.execCommand("mceAddControl", false, id);

	$("#"+id+"Enb, #"+id+"DisB").hide();
	$("#"+id+"EnbB, #"+id+"Dis").show();

	setCookie(id+'EditorEnabled', 'Y');

	if (localBrowser == 'Opera' && localVersion == '9.00') {
		window.scrollTo(sx, sy);
	}
}

function disableEditor(id, name) {
	if (!$("#"+id))
		return false;

	tinyMCE.getInstanceById(id);
	tinyMCE.execCommand("mceRemoveControl", false, id);

	$("#"+id+"EnbB, #"+id+"Dis").hide();
	$("#"+id+"Enb, #"+id+"DisB").show();

	if (localBFamily == 'MSIE')
		setTimeout("document.getElementById('"+id+"').value = document.getElementById('"+id+"').value;", 100);

	deleteCookie(id+'EditorEnabled');
}

function editor_get_xhtml_body(name) {
  tinyMCE.execCommand("mceRemoveControl", false, name);
  result = $("#"+name).val();
  tinyMCE.execCommand("mceAddControl", false, name);
  return result;
}

function editor_puthtml(name, value) {
  $("#"+name).val(value);
}

function get_html_editor(name) {
  return $("#"+name);
}

