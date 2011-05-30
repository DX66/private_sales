{*
$Id: FAQ_HTML.tpl,v 1.1 2010/05/21 08:32:05 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}
{$lng.txt_faq}
{/capture}
{include file="dialog.tpl" title=$lng.lbl_faq content=$smarty.capture.dialog extra='width="100%"'}
