{*
$Id: register_address_info.tpl,v 1.1 2010/05/21 08:32:05 joy Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{if $need_address_info}

  {if $hide_header eq ''}
    <tr>
      <td colspan="3" class="register-section-title">
        <div><label>{$lng.lbl_billing_address}</label></div>
      </td>
    </tr>
  {/if}
  {include file="customer/main/address_fields.tpl" default_fields=$address_fields address=$userinfo.address.B id_prefix='b_' name_prefix="address_book[B]" zip_section="billing"}

  {if $config.Shipping.need_shipping_section eq 'Y'}

    {if $hide_header eq ''}
      <tr>
        <td class="register-section-title register-exp-section{if not $ship2diff} register-sec-minimized{/if}" colspan="3">
          <div>
            <label class="pointer" for="ship2diff">{$lng.lbl_ship_to_different_address}</label>
            <input type="checkbox" id="ship2diff" name="ship2diff" value="Y"{if $ship2diff} checked="checked"{/if} />
          </div>
        </td>
      </tr>
    {/if}

    </tbody>
    <tbody id="ship2diff_box">

    {include file="customer/main/address_fields.tpl" default_fields=$address_fields address=$userinfo.address.S id_prefix='s_' name_prefix="address_book[S]" zip_section="shipping"}

    </tbody>
    <tbody>

  {/if}
{/if}
