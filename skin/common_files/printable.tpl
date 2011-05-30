{*
$Id: printable.tpl,v 1.1 2010/05/21 08:31:58 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table cellspacing="0" cellpadding="0">
<tr>
  <td align="right" valign="middle"><a href="{$php_url.url}?printable=Y{if $php_url.query_string ne ''}&amp;{$php_url.query_string|amp}{/if}" style="TEXT-DECORATION: underline;" rel="nofollow">{$lng.lbl_printable_version}&nbsp;</a></td>
  <td width="16" valign="middle"><a href="{$php_url.url}?printable=Y{if $php_url.query_string ne ''}&amp;{$php_url.query_string|amp}{/if}" rel="nofollow"><img src="{$ImagesDir}/printer.gif" alt="" /></a></td>
</tr>
</table>
