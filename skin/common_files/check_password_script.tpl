{*
$Id: check_password_script.tpl,v 1.3 2010/06/08 06:17:38 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
var txt_simple_password = "{$lng.txt_simple_password|wm_remove|escape:javascript|replace:"\n":" "|replace:"\r":" "}";
var txt_password_match_error = "{$lng.txt_password_match_error|wm_remove|escape:javascript|replace:"\n":" "|replace:"\r":" "}";
//]]>
</script>
<script type="text/javascript" src="{$SkinDir}/js/check_password_script.js"></script>
