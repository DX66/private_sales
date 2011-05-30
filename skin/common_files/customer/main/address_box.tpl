{*
$Id: address_box.tpl,v 1.4.2.3 2011/03/01 09:26:24 aim Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{if $mode eq "select"}
  <form action="popup_address.php" method="post" name="address_{$address.id|default:0}" id="address_{$address.id|default:0}">
      <input type="hidden" name="mode" value="select" />
      <input type="hidden" name="id" value="{$address.id|default:0}" />
      <input type="hidden" name="type" value="{$type|escape:"html"|default:"B"}" />
      <input type="hidden" name="for" value="{$for|escape:"html"|default:"cart"}" />
  </form>
{/if}

<li id="address_box_{$address.id|default:0}" class="address-box{if $address.id eq $current} address-current{/if}{if $mode eq 'select' and not $add_new and $address.id neq $current} address-select cursor-hover pointer{/if}" {if $mode eq 'select' and $type ne '' and $for ne '' and $checkout_module ne 'One_Page_Checkout' or $address.id lte 0}onclick="javascript: $('#address_{$address.id|default:0}').submit();"{/if}>
  <div class="address-bg">
    <div class="address-main">

      {if $add_new}
          <div class="new-address-label">
            <a class="new-address" href="popup_address.php" onclick="javascript: return !popupOpen('popup_address.php{if $mode eq 'select'}?return=select&for={$for}&type={$type}{/if}');">{$lng.lbl_add_new_address}</a>
          </div>

      {else}

        {if $address.default_s eq 'Y' or $address.default_b eq 'Y'}
          <div class="address-default">
            {if $address.default_s eq 'Y' and $address.default_b eq 'Y'}
              <img src="{$ImagesDir}/icon_billing.png" width="19" height="15" alt="" />
              <img src="{$ImagesDir}/icon_shipping.png" width="16" height="9" alt="" />
              {if $mode ne 'select'}{$lng.lbl_billing_and_shipping_address}{/if}
            {elseif $address.default_b eq 'Y'}
              <img src="{$ImagesDir}/icon_billing.png" width="19" height="15" alt="" />
              {if $mode ne 'select'}{$lng.lbl_billing_address}{/if}
            {else}
              <img src="{$ImagesDir}/icon_shipping.png" width="16" height="9" alt="" />
              {if $mode ne 'select'}{$lng.lbl_shipping_address}{/if}
            {/if}
          </div>
        {/if}

        {include file="customer/main/address_details_html.tpl"}

        <br />
        <div class="buttons-row buttons-auto-separator">
          {if not ($checkout_module eq 'One_Page_Checkout' and $for eq 'cart')}
            {include file="customer/buttons/button.tpl" button_title=$lng.lbl_change href="javascript: popupOpen('popup_address.php?id=`$address.id`');" link_href="popup_address.php?id=`$address.id`" target="_blank"}
          {/if}
          {if $address.default_s ne 'Y' and $address.default_b ne 'Y'}
            {include file="customer/buttons/button.tpl" button_title=$lng.lbl_delete href="javascript: if (confirm('`$lng.txt_are_you_sure`')) self.location = 'address_book.php?mode=delete&amp;id=`$address.id`'"}
          {/if}
        </div>

      {/if}

    </div>
  </div>
</li>
