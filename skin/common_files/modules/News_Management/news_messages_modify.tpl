{*
$Id: news_messages_modify.tpl,v 1.1 2010/05/21 08:32:45 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<form action="news.php" method="post">
<input type="hidden" name="mode" value="messages" />
<input type="hidden" name="targetlist" value="{$targetlist|escape}" />
<input type="hidden" name="action" value="{$action|escape}" />
<input type="hidden" name="message[newsid]" value="{$message.newsid}" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td width="25%" class="FormButton">{$lng.lbl_subject}: <font class="Star">*</font></td>
  <td width="75%"><input type="text" size="50" name="message[subject]" value="{$message.subject|escape}" style="width:90%" /></td>
  <td width="10">{if $error.subject and $message.subject eq ""}<font class="AdminTitle">&lt;&lt;</font>{else}&nbsp;{/if}</td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_news_body}: <font class="Star">*</font></td>
  <td>
{include file="main/textarea.tpl" name="message[body]" cols=50 rows=10 style="width: 90%;" data=$message.body width="90%" btn_rows=3}
  </td>
  <td>{if $error.body and $message.body eq ""}<font class="AdminTitle">&lt;&lt;</font>{else}&nbsp;{/if}</td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_html_tags_allowed}:</td>
  <td>
  <select name="message[allow_html]">
  <option value="Y"{if $message.allow_html eq "Y"} selected="selected"{/if}>{$lng.lbl_yes}</option>
  <option value="N"{if $message.allow_html eq "N"} selected="selected"{/if}>{$lng.lbl_no}</option>
  </select>
  </td>
  <td>&nbsp;</td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_show_as_news}:</td>
  <td>
  <select name="message[show_as_news]">
  <option value="Y"{if $message.show_as_news eq "Y"} selected="selected"{/if}>{$lng.lbl_yes}</option>
  <option value="N"{if $message.show_as_news eq "N"} selected="selected"{/if}>{$lng.lbl_no}</option>
  </select>
  </td>
  <td>&nbsp;</td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_send_to_tes_emails}:</td>
  <td>
  <input type="text" size="50" name="message[email1]" value="{$message.email1|escape}" /><br />
  <input type="text" size="50" name="message[email2]" value="{$message.email2|escape}" /><br />
  <input type="text" size="50" name="message[email3]" value="{$message.email3|escape}" /><br />
  </td>
  <td>&nbsp;</td>
</tr>

<tr>
  <td>&nbsp;</td>
  <td colspan="2"><br />
  <input type="submit" value=" {$lng.lbl_save} " />
  </td>
</tr>

</table>
</form>

