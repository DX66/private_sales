{*
$Id: news_lists_select.tpl,v 1.4 2010/07/21 11:58:50 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name="dialog"}

<script type="text/javascript">
//<![CDATA[
var txt_delete_new_list_text = "{$lng.txt_delete_new_list_text|wm_remove|escape:javascript}";
//]]>
</script>

{if $lists ne ""}

<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
checkboxes_form = 'selectlistsform';
checkboxes = new Array({foreach from=$lists item=v key=k}{if $k gt 0},{/if}'to_delete[{$v.listid}]'{/foreach});
 
//]]> 
</script>
<script type="text/javascript" src="{$SkinDir}/js/change_all_checkboxes.js"></script>

<table width="100%">
<tr>
<td>
<div style="line-height:170%"><a href="javascript:change_all(true);">{$lng.lbl_check_all}</a> / <a href="javascript:change_all(false);">{$lng.lbl_uncheck_all}</a></div>
</td>
<td>{include file="main/language_selector.tpl" script="news.php?"}</td>
</tr>
</table>

{/if}

<form action="news.php" method="post" name="selectlistsform">
<input type="hidden" name="mode" value="update" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
  <td width="15">&nbsp;</td>
  <td width="60%">{$lng.lbl_list_name}</td>
  <td width="20%" align="center">{$lng.lbl_show_as_news}</td>
  <td width="20%" align="center">{$lng.lbl_active}</td>
</tr>

{if $lists ne ""}

{section name=idx loop=$lists}

<tr{cycle values=", class='TableSubHead'"}>
  <td>
  <input type="hidden" name="posted_data[{$lists[idx].listid}][listid]" value="{$lists[idx].listid}" />
  <input type="checkbox" name="to_delete[{$lists[idx].listid}]" />
  </td>
  <td><b><a href="news.php?mode=modify&amp;targetlist={$lists[idx].listid}" title="Click for details">{$lists[idx].name}</a></b></td>
  <td align="center"><input type="checkbox" name="posted_data[{$lists[idx].listid}][show_as_news]"{if $lists[idx].show_as_news eq "Y"} checked="checked"{/if} /></td>
  <td align="center"><input type="checkbox" name="posted_data[{$lists[idx].listid}][avail]"{if $lists[idx].avail eq "Y"} checked="checked"{/if} /></td>
</tr>

{/section}

{else}

<tr>
  <td colspan="4" align="center">{$lng.txt_no_newslists}</td>
</tr>

{/if}

<tr>
  <td colspan="4"><br />
{if $lists ne ""}
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick='javascript: if (checkMarks(this.form, new RegExp("^to_delete\\[.+\\]", "gi")) &amp;&amp;confirm(txt_delete_new_list_text)) submitForm(this.form, "delete");' />
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_update|strip_tags:false|escape}" onclick='javascript: this.form.submit();' />
<br /><br />
{/if}
<br />
<input type="button" value="{$lng.lbl_add_new|strip_tags:false|escape}" onclick="javascript: self.location='news.php?mode=create';" />
  </td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_news_lists content=$smarty.capture.dialog extra='width="100%"'}
