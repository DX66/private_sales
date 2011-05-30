{*
$Id: shipping_methods.tpl,v 1.2.2.2 2011/01/04 15:55:56 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{foreach from=$shipping item=s name=sm}
  <label{interline name=sm}>
    {if not $simple_list}
      <input type="radio" name="shippingid" value="{$s.shippingid}"{if $s.shippingid eq $cart.shippingid} checked="checked"{/if}{if $allow_cod} onclick="javascript: display_cod({if $s.is_cod eq 'Y'}true{else}false{/if});"{/if} />
    {/if}
    <span>
      {$s.shipping|trademark}{if $s.shipping_time ne ""} - {$s.shipping_time}{/if}{if $config.Appearance.display_shipping_cost eq "Y" and ($userinfo ne "" or $config.General.apply_default_country eq "Y" or $cart.shipping_cost gt 0)} ({currency value=$s.rate}){/if}
    </span>
  </label>
  {if $s.warning ne ""}
    <div class="{if $s.shippingid eq $cart.shippingid}error-message{else}small-note{/if}">{$s.warning}</div>
  {/if}
{/foreach}
