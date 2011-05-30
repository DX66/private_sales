{*
$Id: profile_notdelete_message.tpl,v 1.1 2010/05/21 08:32:18 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}
{$lng.txt_profile_not_deleted}
{/capture}
{include file="dialog.tpl" title=$lng.lbl_confirmation content=$smarty.capture.dialog extra='width="100%"'}
