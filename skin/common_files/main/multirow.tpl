{*
$Id: multirow.tpl,v 1.3 2010/06/08 06:17:39 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
var lbl_remove_row = '{$lng.lbl_remove_row|wm_remove|escape:javascript}';
var lbl_add_row = '{$lng.lbl_add_row|wm_remove|escape:javascript}';
var inputset_plus_img = "{$ImagesDir}/plus.gif";
var inputset_minus_img = "{$ImagesDir}/minus.gif";
//]]>
</script>
<img src="{$ImagesDir}/plus.gif" width="0" height="0" alt="" style="display: none" />
<img src="{$ImagesDir}/minus.gif" width="0" height="0" alt="" style="display: none" />
<script type="text/javascript" src="{$SkinDir}/js/multirow.js"></script>
