{*
$Id: gcheckout_top_button.tpl,v 1.1 2010/05/21 08:32:23 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $gcheckout_button and ($config.General.ajax_add2cart ne 'Y' or $config.General.redirect_to_cart eq 'Y')}
  <table cellspacing="0" class="gcheckout-top-button">
    <tr>
{if not $std_checkout_disabled}
      <td>{include file="customer/buttons/button.tpl" button_title=$lng.lbl_checkout  href="cart.php?mode=checkout" additional_button_class="main-button"}</td>
      <td class="gcheckout-or-use">{$lng.lbl_gcheckout_or_use}</td>
{/if}
      <td>{$gcheckout_button}</td>
    </tr>
  </table>
  <div class="clearing"></div>
{/if}
