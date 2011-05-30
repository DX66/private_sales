{*
$Id: checkout_2_method.tpl,v 1.2 2010/07/22 10:15:48 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>{$lng.lbl_shipping_and_payment}</h1>

{load_defer file="js/popup_open.js" type="js"}

{capture name=dialog}

{if $smarty.get.err eq 'gc_not_enough_money'}
<div class="center error-message">{$lng.txt_gc_not_enough_money}</div>
{/if}

<form action="cart.php" method="post" name="cartform">

  <input type="hidden" name="mode" value="checkout" />
  <input type="hidden" name="cart_operation" value="cart_operation" />
  <input type="hidden" name="action" value="update" />

{if $config.Shipping.enable_shipping eq "Y"}

<div class="flc-checkout-container">
  <div class="flc-address">

    {include file="customer/subheader.tpl" title=$lng.lbl_shipping_address}

{if $userinfo}
{if $userinfo.default_address_fields.address}{$userinfo.s_address}<br />{/if}
{if $userinfo.default_address_fields.address_2 and $userinfo.s_address_2}
{$userinfo.s_address_2}<br />
{/if}
{if $userinfo.default_address_fields.city}{$userinfo.s_city}<br />{/if}
{if $userinfo.default_address_fields.county and $config.General.use_counties eq "Y" and $userinfo.s_county}{$userinfo.s_countyname}<br />{/if}
{if $userinfo.default_address_fields.state}{$userinfo.s_statename}<br />{/if}
{if $userinfo.default_address_fields.country}{$userinfo.s_countryname}<br />{/if}
{if $userinfo.default_address_fields.zipcode}{include file="main/zipcode.tpl" val=$userinfo.s_zipcode zip4=$userinfo.s_zip4 static=true}{/if}
{else}
No data
{/if}

{assign var=modify_url value="cart.php?mode=checkout&edit_profile&paymentid=`$paymentid`"}

{if $userinfo ne ""}
<div class="text-pre-block">
  {if $login ne ''}
    {assign var=modify_url value="javascript: popupOpen('popup_address.php?mode=select&amp;for=cart&amp;type=S');"}
    {assign var=link_href value="popup_address.php?mode=select&for=cart&type=S"}
  {/if}
  {include file="customer/buttons/modify.tpl" href=$modify_url link_href=$link_href|default:$modify_url style="link"}
</div>
{/if}

  </div>
  <div class="flc-checkout-options">

    {include file="customer/subheader.tpl" title=$lng.lbl_delivery}
    {include file="customer/main/checkout_shipping_methods.tpl"}

  </div>

  <div class="clearing"></div>

</div>

{if $display_ups_trademarks and $current_carrier eq "UPS"}
{include file="modules/UPS_OnLine_Tools/ups_notice.tpl"}
{/if}

{/if}

  <div class="flc-checkout-container">
    <div class="flc-address">

      {include file="customer/subheader.tpl" title=$lng.lbl_billing_address}

{if $userinfo ne ''}

{if $userinfo.default_address_fields.address}{$userinfo.b_address}<br />{/if}
{if $userinfo.default_address_fields.address_2 and $userinfo.b_address_2}
{$userinfo.b_address_2}<br />
{/if}
{if $userinfo.default_address_fields.city}{$userinfo.b_city}<br />{/if}
{if $userinfo.default_address_fields.county and $config.General.use_counties eq "Y" and $userinfo.b_county}{$userinfo.b_countyname}<br />{/if}
{if $userinfo.default_address_fields.state}{$userinfo.b_statename}<br />{/if}
{if $userinfo.default_address_fields.country}{$userinfo.b_countryname}<br />{/if}
{if $userinfo.default_address_fields.zipcode}{include file="main/zipcode.tpl" val=$userinfo.b_zipcode zip4=$userinfo.b_zip4 static=true}{/if}

{else} 

No data 

{/if} 

{if $userinfo}
<br />
<br />
{if $login ne ''}
  {assign var=modify_url value="javascript: popupOpen('popup_address.php?mode=select&amp;for=cart&amp;type=B');"}
  {assign var=link_href value="popup_address.php?mode=select&for=cart&type=B"}
{/if}
{include file="customer/buttons/modify.tpl" href=$modify_url link_href=$link_href|default:$modify_url style="link"}
{/if}

    </div>
    <div class="flc-checkout-options">

      {include file="customer/subheader.tpl" title=$lng.lbl_payment_method}
      
      {include file="customer/main/checkout_payment_methods.tpl}

    </div>

    <div class="clearing"></div>

  </div>

  <br />
  <div class="center">
    <div class="halign-center">
      {include file="customer/buttons/continue.tpl" type="input" additional_button_class="main-button"}
    </div>
  </div>

</form>

{/capture}
{include file="customer/dialog.tpl" title=$lng.lbl_shipping_and_payment content=$smarty.capture.dialog noborder=true additional_class='big_title'}
