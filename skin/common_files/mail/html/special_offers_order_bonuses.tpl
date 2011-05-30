{*
$Id: special_offers_order_bonuses.tpl,v 1.1 2010/05/21 08:32:16 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $bonuses.points gt 0 or $bonuses.memberships or $bonuses.blocked_points gt 0}

  {if $bonuses.points gt 0 or $bonuses.memberships}
    <h2>{$lng.lbl_sp_order_bonuses}</h2>
  {/if}

  <div>

    <table cellspacing="3" cellpadding="0" border="0" summary="{$lng.lbl_sp_order_bonuses|escape}">

      {if $bonuses.points gt 0}
        <tr>
          <td>{$lng.lbl_sp_earned_bonus_points}:</td>
          <td>&nbsp;</td>
          <td><strong>{$bonuses.points}</strong></td>
        </tr>
        <tr>
          <td colspan="3">({$lng.lbl_sp_earned_bonus_points_explanation})</td>
        </tr>
      {/if}

      {if $bonuses.memberships}
        <tr>
          <td>{$lng.lbl_sp_customer_bonus_memberships}:</td>
          <td>&nbsp;</td>
          <td>
            {foreach name=memberships from=$bonuses.memberships item=membership}
              <strong>{$membership}</strong>{if $smarty.foreach.memberships.last ne "1"}, {/if}
            {/foreach}
          </td>
        </tr>
      {/if}
  
      {if $bonuses.blocked_points gt 0}
        <tr>
          <td colspan="3"><img src="{$ImagesDir}/spacer.gif" width="1" height="1" alt="" /></td>
        </tr>
        <tr>
          <td>{$lng.lbl_sp_withdrawn_bonus_points}:</td>
          <td>&nbsp;</td>
          <td><strong>{$bonuses.blocked_points}</strong></td>
        </tr>
      {/if}  

    </table>

  </div>

{/if}
