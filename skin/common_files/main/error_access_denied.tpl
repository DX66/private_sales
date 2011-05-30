{*
$Id: error_access_denied.tpl,v 1.1 2010/05/21 08:32:17 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>{$lng.err_access_denied}</h1>
{$message}
{if $id ne ''}
<br /><br />
<b>{$lng.lbl_error_id}:</b> {$id}
{/if}
