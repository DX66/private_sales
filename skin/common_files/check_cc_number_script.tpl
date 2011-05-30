{*
$Id: check_cc_number_script.tpl,v 1.3 2010/06/08 06:17:38 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
var card_types = new Array();
var card_cvv2 = new Array();
{foreach from=$card_types item=c}
card_types["{$c.code}"] = "{$c.type}";
card_cvv2["{$c.code}"] = "{$c.cvv2}";
{/foreach}
var force_cvv2 = {if $payment_cc_data.disable_ccinfo eq "N" and ($config.General.check_cc_number eq "Y" or $smarty.get.mode eq 'checkout')}true{else}false{/if};
var txt_cc_number_invalid = "{$lng.txt_cc_number_invalid|strip_tags|wm_remove|escape:javascript}";
var current_year = parseInt(('{$smarty.now|date_format:"%Y"}').replace(/^0/gi, ""));
var current_month = parseInt(('{$smarty.now|date_format:"%m"}').replace(/^0/gi, ""));
var lbl_is_this_card_expired = "{$lng.lbl_is_this_card_expired|strip_tags|wm_remove|escape:javascript}";
var lbl_cvv2_is_empty = "{$lng.lbl_cvv2_is_empty|wm_remove|escape:javascript}";
var lbl_cvv2_isnt_correct = "{$lng.lbl_cvv2_isnt_correct|wm_remove|escape:javascript}";
var lbl_cvv2_must_be_number = "{$lng.lbl_cvv2_must_be_number|wm_remove|escape:javascript}";
//]]>
</script>
<script type="text/javascript" src="{$SkinDir}/js/check_cc_number_script.js"></script>

