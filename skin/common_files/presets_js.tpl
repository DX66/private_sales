{*
$Id: presets_js.tpl,v 1.6.2.1 2010/08/11 10:53:28 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
//<![CDATA[
var number_format_dec = '{$number_format_dec}';
var number_format_th = '{$number_format_th}';
var number_format_point = '{$number_format_point}';
var store_language = '{$store_language|escape:javascript}';
var xcart_web_dir = "{$xcart_web_dir|escape:javascript}";
var images_dir = "{$ImagesDir|escape:javascript}";
var lbl_no_items_have_been_selected = '{$lng.lbl_no_items_have_been_selected|wm_remove|escape:javascript}';
var current_area = '{$usertype}';
var skin_dir = '{$SkinDir|escape:javascript}';
var lbl_required_field_is_empty = "{$lng.lbl_required_field_is_empty|strip_tags|wm_remove|escape:javascript}";
var lbl_field_required = "{$lng.lbl_field_required|strip_tags|wm_remove|escape:javascript}";
var lbl_field_format_is_invalid = "{$lng.lbl_field_format_is_invalid|wm_remove|escape:javascript}";
var txt_required_fields_not_completed = "{$lng.txt_required_fields_not_completed|wm_remove|escape:javascript}";
{if $use_email_validation ne "N"}
var txt_email_invalid = "{$lng.txt_email_invalid|wm_remove|escape:javascript}";
var email_validation_regexp = new RegExp("{$email_validation_regexp|wm_remove|escape:javascript}", "gi");
{/if}
var lbl_error = '{$lng.lbl_error|wm_remove|escape:javascript}';
var lbl_warning = '{$lng.lbl_warning|wm_remove|escape:javascript}';
var lbl_ok = '{$lng.lbl_ok|wm_remove|escape:javascript}';
var lbl_yes = '{$lng.lbl_yes|wm_remove|escape:javascript}';
var lbl_no = '{$lng.lbl_no|wm_remove|escape:javascript}';
var txt_ajax_error_note = '{$lng.txt_ajax_error_note|wm_remove|escape:javascript}';
var is_admin_editor = {if $is_admin_editor}true{else}false{/if};
var lbl_blockui_default_message = "{$lng.lbl_blockui_default_message|wm_remove|escape:javascript}";
var current_location = '{$current_area|wm_remove|escape:javascript}';
//]]>
</script>
