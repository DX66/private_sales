{* $Id: register_newslists.tpl,v 1.1 2010/05/21 08:32:45 joy Exp $ *}
{if $active_modules.News_Management and $newslists}

{if $hide_header eq ""}
<tr>
<td height="20" colspan="3"><b>{$lng.lbl_newsletter}</b><hr size="1" noshade="noshade" /></td>
</tr>
{/if}

<tr>
<td colspan="3">{$lng.lbl_newsletter_signup_text}</td>
</tr>

<tr>
<td colspan="2">&nbsp;</td>
<td>
<table border="0">

{section name=idx loop=$newslists}
{assign var="listid" value=$newslists[idx].listid}
<tr>
<td><input type="checkbox" name="subscription[{$listid}]" {if $subscription[$listid] ne ""}checked="checked"{/if} /></td>
<td>{$newslists[idx].name}</td>
</tr>
<tr>
<td>&nbsp;</td>
<td><i>{$newslists[idx].descr}</i></td>
</tr>
{/section}

</table>
</td>
</tr>

{/if}
