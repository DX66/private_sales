{*
$Id: products.tpl,v 1.1 2010/05/21 08:32:23 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}

{include file="modules/Gift_Registry/event_modify_menu.tpl"}

{include file="customer/subheader.tpl" title=$lng.lbl_wish_list}

{if $wl_products ne "" or $wl_giftcerts ne ""}

{include file="modules/Wishlist/wl_products.tpl" source="giftreg" script_name="giftreg"}

<div class="button-row">
  {include file="customer/buttons/button.tpl" button_title=$lng.lbl_giftreg_send_wishlist href="giftreg_manage.php?mode=send&eventid=`$eventid`"}
</div>

{else}

{$lng.txt_giftreg_wishlist_empty}<br />
{$lng.txt_giftreg_wishlist_empty_note}

{/if}

{/capture}
{include file="customer/dialog.tpl" title="`$lng.lbl_giftreg_manage_giftregistry`: `$event_data.title`" content=$smarty.capture.dialog}
