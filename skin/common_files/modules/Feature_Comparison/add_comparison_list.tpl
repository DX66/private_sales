{*
$Id: add_comparison_list.tpl,v 1.1 2010/05/21 08:32:21 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $href eq ''}
{assign var="href" value="comparison_list.php?mode=add&productid=`$productid`"}
{/if}
{include file="customer/buttons/button.tpl" button_title=$lng.lbl_add_comparison_list title=$lng.lbl_add_comparison_list}
