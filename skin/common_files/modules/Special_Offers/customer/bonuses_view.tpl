{*
$Id: bonuses_view.tpl,v 1.1.2.3 2010/12/15 11:57:06 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

{if $bonus.points gt 0}

  {include file="customer/subheader.tpl" title=$lng.lbl_sp_ttl_bonus_points class=separator}

  <form action="bonuses.php" name="bonuspointsform" method="get">
    <input type="hidden" name="mode" value="points" />

    <table cellspacing="0" class="data-table light-table">

      <tr>
        <td>{$lng.lbl_sp_earned_bonus_points}:</td>
        <td>&nbsp;</td>
        <td>{$bonus.points}</td>
      </tr>

    {if $active_modules.Gift_Certificates}
      <tr>
        <td>{$lng.lbl_sp_bonus_points_min_convert}:</td>
        <td>&nbsp;</td>
        <td>{$config.Special_Offers.offers_bp_min}</td>
      </tr>

      <tr>
        <td>{$lng.lbl_sp_bonus_points_convert_rate}:</td>
        <td>&nbsp;</td>
        <td>{currency value=$config.Special_Offers.offers_bp_rate} {alter_currency value=$config.Special_Offers.offers_bp_rate}</td>
      </tr>

      <tr>
        <td colspan="3" class="button-row">
          {include file="customer/buttons/button.tpl" button_title=$lng.lbl_sp_bonus_points_convert2gc type="input" additional_button_class="light-button"}
        </td>
      </tr>

    {/if}

    </table>

  </form>

{/if}

{if $bonus.memberships ne ''}

  {include file="customer/subheader.tpl" title=$lng.lbl_membership class=separator}

  <form action="bonuses.php" name="bonusmembershipform" method="post">
    <input type="hidden" name="mode" value="membership" />

    <table cellspacing="0" class="data-table light-table">

      <tr>
        <td>{$lng.lbl_sp_change_membership}:</td>
        <td>&nbsp;</td>
        <td>
          <select name="change_to">
            {foreach from=$bonus.memberships item=membership key=membershipid}
              <option value="{$membershipid}">{$membership.membership|escape}</option>
            {/foreach}
          </select>
        </td>
      </tr>

      <tr>
        <td colspan="3" class="button-row">
          {include file="customer/buttons/button.tpl" button_title=$lng.lbl_change type="input" additional_button_class="light-button"}
        </td>
      </tr>

    </table>

  </form>

{/if}
