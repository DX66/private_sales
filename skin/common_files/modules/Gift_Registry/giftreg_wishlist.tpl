{*
$Id: giftreg_wishlist.tpl,v 1.1 2010/05/21 08:32:23 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $show ne "event_details_customer" and $events_list}

  {if $is_gc}
    <form action="giftreg_manage.php" name="moveproduct_form{$wlitem_data.wishlistid}" method="post">
      <input type="hidden" name="mode" value="move_product" />
      <input type="hidden" name="wlitem" value="{$wlitem_data.wishlistid}" />
  {/if}

  <br />
  <br />

  {include file="customer/subheader.tpl" title=$lng.lbl_giftreg_move_product}

  <table cellspacing="0" class="data-table giftreg-selector">
    <tr>
      <td class="data-name">{$lng.lbl_event}:</td>
      <td class="data-required">&nbsp;</td>
      <td>
        <select id="eventid_{$wlitem_data.wishlistid}" name="eventid_to">
{if $eventid ne ""}
          <option value="0">[{$lng.lbl_back_to_wish_list|wm_remove|escape}]</option>
{/if}
{foreach from=$events_list item=e}
{if $e.event_id ne $eventid}
          <option value="{$e.event_id}">{$e.title}</option>
{/if}
{/foreach}
        </select>
      </td>
    </tr>

{if not $is_gc}

    <tr>
      <td class="data-name">{$lng.lbl_quantity}:</td>
      <td class="data-required">&nbsp;</td>
      <td>
        <input type="text" size="3" name="move_quantity" value="{$wlitem_data.amount}" />
      </td>
    </tr>

{else}

    <tr style="display: none;">
      <td colspan="3">
        <input type="hidden" name="move_quantity" value="1" />
      </td>
    </tr>

{/if}

    <tr>
      <td colspan="2">&nbsp;</td>
      <td>
{if $is_gc}
        {include file="customer/buttons/button.tpl" button_title=$lng.lbl_move type="input" additional_button_class="light-button"}
{else}
        {include file="customer/buttons/button.tpl" button_title=$lng.lbl_move href="javascript: submitForm(document.`$form_name`, 'move_product');" additional_button_class="light-button"}
{/if}
      </td>
    </tr>

  </table>

{if $is_gc}
</form>
{/if}

{/if}
