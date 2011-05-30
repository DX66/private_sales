{*
$Id: address_book_link.tpl,v 1.2.2.3 2010/11/08 13:55:21 aim Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="address-book-link">
  {if $save_new && $type eq 'B'}
    <label class="save-new" for="new_B">
      <input type="checkbox" name="new_address[B]" id="new_B" value="{$addressid}" onclick="javascript: if (this.checked) $('#existing_B').attr('checked', false); $('#new_S').attr('disabled', !this.checked);" />
      {$lng.lbl_save_as_new_address}
    </label>
    {if $addressid gt 0}
    <br />
    <label class="update-existing" for="existing_B">
      <input type="checkbox" name="existing_address[B]" id="existing_B" value="{$addressid}" onclick="javascript:  if (this.checked) $('#new_B').attr('checked', false); $('#existing_S').attr('disabled', !this.checked); " />
      {$lng.lbl_update_existing_address}
    </label>
    {/if}
  {elseif $save_new && $type eq 'S'}
    <input type="hidden" name="new_address[S]" id="new_S" value="{$addressid}" disabled="disabled" />
    {if $addressid gt 0 && $ship2diff eq 'Y'}
      <input type="hidden" name="existing_address[S]" id="existing_S" value="{$addressid}" disabled="disabled" />
    {/if}
  {/if}
  <span class="popup-link">
    <a href="popup_address.php?mode=select&amp;for=cart&amp;type={$type}" onclick="javascript: popupOpen('popup_address.php?mode=select&amp;for=cart&amp;type={$type|escape:"javascript"}'); return false;" title="{$lng.lbl_address_book|escape}">{$lng.lbl_address_book}</a>
  </span>
  <div class="clearing"></div>
</div>
