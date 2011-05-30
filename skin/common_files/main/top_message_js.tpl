{*
$Id: top_message_js.tpl,v 1.3 2010/06/08 06:17:39 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
//<![CDATA[
var top_message_icon = {ldelim}
  "E": "{$ImagesDir}/icon_error_small.gif",
  "W": "{$ImagesDir}/icon_warning_small.gif",
  "I": "{$ImagesDir}/icon_info_small.gif"
{rdelim};

var top_message_title = {ldelim}
  "E": "{$lng.lbl_error|wm_remove|escape:javascript}",
  "W": "{$lng.lbl_warning|wm_remove|escape:javascript}",
  "I": "{$lng.lbl_information|wm_remove|escape:javascript}"
{rdelim};
//]]>
</script>
<script type="text/javascript" src="{$SkinDir}/js/top_message.js"></script>
