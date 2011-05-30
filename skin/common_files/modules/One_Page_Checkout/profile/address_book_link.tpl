{*
$Id: address_book_link.tpl,v 1.2.2.4 2011/02/24 13:50:32 aim Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="address-book-link">
  {if $change_mode eq 'Y'}
    <label class="save-new" for="new_{$type}">
      <input type="checkbox" name="new_address[{$type}]" id="new_{$type}" value="{$addressid}" onclick="javascript: if (this.checked) $('#existing_{$type}').attr('checked', false);" />
      {$lng.lbl_save_as_new_address}
    </label>
    {if $addressid gt 0}
    <br />
    <label class="update-existing" for="existing_{$type}">
      <input type="checkbox" name="existing_address[{$type}]" id="existing_{$type}" value="{$addressid}" onclick="javascript:  if (this.checked) $('#new_{$type}').attr('checked', false); " checked="checked" />
      {$lng.lbl_update_existing_address}
    </label>
    {/if}
  {/if}
  <span class="popup-link">
    <a href="popup_address.php?mode=select&amp;for=cart&amp;type={$type}" onclick="javascript: popupOpen('popup_address.php?mode=select&amp;for=cart&amp;type={$type|escape:"javascript"}'); return false;" title="{$lng.lbl_address_book|escape}">{$lng.lbl_address_book}</a>
  </span>
  <div class="clearing"></div>
</div>
