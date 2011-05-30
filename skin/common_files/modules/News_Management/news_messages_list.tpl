{*
$Id: news_messages_list.tpl,v 1.4 2010/07/21 11:58:50 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
//<![CDATA[
var txt_delete_messages = "{$lng.txt_delete_messages|wm_remove|escape:javascript}";
//]]>
</script>

{if $messages ne ""}

<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
checkboxes_form = 'messagesform';
checkboxes = new Array({foreach from=$messages item=v key=k}{if $k gt 0},{/if}'to_delete[{$v.newsid}]'{/foreach});
 
//]]> 
</script>
<script type="text/javascript" src="{$SkinDir}/js/change_all_checkboxes.js"></script>

{if $messages ne ""}
<br />
{include file="main/navigation.tpl"}
<br />
{/if}

<div style="line-height:170%"><a href="javascript:change_all(true);">{$lng.lbl_check_all}</a> / <a href="javascript:change_all(false);">{$lng.lbl_uncheck_all}</a></div>

{/if}

<form action="news.php" method="post" name="messagesform">
<input type="hidden" name="targetlist" value="{$targetlist|escape}" />
<input type="hidden" name="mode" value="messages" />
<input type="hidden" name="action" value="" />
<input type="hidden" name="messageid" value="" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
  <td width="10">&nbsp;</td>
  <td width="40%">{$lng.lbl_subject}</td>
  <td width="30%" align="center">{$lng.lbl_created}</td>
  <td width="20%" align="center">{$lng.lbl_status}</td>
  <td width="10%" align="center" nowrap="nowrap">{$lng.lbl_send_message}</td>
</tr>

{if $messages ne ""}
{section name=idx loop=$messages}
<tr{cycle values=", class='TableSubHead'"}>
  <td><input type="checkbox" name="to_delete[{$messages[idx].newsid}]" /></td>
  <td><b><a href="news.php?mode=messages&amp;messageid={$messages[idx].newsid}&amp;targetlist={$targetlist}&amp;action=modify">{$messages[idx].subject}</a></b></td>
  <td align="center">{$messages[idx].date|date_format:$config.Appearance.datetime_format}</td>
  <td align="center">
{if $messages[idx].status eq "N"}{$lng.lbl_queued}{else}{$lng.lbl_sent}<br /><font class="SmallText">[{$messages[idx].send_date|date_format:$config.Appearance.datetime_format}]</font>{/if}
  </td>
  <td align="center"><input type="button" value=" {if $messages[idx].status eq "N"}{$lng.lbl_send|strip_tags:false|escape}{else}{$lng.lbl_resend|strip_tags:false|escape}{/if} " onclick="javascript: document.messagesform.messageid.value='{$messages[idx].newsid}'; document.messagesform.action.value='send'; document.messagesform.submit();" /></td>
</tr>
{/section}

{else}

<tr>
  <td colspan="5" align="center">{$lng.txt_no_messages}</td>
</tr>

{/if}

<tr>
  <td colspan="5"><br />
{if $messages ne ""}
{include file="main/navigation.tpl"}
<br />
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if ( checkMarks(this.form, new RegExp('to_delete', 'ig')) &amp;&amp;confirm(txt_delete_messages)) {ldelim} this.form.action.value='delete'; this.form.submit(); {rdelim}" />
<br /><br />
{/if}
<input type="button" value="{$lng.lbl_add_new|strip_tags:false|escape}" onclick="javascript: self.location = 'news.php?mode=messages&amp;targetlist={$targetlist}&amp;action=modify'" />
  </td>
</tr>

</table>
</form>

