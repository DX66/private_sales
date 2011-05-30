{*
$Id: authbox.tpl,v 1.1 2010/05/21 08:31:57 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=menu}
<form action="{$authform_url}" method="post" name="loginform">
<table cellpadding="0" cellspacing="0" width="100%">
<tr> 
  <td>&nbsp;&nbsp;&nbsp;</td>
  <td class="VertMenuItems" valign="top">
{$login}
<br />
{include file="buttons/logout_menu.tpl"}
<br />
  </td>
</tr>
</table>
<input type="hidden" name="mode" value="logout" />
<input type="hidden" name="usertype" value="{$auth_usertype|escape}" />
<input type="hidden" name="redirect" value="{$redirect|amp}" />
</form>
{/capture}
{include file="menu.tpl" dingbats="dingbats_authentification.gif" menu_title=$lng.lbl_authentication menu_content=$smarty.capture.menu}
