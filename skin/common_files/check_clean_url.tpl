{*
$Id: check_clean_url.tpl,v 1.3 2010/06/08 06:17:38 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
//<![CDATA[
var err_clean_url_wrong_format = "{$lng.err_clean_url_wrong_format|wm_remove|escape:javascript|replace:"\n":" "|replace:"\r":" "}";
var clean_url_validation_regexp = new RegExp("{$clean_url_validation_regexp|wm_remove|escape:javascript}", "g");
//]]>
</script>
<script type="text/javascript" src="{$SkinDir}/js/check_clean_url.js"></script>
