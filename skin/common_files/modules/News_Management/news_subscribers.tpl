{*
$Id: news_subscribers.tpl,v 1.4 2010/07/21 11:58:50 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="check_email_script.tpl"}

<script type="text/javascript">
//<![CDATA[
var txt_news_list_subscribers_delete = "{$lng.txt_news_list_subscribers_delete|wm_remove|escape:javascript}";
//]]>
</script>

{if $subscribers ne ""}

{if $total_pages gt 2}
<br />
{include file="main/navigation.tpl"}
{/if}

<script type="text/javascript">
//<![CDATA[
checkboxes_form = 'subscribersform';
checkboxes = new Array({foreach from=$subscribers item=v key=k}{if $k gt 0},{/if}"to_delete[{$v.email|replace:'"':'\"'}]"{/foreach});
 
//]]> 
</script>
<script type="text/javascript" src="{$SkinDir}/js/change_all_checkboxes.js"></script>

<div style="line-height:170%"><a href="javascript:change_all(true);">{$lng.lbl_check_all}</a> / <a href="javascript:change_all(false);">{$lng.lbl_uncheck_all}</a></div>

{/if}

<form action="news.php" method="post" name="subscribersform">
<input type="hidden" name="mode" value="subscribers" />
<input type="hidden" name="action" value="" />
<input type="hidden" name="targetlist" value="{$targetlist|escape}" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
<td width="10">&nbsp;</td>
<td width="50%">{if $search_prefilled.sort_field eq "email"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="news.php?mode=subscribers&amp;targetlist={$targetlist}&amp;sort=email">{$lng.lbl_email}</a></td>
<td width="50%">{if $search_prefilled.sort_field eq "since_date"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="news.php?mode=subscribers&amp;targetlist={$targetlist}&amp;sort=since_date">{$lng.lbl_since_date}</a></td>
</tr>

{if $subscribers ne ""}

{section name=idx loop=$subscribers}
<tr{cycle values=", class='TableSubHead'"}>
<td><input type="checkbox" name="to_delete[{$subscribers[idx].email|escape}]" /></td>
<td>{$subscribers[idx].email}</td>
<td>{$subscribers[idx].since_date|date_format:$config.Appearance.datetime_format}</td>
</tr>
{/section}

{if $total_pages gt 2}
<tr>
<td colspan="3">
<br />
{include file="main/navigation.tpl"}
</td>
</tr>
{/if}

<tr>
<td colspan="3"><br />
<input type="button" value="{$lng.lbl_export_selected|strip_tags:false|escape}" onclick='javascript: if (!checkMarks(this.form, new RegExp("^to_delete\\[.+\\]", "gi"))) return; document.subscribersform.action.value="export"; document.subscribersform.submit();' />&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick='javascript: if (checkMarks(this.form, new RegExp("^to_delete\\[.+\\]", "gi")) &amp;&amp;confirm(txt_news_list_subscribers_delete)) {ldelim} this.form.action.value="delete"; this.form.submit(); {rdelim}' />&nbsp;&nbsp;
<br /><br />
<input type="button" value="{$lng.lbl_export_all|strip_tags:false|escape}" onclick='javascript: document.subscribersform.action.value="export_all"; document.subscribersform.submit();' />&nbsp;&nbsp;
</td>
</tr>

{else}

<tr>
<td colspan="3" align="center">{$lng.txt_no_subscribers}</td>
</tr>

{/if}
</table>
</form>

<form action="news.php" method="post" name="addsubscribersform">
<input type="hidden" name="mode" value="subscribers" />
<input type="hidden" name="action" value="add" />
<input type="hidden" name="targetlist" value="{$targetlist|escape}" />

<div>
<br /><br />
{include file="main/subheader.tpl" title=$lng.lbl_add_to_maillist}
</div>

{$lng.lbl_email}: <input type="text" id="new_email" name="new_email" size="40" />&nbsp;
<input type="submit" value=" {$lng.lbl_add|strip_tags:false|escape} " onclick="javascript: return checkEmailAddress(document.getElementById('new_email'), 'Y');" />

</form>

<form action="news.php" method="post" name="importsubscribersform" enctype="multipart/form-data">
<input type="hidden" name="mode" value="subscribers" />
<input type="hidden" name="action" value="import" />
<input type="hidden" name="targetlist" value="{$targetlist|escape}" />

<div>
<br /><br />
{include file="main/subheader.tpl" title=$lng.lbl_news_list_subscribers_import}
</div>

{$lng.lbl_news_list_subscribers}: <input type="file" size="32" name="userfile" />
<input type="submit" value="{$lng.lbl_import|strip_tags:false|escape}" />

</form>

<br />

{if $need_unsubscribe_section}

<form action="news.php" method="post" name="importunsubscribersform" enctype="multipart/form-data">
<input type="hidden" name="mode" value="unsubscribers" />
<input type="hidden" name="action" value="import" />
<input type="hidden" name="targetlist" value="{$targetlist|escape}" />

<div>
<br /><br />
{include file="main/subheader.tpl" title=$lng.lbl_news_list_unsubscribers_import}
</div>

<table cellpadding="4" cellspacing="4">

<tr><td align="left">{$lng.lbl_news_list_subscribers}:</td>
<td align="left"><input type="file" size="32" name="userfile" /></td></tr>

<tr><td align="left">{$lng.lbl_news_list_unsubscribe_from}:</td>
<td align="left"><select name="do_not_need_targetlist">
<option value="N"{if $not_empty_list} selected="selected"{/if}>{$lng.lbl_news_list_current_list}</option>
<option value="Y"{if not $not_empty_list} selected="selected"{/if}>{$lng.lbl_news_list_all_lists}</option>
</select></td></tr>

<tr><td>&nbsp;</td><td align="left"><input type="submit" value="{$lng.lbl_remove|strip_tags:false|escape}" /></td></tr>

</table>

</form>

{/if}
