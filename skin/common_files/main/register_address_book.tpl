{*
$Id: register_address_book.tpl,v 1.3 2010/07/28 11:02:15 igoryan Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{if $userinfo.id gt 0 and $is_areas.B ne ''}

{if $hide_header eq ""}
<tr>
  <td colspan="3" class="RegSectionTitle">
    {include file="main/visiblebox_link.tpl" no_use_class="Y" mark="ab" title=$lng.lbl_address_book extra=' width="100%"'}
    <hr size="1" noshade="noshade" />
  </td>
</tr>
{/if}

<tr id="boxab"{if $reg_error.address eq ''} style="display:none;"{/if}>
  <td colspan="3" width="100%" align="center">
    <table class="address-book-container" cellpadding="3" cellspacing="1" width="100%">
      <tr>
        <th width="70%">
          <span style="float:left;"><a href="javascript:void(0);" onclick="$('.address-row-0').toggle();">{$lng.lbl_add_new_address}</a></span>
          <span style="float:right;">{$lng.lbl_set_address_as_default}:</span>
        </th>
        <th width="10%" class="hl" align="center">{$lng.lbl_billing|lower}</th>
        <th width="10%" class="hl" align="center">{$lng.lbl_shipping|lower}</th>
        <th width="10%" align="center">{$lng.lbl_delete}</th>
      </tr>
      {if $address_book ne ''}
        {assign var=hide_new value="Y"}
      {/if}
      {include file="main/address_fields.tpl" address=$address hide=$hide_new id=0 reg_error=$reg_error.address.0}
      {if $address_book ne ''}
      {foreach from=$address_book item=address key=id}
        {include file="main/address_fields.tpl" address=$address id=$id reg_error=$reg_error.address.$id}
      {/foreach}
      {/if}
    </table>
  </td>
</tr>

<tr>
  <td colspan="3">&nbsp;</td>
</tr>
{/if}
