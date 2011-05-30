{*
$Id: gc_cart.tpl,v 1.1.2.3 2010/12/15 11:57:05 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $giftcerts_data ne ""}

  {foreach from=$giftcerts_data item=gc key=gcindex}

    <table cellspacing="0" class="item giftcert-item width-100">
      <tr>
        <td class="image">
          <img src="{$ImagesDir}/spacer.gif" alt="" />
        </td>
        <td class="details">

          {if $g.amount_purchased gt 1}
            <div class="product-details-title">{$lng.lbl_purchased}</div>
          {/if}

          <div class="product-title">{$lng.lbl_gift_certificate}</div>

          <div class="giftcert-item-row">
            <span class="giftcert-item-subtitle">{$lng.lbl_recipient}:</span>
            {$gc.recipient}
          </div>

          {if $gc.send_via eq "E"}
            <div class="giftcert-item-row">
              <span class="giftcert-item-subtitle">{$lng.lbl_email}:</span>
              {$gc.recipient_email}
            </div>

          {elseif $gc.send_via eq "P"}

            <div class="giftcert-item-row">
              <span class="giftcert-item-subtitle">{$lng.lbl_mail_address}:</span>
              {$gc.recipient_address}, {$gc.recipient_city}, {if $config.General.use_counties eq "Y"}{$gc.recipient_countyname} {/if}{$gc.recipient_state} {$gc.recipient_country} {include file="main/zipcode.tpl" val=$giftcert.recipient_zipcode zip4=$giftcert.recipient_zip4 static=true}
            </div>

            {if $gc.recipient_phone}
              <div class="giftcert-item-row">
                <span class="giftcert-item-subtitle">{$lng.lbl_phone}:</span>
                {$gc.recipient_phone}
              </div>
            {/if}

          {/if}

          <div class="giftcert-item-row">
            <span class="giftcert-item-subtitle">{$lng.lbl_amount}:</span>
            <span class="price">{currency value=$gc.amount}</span>
            <span class="market-price">{alter_currency value=$gc.amount}</span>
          </div>

        </td>
      </tr>

      {if $active_modules.Wishlist ne "" and $wl_giftcerts ne ""}

        <tr>
          <td class="buttons-row">

            {if $giftregistry eq ""}
              {include file="customer/buttons/delete_item.tpl" href="cart.php?mode=wldelete&wlitem=`$gc.wishlistid`&eventid=`$eventid`" style="link" additional_button_class="simple-delete-button"}
            {/if}

          </td>
          <td class="buttons-row">

            {if $allow_edit eq "Y"}
              {include file="customer/buttons/modify.tpl" href="giftcert.php?gcindex=`$gc.wishlistid`&action=wl" style="link"}
              <div class="button-separator"></div>
              <div class="button-separator"></div>
              <div class="button-separator"></div>
            {/if}

            {if $login}

              {if $giftregistry eq ""}
                {include file="customer/buttons/add_to_cart.tpl" href="cart.php?mode=wl2cart&wlitem=`$gc.wishlistid`" additional_button_class="light-button"}
              {else}
                {include file="customer/buttons/add_to_cart.tpl" href="cart.php?mode=wl2cart&fwlitem=`$gc.wishlistid`&eventid=`$eventid`" additional_button_class="light-button"}
              {/if}

            {/if}

          </td>
        </tr>

        {if $active_modules.Gift_Registry}
          <tr>
            <td>&nbsp;</td>
            <td>
              {include file="modules/Gift_Registry/giftreg_wishlist.tpl" wlitem_data=$gc is_gc=true}
            </td>
          </tr>
        {/if}

      {else}

        <tr>
          <td class="buttons-row">
            {include file="customer/buttons/delete_item.tpl" href="giftcert.php?mode=delgc&gcindex=`$gcindex`" style="link" additional_button_class="simple-delete-button"}
           </td>
          <td class="buttons-row">
            {include file="customer/buttons/modify.tpl" href="giftcert.php?gcindex=`$gcindex`" style="link"}
          </td>
        </tr>

      {/if}

    </table>

    <hr />

  {/foreach}

{/if}
