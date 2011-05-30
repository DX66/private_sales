{*
$Id: stop_list.tpl,v 1.3 2010/06/08 06:17:41 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_stop_list}
{$lng.txt_stop_list_note}<br /><br />

<br />

{if $mode ne 'add'} 
{capture name=dialog}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
checkboxes_form = 'stoplistform';
checkboxes = new Array({foreach from=$stop_list item=v key=k}{if $k gt 0},{/if}'to_delete[{$v.ip}]'{/foreach});

//]]>
</script>
{include file="main/navigation.tpl"}

<script type="text/javascript" src="{$SkinDir}/js/change_all_checkboxes.js"></script>

<div style="line-height: 170%"><a href="javascript:change_all(true);">{$lng.lbl_check_all}</a> / <a href="javascript:change_all(false);">{$lng.lbl_uncheck_all}</a></div>

<form action="stop_list.php" method="post" name="stoplistform">
<input type="hidden" name="mode" value="" />
<table width="100%" cellpadding="2" cellspacing="2">
<tr class="TableHead">
  <td width="10">&nbsp;</td>
  <td>
    {if $sort_info.field eq 'ip'}
      {include file="buttons/sort_pointer.tpl" dir=$sort_info.type}&nbsp;
    {/if}  
    <a href="stop_list.php?sort=ip">{$lng.lbl_ip_address}</a>
  </td>
  <td>
    {if $sort_info.field eq 'reason'}
      {include file="buttons/sort_pointer.tpl" dir=$sort_info.type}&nbsp;
    {/if}
    <a href="stop_list.php?sort=reason">{$lng.lbl_reason}</a>
  </td>
  <td>{$lng.lbl_status}</td>
  <td>
    {if $sort_info.field eq 'date'}
      {include file="buttons/sort_pointer.tpl" dir=$sort_info.type}&nbsp;
    {/if}
    <a href="stop_list.php?sort=date">{$lng.lbl_date}</a>
  </td>
{if $active_modules.Anti_Fraud}
  <td>{$lng.lbl_details}</td>
{/if}
</tr>
{foreach from=$stop_list item=v}
<tr{cycle name="classes" values=', class="TableSubHead"'}>
  <td align="center"><input type="checkbox" name="to_delete[{$v.ip}]" value="Y" /></td>
  <td><a href="stop_list.php?mode=add&amp;ipid={$v.ipid}">{$v.ip}</a></td>
  <td>{$v.reason_text}</td>
  <td align="center">{if $v.ip_type eq 'T'}{$lng.lbl_trusted}{else}{$lng.lbl_blocked}{/if}</td>
  <td>{$v.date|date_format:$config.Appearance.datetime_format}</td>
{if $active_modules.Anti_Fraud}
  <td><a href="javascript:void(0);" onclick="javascript:window.open('{$catalogs.admin}/anti_fraud.php?mode=popup&ip={$v.ip}','AFLOOKUP','width=600,height=460,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');"><b>&gt;&gt;</b></a></td>
{/if}
</tr>
{foreachelse}
<tr>
  <td colspan="4" align="center">{$lng.lbl_stop_list_empty}</td>
</tr>
{/foreach}
{if $stop_list ne ''}
<tr>
  <td>&nbsp;</td>
</tr>
<tr>
  <td colspan="4"><input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('to_delete', 'gi'))) {ldelim}document.stoplistform.mode.value='delete'; document.stoplistform.submit();{rdelim}" /></td>
</tr>
{/if}
</table>
</form>
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_stop_list extra='width="100%"'}

{else}

<script type="text/javascript">
//<![CDATA[
var lbl_octet_has_wrong_format = "{$lng.lbl_octet_X_has_wrong_format|strip_tags|wm_remove|escape:javascript}";
{literal}
function checkIP(obj, idx) {
  if(obj.value != '*') {
    var i = parseInt(obj.value);
    if(i > 255 || i < 0 || (idx == 1 && i == 0)) {
      alert(substitute(lbl_octet_has_wrong_format, "X", idx));
      return false;
    }
  }
  return true;
}

function checkAllIP() {
  return (checkIP(document.getElementById("o0"), 1) &&
    checkIP(document.getElementById("o1"), 2) &&
    checkIP(document.getElementById("o2"), 3) &&
    checkIP(document.getElementById("o3"), 4)
  );
}
{/literal}
//]]>
</script>
{capture name=dialog}
<b>{$lng.lbl_note}:</b> {$lng.txt_stop_list_add_ip_address_note}<br /><br />
<form action="stop_list.php" method="post" name="stoplistform" onsubmit="javascript: return checkAllIP();">
<input type="hidden" name="mode" value="add" />
<input type="hidden" name="ipid" value="{$ip.ipid}" />
<table border="0">
<tr>
  <td>{$lng.lbl_ip_address}:</td>
  <td>
  <input id="o0"type="text" maxlength="3" size="3" name="octet[0]" value="{if $ip.octet1 eq -1}*{else}{$ip.octet1}{/if}" onchange="javascript: checkIP(this, 1);" />.
  <input id="o1" type="text" maxlength="3" size="3" name="octet[1]" value="{if $ip.octet2 eq -1}*{else}{$ip.octet2}{/if}" onchange="javascript: checkIP(this, 2);" />.
  <input id="o2" type="text" maxlength="3" size="3" name="octet[2]" value="{if $ip.octet3 eq -1}*{else}{$ip.octet3}{/if}" onchange="javascript: checkIP(this, 3);" />.
  <input id="o3" type="text" maxlength="3" size="3" name="octet[3]" value="{if $ip.octet4 eq -1}*{else}{$ip.octet4}{/if}" onchange="javascript: checkIP(this, 4);" />
  </td>
</tr>
<tr>
  <td>{$lng.lbl_status}:</td>
  <td><select name="ip_type">
  <option value="B"{if $ip.ip_type ne 'T'} selected="selected"{/if}>{$lng.lbl_blocked}</option>
  <option value="T"{if $ip.ip_type eq 'T'} selected="selected"{/if}>{$lng.lbl_trusted}</option>
  </select></td>
</tr>
<tr>
    <td>&nbsp;</td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td><input type="submit" value="{if $ip.ipid  gt 0}{$lng.lbl_update|strip_tags:false|escape}{else}{$lng.lbl_add|strip_tags:false|escape}{/if}" /></td>
</tr>
</table>
</form>
{if $ip.ipid gt 0}
{assign var="dialog_title" value=$lng.lbl_update_ip_address}
{else}
{assign var="dialog_title" value=$lng.lbl_add_ip_address}
{/if}
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$dialog_title extra='width="100%"'}

{/if}
