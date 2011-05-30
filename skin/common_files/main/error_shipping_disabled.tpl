{*
$Id: error_shipping_disabled.tpl,v 1.1 2010/05/21 08:32:17 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}

{if $active_modules.Simple_Mode}
{$lng.txt_shipping_disabled_admin|substitute:"path":$catalogs.admin}
{else}
{$lng.txt_shipping_disabled_provider}
{/if}

<br /><br />

{include file="buttons/button.tpl" button_title=$lng.lbl_continue href="home.php"}

<br />

{/capture}
{include file="dialog.tpl" title=$lng.lbl_warning content=$smarty.capture.dialog extra='width="100%"'}
