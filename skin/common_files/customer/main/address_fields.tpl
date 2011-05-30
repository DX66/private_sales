{*
$Id: address_fields.tpl,v 1.4.2.2 2010/10/25 13:32:40 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
  {if $default_fields.title.avail eq 'Y'}
    <tr>
      <td class="data-name"><label for="{$id_prefix}title">{$lng.lbl_title}</label></td>
      <td{if $default_fields.title.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
      <td>
        {include file="main/title_selector.tpl" val=$address.titleid id="`$id_prefix`title" name="`$name_prefix`[title]"}
      </td>
    </tr>
  {/if}

  {if $default_fields.firstname.avail eq 'Y'}
    <tr>
      <td class="data-name"><label for="{$id_prefix}firstname">{$lng.lbl_first_name}</label></td>
      <td{if $default_fields.firstname.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
      <td>
        <input type="text" id="{$id_prefix}firstname" name="{$name_prefix}[firstname]" size="32" maxlength="32" value="{$address.firstname|escape}" />
      </td>
    </tr>
  {/if}

  {if $default_fields.lastname.avail eq 'Y'}
    <tr>
      <td class="data-name"><label for="{$id_prefix}lastname">{$lng.lbl_last_name}</label></td>
      <td{if $default_fields.lastname.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
      <td>
        <input type="text" id="{$id_prefix}lastname" name="{$name_prefix}[lastname]" size="32" maxlength="32" value="{$address.lastname|escape}" />
      </td>
    </tr>
  {/if}

  {if $default_fields.address.avail eq 'Y'}
    <tr>
      <td class="data-name"><label for="{$id_prefix}address">{$lng.lbl_address}</label></td>
      <td{if $default_fields.address.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
      <td>
        <input type="text" id="{$id_prefix}address" name="{$name_prefix}[address]" size="32" maxlength="64" value="{$address.address|escape}" />
      </td>
    </tr>
  {/if}

  {if $default_fields.address_2.avail eq 'Y'}
    <tr>
      <td class="data-name"><label for="{$id_prefix}address_2">{$lng.lbl_address_2}</label></td>
      <td{if $default_fields.address_2.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
      <td>
        <input type="text" id="{$id_prefix}address_2" name="{$name_prefix}[address_2]" size="32" maxlength="64" value="{$address.address_2|escape}" />
      </td>
    </tr>
  {/if}

  {if $default_fields.city.avail eq 'Y'}
    <tr>
      <td class="data-name"><label for="{$id_prefix}city">{$lng.lbl_city}</label></td>
      <td{if $default_fields.city.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
      <td>
        <input type="text" id="{$id_prefix}city" name="{$name_prefix}[city]" size="32" maxlength="64" value="{$address.city|default:$config.General.default_city|escape}" />
      </td>
    </tr>
  {/if}

  {if $default_fields.county.avail eq 'Y' and $config.General.use_counties eq "Y"}
    <tr>
      <td class="data-name"><label for="{$id_prefix}county">{$lng.lbl_county}</label></td>
      <td{if $default_fields.county.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
      <td>
        {include file="main/counties.tpl" counties=$counties name="`$name_prefix`[county]" id="`$id_prefix`county" default=$address.county country_name="`$id_prefix`country"}
      </td>
    </tr>
  {/if}

  {if $default_fields.state.avail eq 'Y'}
    <tr>
      <td class="data-name"><label for="{$id_prefix}state">{$lng.lbl_state}</label></td>
      <td{if $default_fields.state.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
      <td>
        {include file="main/states.tpl" states=$states name="`$name_prefix`[state]" default=$address.state|default:$config.General.default_state default_country=$address.country|default:$config.General.default_country id="`$id_prefix`state" country_name="`$id_prefix`country"}
      </td>
    </tr>
  {/if}

  {if $default_fields.country.avail eq 'Y'}
    <tr>
      <td class="data-name"><label for="{$id_prefix}country">{$lng.lbl_country}</label></td>
      <td{if $default_fields.country.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
      <td>
        <select name="{$name_prefix}[country]" id="{$id_prefix}country" onchange="check_zip_code_field(this, $('#{$id_prefix}zipcode').get(0))">
          {foreach from=$countries item=c}
            <option value="{$c.country_code}"{if $address.country eq $c.country_code or ($c.country_code eq $config.General.default_country and $address.country eq "")} selected="selected"{/if}>{$c.country|amp}</option>
          {/foreach}
        </select>
      </td>
    </tr>
  {/if}

  {if $default_fields.state.avail eq 'Y' and $default_fields.country.avail eq 'Y'}
    <tr style="display: none;">
      <td{if $default_fields.state.required} class="data-required"{/if}>
        {include file="main/register_states.tpl" state_name="`$name_prefix`[state]" country_name="`$id_prefix`country" county_name="`$name_prefix`[county]" state_value=$address.state|default:$config.General.default_state county_value=$address.county}
      </td>
    </tr>
  {/if}

  {if $default_fields.zipcode.avail eq 'Y'}
    <tr>
      <td class="data-name"><label for="{$id_prefix}zipcode">{$lng.lbl_zip_code}</label></td>
      <td{if $default_fields.zipcode.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
      <td>
        {include file="main/zipcode.tpl" zip_section=$zip_section name="`$name_prefix`[zipcode]" id="`$id_prefix`zipcode" val=$address.zipcode|default:$config.General.default_zipcode zip4=$address.zip4}
      </td>
    </tr>
  {/if}

  {if $default_fields.phone.avail eq 'Y'}
    <tr>
      <td class="data-name"><label for="{$id_prefix}phone">{$lng.lbl_phone}</label></td>
      <td{if $default_fields.phone.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
      <td>
        <input type="text" id="{$id_prefix}phone" name="{$name_prefix}[phone]" size="32" maxlength="32" value="{$address.phone|escape}" />
      </td>
    </tr>
  {/if}

  {if $default_fields.fax.avail eq 'Y'}
    <tr>
      <td class="data-name"><label for="{$id_prefix}fax">{$lng.lbl_fax}</label></td>
      <td{if $default_fields.fax.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
      <td>
        <input type="text" id="{$id_prefix}fax" name="{$name_prefix}[fax]" size="32" maxlength="32" value="{$address.fax|escape}" />
      </td>
    </tr>
  {/if}
