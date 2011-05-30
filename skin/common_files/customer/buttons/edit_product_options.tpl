{*
$Id: edit_product_options.tpl,v 1.1 2010/05/21 08:32:03 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if not $target}
  {assign var="target" value="cart"}
{/if}
{include file="customer/buttons/button.tpl" button_title=$lng.lbl_edit_options href="javascript: popupOpen('popup_poptions.php?target=`$target`&amp;id=`$id`');" style="link" link_href="popup_poptions.php?target=`$target`&amp;id=`$id`" target="_blank"}
