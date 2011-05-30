{*
$Id: cart_bonuses.tpl,v 1.1 2010/05/21 08:32:48 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $cart.bonuses ne ""}
  <div>
    <strong>{$lng.lbl_sp_cart_bonuses_title}</strong>

    <ul>
      {if $cart.bonuses.points ne 0}
        <li>{$lng.lbl_sp_cart_bonuses_bp|substitute:"num":$cart.bonuses.points}</li>
      {/if}
      {if $cart.bonuses.memberships ne ""}
        <li>{$lng.lbl_sp_cart_bonuses_memberships}<br />
          {foreach name=memberships from=$cart.bonuses.memberships item=membership}
            {$membership}
            {if not $smarty.foreach.memberships.last}
              {$lng.lbl_or}
            {/if}
          {/foreach}
        </li>
      {/if}
    </ul>

  </div>

  <hr />

{/if}
