{*
$Id: address_fields.tpl,v 1.6.2.1 2010/10/25 13:32:40 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<tr class="address-row-{$id}"{if $hide} style="display:none;"{/if}>
  <td colspan="4"><hr class="dotted" noshade="noshade" /></td>
</tr>
<tr class="address-row-{$id}"{if $hide} style="display:none;"{/if}>
  <td align="center">
  {if $reg_error}
  <div align="left" style="text-align: left;">
    <font class="Star">{$reg_error.errdesc}</font>
  </div>
  <br />
  {/if}
  <table cellpadding="3" cellspacing="1">

  {if $address_fields.title.avail eq 'Y'}
    <tr>
      <td class="data-name"><label for="title{$id}">{$lng.lbl_title}</label></td>
      <td{if $address_fields.title.required eq 'Y'} class="data-required"{/if}>{if $address_fields.title.required eq 'Y'}*{/if}</td>
      <td>
        {include file="main/title_selector.tpl" val=$address.titleid name="address_book[`$id`][title]" id="title`$id`"}
      </td>
    </tr>
  {/if}

  {if $address_fields.firstname.avail eq 'Y'}
    <tr>
      <td class="data-name"><label for="firstname{$id}">{$lng.lbl_first_name}</label></td>
      <td{if $address_fields.firstname.required eq 'Y'} class="data-required"{/if}>{if $address_fields.firstname.required eq 'Y'}*{/if}</td>
      <td>
        <input type="text" id="firstname{$id}" name="address_book[{$id}][firstname]" size="32" maxlength="32" value="{$address.firstname|escape}" />
      </td>
    </tr>
  {/if}

  {if $address_fields.lastname.avail eq 'Y'}
    <tr>
      <td class="data-name"><label for="lastname{$id}">{$lng.lbl_last_name}</label></td>
      <td{if $address_fields.lastname.required eq 'Y'} class="data-required"{/if}>{if $address_fields.lastname.required eq 'Y'}*{/if}</td>
      <td>
        <input type="text" id="lastname{$id}" name="address_book[{$id}][lastname]" size="32" maxlength="32" value="{$address.lastname|escape}" />
      </td>
    </tr>
  {/if}

  {if $address_fields.address.avail eq 'Y'}
    <tr>
      <td class="data-name"><label for="address{$id}">{$lng.lbl_address}</label></td>
      <td{if $address_fields.address.required eq 'Y'} class="data-required"{/if}>{if $address_fields.address.required eq 'Y'}*{/if}</td>
      <td>
        <input type="text" id="address{$id}" name="address_book[{$id}][address]" size="32" maxlength="64" value="{$address.address|escape}" />
      </td>
    </tr>
  {/if}

  {if $address_fields.address_2.avail eq 'Y'}
    <tr>
      <td class="data-name"><label for="address_2{$id}">{$lng.lbl_address_2}</label></td>
      <td{if $address_fields.address_2.required eq 'Y'} class="data-required"{/if}>{if $address_fields.address_2.required eq 'Y'}*{/if}</td>
      <td>
        <input type="text" id="address_2{$id}" name="address_book[{$id}][address_2]" size="32" maxlength="64" value="{$address.address_2|escape}" />
      </td>
    </tr>
  {/if}

  {if $address_fields.city.avail eq 'Y'}
    <tr>
      <td class="data-name"><label for="city{$id}">{$lng.lbl_city}</label></td>
      <td{if $address_fields.city.required eq 'Y'} class="data-required"{/if}>{if $address_fields.city.required eq 'Y'}*{/if}</td>
      <td>
        <input type="text" id="city{$id}" name="address_book[{$id}][city]" size="32" maxlength="64" value="{$address.city|escape}" />
      </td>
    </tr>
  {/if}

  {if $address_fields.county.avail eq 'Y' and $config.General.use_counties eq "Y"}
    <tr>
      <td class="data-name"><label for="county{$id}">{$lng.lbl_county}</label></td>
      <td{if $address_fields.county.required eq 'Y'} class="data-required"{/if}>{if $address_fields.county.required eq 'Y'}*{/if}</td>
      <td>
        {include file="main/counties.tpl" counties=$counties name="address_book[`$id`][county]" default=$address.county country_name="country`$id`" id="county`$id`"}
      </td>
    </tr>
  {/if}

  {if $address_fields.state.avail eq 'Y'}
    <tr>
      <td class="data-name"><label for="state{$id}">{$lng.lbl_state}</label></td>
      <td{if $address_fields.state.required eq 'Y'} class="data-required"{/if}>{if $address_fields.state.required eq 'Y'}*{/if}</td>
      <td>
        {include file="main/states.tpl" states=$states name="address_book[`$id`][state]" default=$address.state|default:$config.General.default_state default_country=$address.country|default:$config.General.default_country country_name="country" id="state`$id`"}
      </td>
    </tr>
  {/if}

  {if $address_fields.country.avail eq 'Y'}
    <tr>
      <td class="data-name"><label for="country{$id}">{$lng.lbl_country}</label></td>
      <td{if $address_fields.country.required eq 'Y'} class="data-required"{/if}>{if $address_fields.country.required eq 'Y'}*{/if}</td>
      <td>
        <select name="address_book[{$id}][country]" id="country{$id}" onchange="check_zip_code_field(this, $('#zipcode{$id}').get(0))">
          {foreach from=$countries item=c}
            <option value="{$c.country_code}"{if $address.country eq $c.country_code or ($c.country_code eq $config.General.default_country and $address.country eq "")} selected="selected"{/if}>{$c.country|amp}</option>
          {/foreach}
        </select>
      </td>
    </tr>
  {/if}

  {if $address_fields.state.avail eq 'Y' and $address_fields.country.avail eq 'Y'}
    <tr style="display: none;">
      <td{if $address_fields.state.required} class="data-required"{/if}>
        {include file="main/register_states.tpl" state_name="address_book[`$id`][state]" country_name="country`$id`" county_name="address_book[`$id`][county]" state_value=$address.state|default:$config.General.default_state county_value=$address.county}
      </td>
    </tr>
  {/if}

  {if $address_fields.zipcode.avail eq 'Y'}
    <tr>
      <td class="data-name"><label for="zipcode{$id}">{$lng.lbl_zip_code}</label></td>
      <td{if $address_fields.zipcode.required eq 'Y'} class="data-required"{/if}>{if $address_fields.zipcode.required eq 'Y'}*{/if}</td>
      <td>
        {include file="main/zipcode.tpl" name="address_book[`$id`][zipcode]" id="zipcode`$id`" val=$address.zipcode zip4=$address.zip4}
      </td>
    </tr>
  {/if}

  {if $address_fields.phone.avail eq 'Y'}
    <tr>
      <td class="data-name"><label for="phone{$id}">{$lng.lbl_phone}</label></td>
      <td{if $address_fields.phone.required eq 'Y'} class="data-required"{/if}>{if $address_fields.phone.required eq 'Y'}*{/if}</td>
      <td>
        <input type="text" id="phone{$id}" name="address_book[{$id}][phone]" size="32" maxlength="32" value="{$address.phone|escape}" />
      </td>
    </tr>
  {/if}

  {if $address_fields.fax.avail eq 'Y'}
    <tr>
      <td class="data-name"><label for="fax{$id}">{$lng.lbl_fax}</label></td>
      <td{if $address_fields.fax.required eq 'Y'} class="data-required"{/if}>{if $address_fields.fax.required eq 'Y'}*{/if}</td>
      <td>
        <input type="text" id="fax{$id}" name="address_book[{$id}][fax]" size="32" maxlength="32" value="{$address.fax|escape}" />
      </td>
    </tr>
  {/if}

  </table>
  </td>
  <td class="hl" valign="top" align="center">
    <input type="radio" name="default_b" value="{$id}"{if $address_book eq '' or ($address ne '' and $address.default_b eq 'Y')} checked="checked"{/if} />
  </td>
  <td class="hl" valign="top" align="center">
    <input type="radio" name="default_s" value="{$id}"{if $address_book eq '' or ($address ne '' and $address.default_s eq 'Y')} checked="checked"{/if} />
  </td>
  {if $id gt 0}
  <td valign="top" align="center">
    <input type="checkbox" name="delete_address[{$id}]" value="Y" />
  </td>
  {else}
    <td colspan="3">&nbsp;</td> 
  {/if}
</tr>
