{*
$Id: head_printable.tpl,v 1.1 2010/05/21 08:31:58 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table cellpadding="0" cellspacing="0" width="100%">
<tr> 
  <td class="HeadLogo"><a href="{$http_location}/"><img src="{$ImagesDir}/xlogo.gif" alt="" /></a></td>
  <td valign="top" align="right">
  {if $usertype eq "C"}
  {include file="customer/top_menu_printable.tpl"}
  {/if}
  </td>
</tr>
</table>
  <table cellpadding="0" cellspacing="0" width="100%">
<tr> 
  <td  colspan="2"><hr size="1" noshade="noshade" /></td>
</tr>
</table>
<br /><br />
