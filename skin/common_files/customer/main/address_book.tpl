{*
$Id: address_book.tpl,v 1.3 2010/08/03 15:52:10 igoryan Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{if $mode eq 'select'}
  <h1>{$lng.lbl_select_address}</h1>
  <div><a href="address_book.php"{if $is_modal_popup} onclick="javascript: self.location='address_book.php';"{/if}>{$lng.lbl_edit_address_book}</a></div>
{else}
  <h1>{$lng.lbl_address_book}</h1>
{/if}

<br />

<ul class="address-container{if $mode eq 'select'} popup-address{/if}">
  {include file="customer/main/address_box.tpl" add_new=true}
  {if $addresses}
    {foreach from=$addresses item=a}
      {include file="customer/main/address_box.tpl" address=$a}
    {/foreach}
  {/if}
</ul>
