{*
$Id: partner_adv_campaigns.tpl,v 1.3 2010/06/08 06:17:38 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_adv_campaigns_management}
{$lng.txt_advertising_campaigns_note}<br /><br />

<br />
<script type="text/javascript">
//<![CDATA[
var texts = [
  ['G', "{$lng.txt_acm_get_parameter|replace:'"':'\"'}"],
  ['R', "{$lng.txt_acm_http_referer|replace:'"':'\"'}"],
  ['L', "{$lng.txt_acm_landing_page|replace:'"':'\"'}"]
];
{literal}
function change_textarea(word) {
  for (var x = 0; x < texts.length; x++) {
    if (texts[x][0] == word)
      document.getElementById('textspan').innerHTML = texts[x][1];
  }
}
{/literal}
//]]>
</script>
{if $campaigns ne ''}
{capture name=dialog}
<table cellspacing="1" cellpadding="2">
<tr class="TableHead">
  <td>{$lng.lbl_campaign}</td>
  <td>{$lng.lbl_usage_type}</td>
  <td>&nbsp;</td>
</tr>
{foreach from=$campaigns item=v}
<tr{cycle values=", class='TableSubHead'"}>
  <td><a href="partner_adv_campaigns.php?campaignid={$v.campaignid}">{$v.campaign}</a></td>
  <td>{if $v.type eq 'G'}{$lng.lbl_get_parameter}{elseif $v.type eq 'R'}{$lng.lbl_http_referer}{else}{$lng.lbl_landing_page}{/if}</td>
  <td><a href="partner_adv_campaigns.php?mode=delete&amp;campaignid={$v.campaignid}">{$lng.lbl_delete}</a></td>
</tr>
{/foreach}
</table>
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_advertising_campaigns extra='width="100%"'} 

<br />
{/if}
{capture name=dialog}
<form action="partner_adv_campaigns.php" method="post">
<input type="hidden" name="mode" value="add" />
<input type="hidden" name="campaignid" value="{$campaign.campaignid}" />

<table cellspacing="2" cellpadding="2">
<tr>
  <td nowrap="nowrap">{$lng.lbl_campaign_name}</td>
  <td><input type="text" name="add[campaign]" value="{$campaign.campaign|escape}" /></td>
</tr>
<tr>
    <td nowrap="nowrap">{$lng.lbl_pay_per_visit}</td>
    <td><input type="text" size="5" name="add[per_visit]" value="{$campaign.per_visit|formatprice|default:$zero}" /></td>
</tr>
<tr>
    <td nowrap="nowrap">{$lng.lbl_pay_per_campaign}</td>
    <td><input type="text" size="5" name="add[per_period]" value="{$campaign.per_period|formatprice|default:$zero}" /></td>
</tr>
<tr>
  <td nowrap="nowrap">{$lng.lbl_period_from}:</td>
  <td>
    {include file="main/datepicker.tpl" name="start_date" date=$campaign.start_period|default:$month_begin start_year="c-1" end_year="c+5"}
  </td>
</tr>
<tr>
  <td nowrap="nowrap">{$lng.lbl_period_to}:</td>
  <td>
    {include file="main/datepicker.tpl" name="end_date" date=$campaign.end_period|default:$month_begin start_year="c-1" end_year="c+5"}
  </td>
</tr>
<tr> 
    <td nowrap="nowrap">{$lng.lbl_usage_type}</td>
    <td><select name="add[type]" onchange="javascript: change_textarea(this.value);">
  <option value='G'{if $campaign.type eq 'G' or $campaign.type eq ''} selected="selected"{/if}>{$lng.lbl_get_parameter}</option>
    <option value='R'{if $campaign.type eq 'R'} selected="selected"{/if}>{$lng.lbl_http_referer}</option>
    <option value='L'{if $campaign.type eq 'L'} selected="selected"{/if}>{$lng.lbl_landing_page}</option>
  </select></td>
</tr>
<tr>
    <td>&nbsp;</td>
    <td>{$lng.txt_acm_general_note}<br /><br /><span id="textspan"></span><br /><br /><textarea id="textarea" name="add[data]" rows="3" cols="50">{$campaign.data|escape}</textarea><br />{if $campaign.type eq 'L'}<br /><b>{$lng.lbl_img_tag}</b><br /><input type="text" readonly="readonly" value="&lt;IMG src=&quot;{$current_location}/adv_counter.php?campaignid={$v.campaignid}&quot; border=&quot;0&quot; width=&quot;1&quot; height=&quot;1&quot;&gt;" size="50" />{/if}</td>
</tr>

<tr>
  <td>
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
change_textarea('{$campaign.type|default:"G"}');
//]]>
</script>
</td>
  <td class="SubmitBox">
  <input type="submit" value="{if $campaign.campaignid gt 0}{$lng.lbl_modify|strip_tags:false|escape}{else}{$lng.lbl_add|strip_tags:false|escape}{/if}" />
  {if $campaign.campaignid gt 0}<input type="submit" value="{$lng.lbl_close|strip_tags:false|escape}" name="close" />{/if}
  </td>
</tr>

</table>
</form>
{/capture}
{if $campaign.campaignid gt 0}{assign var="dialog_title" value=$lng.lbl_modify_advertising_campaigns}{else}{assign var="dialog_title" value=$lng.lbl_add_advertising_campaigns}{/if} 
{include file="dialog.tpl" content=$smarty.capture.dialog title=$dialog_title extra='width="100%"'} 

