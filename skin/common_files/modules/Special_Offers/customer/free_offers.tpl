{*
$Id: free_offers.tpl,v 1.1 2010/05/21 08:32:48 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $cart.free_offers}

  <script type="text/javascript">
  var bp_balance = {$cart.remained_points};
  var cart_free_offers = [];
  {foreach from=$cart.free_offers item=offer}
  cart_free_offers['{$offer.offerid}'] = {$offer.amount_min};
  {/foreach}
  </script>

  <script type="text/javascript" src="{$SkinDir}/modules/Special_Offers/customer/free_offers.js"></script>

  <div>

    <strong>{$lng.lbl_sp_available_offers}*</strong> ({$lng.lbl_sp_click_to_add_small}):

    <br /><br />

    <table cellpadding="3" summary="{$lng.lbl_sp_offers|escape}">

      {foreach from=$cart.free_offers item=offer key=offerid}
      <tr>
        <td>
          <input type="checkbox" name="free_offers[{$offer.offerid}]" id="free_offers_{$offer.offerid}" value="Y" onclick="javascript: add_remove_free_offer('{$offer.offerid}', this.checked);"{if $cart.applied_free_offers[$offer.offerid] eq "Y"} checked="checked"{elseif $offer.amount_min gt $cart.remained_points} disabled="disabled"{/if} />
        </td>
        <td><label class="cart-free-offer-title" for="free_offers_{$offer.offerid}">{$offer.offer_name}</label></td>
        <td>({$lng.lbl_sp_ttl_bonus_points_N|substitute:amount:$offer.amount_min})</td>
        <td class="offers-more-info"><a href="offers.php?mode=offer&amp;offerid={$offer.offerid}">{$lng.lbl_details}</a></td>
      </tr>
      {/foreach}

    </table>

  </div>

  <br />

  <div>

    <small>*&nbsp;{$lng.txt_sp_reduce_points_balance_note}</small>

  </div>

  <br />

  <div>

    {include file="customer/buttons/button.tpl" button_title=$lng.lbl_sp_apply_offers href="javascript: apply_free_offers();"}

  </div>

  <br />

  <div>

    <table cellpadding="1" summary="{$lng.lbl_sp_ttl_bonus_points|escape}">

      <tr>
        <td>{$lng.lbl_sp_current_bp_balance}:</td>
        <td><strong>{$bonus.points}</strong></td>
      </tr>

      <tr>
        <td>{$lng.lbl_sp_remaining_bp_balance}:</td>
        <td><strong><span id="remained_bp">{$cart.remained_points}</span></strong></td>
      </tr>

    </table>

  </div>

  <hr />

{/if}
