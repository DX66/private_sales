{*
$Id: general.tpl,v 1.1 2010/05/21 08:32:05 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{$lng.txt_help_zone_title}
<br /><br />
{capture name=dialog}
<table cellspacing="0" cellpadding="0" width="100%">

{if $usertype eq "P"}
<tr> 
<td height="15" class="Text">{include file="buttons/button.tpl" button_title=$lng.lbl_providers_zone href="`$catalogs.provider`/home.php"}</td>
</tr>
<tr><td colspan="4" valign="top" height="10" class="Text">&nbsp;</td></tr>
{/if}

<tr>
<td height="15" class="Text">{include file="buttons/button.tpl" button_title=$lng.lbl_recover_password href="help.php?section=Password_Recovery"}</td>
</tr>
<tr><td colspan="4" valign="top" height="10" class="Text">&nbsp;</td></tr>

{if $usertype eq "C" or $usertype eq "B"}
<tr> 
<td height="15" class="Text">{include file="buttons/button.tpl" button_title=$lng.lbl_contact_us href="help.php?section=contactus&mode=update"}</td>
</tr>
<tr><td colspan="4" valign="top" height="10" class="Text">&nbsp;</td></tr>
{/if}

<tr> 
<td height="15" class="Text">{include file="buttons/button.tpl" button_title=$lng.lbl_faq_long href="help.php?section=FAQ"}</td>
</tr>
<tr><td colspan="4" valign="top" height="10" class="Text">&nbsp;</td></tr>

<tr> 
<td valign="top" height="15" class="Text">{include file="buttons/button.tpl" button_title=$lng.lbl_privacy_statement href="help.php?section=business"}</td>
</tr>
<tr><td height="10" width="1" class="Text" colspan="4">&nbsp;</td></tr>

<tr> 
<td valign="top" height="15" class="Text">{include file="buttons/button.tpl" button_title=$lng.lbl_terms_n_conditions href="help.php?section=conditions"}</td>
</tr>
<tr><td height="10" width="1" class="Text" colspan="4">&nbsp;</td></tr>

<tr> 
<td valign="top" height="15" class="Text">{include file="buttons/button.tpl" button_title=$lng.lbl_about_our_site href="help.php?section=about"}</td>
</tr>

</table>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_help_zone content=$smarty.capture.dialog extra='width="100%"'}
