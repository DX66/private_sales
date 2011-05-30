{*
$Id: customer_details.tpl,v 1.1 2010/05/21 08:32:03 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table summary="{$lng.lbl_profile|escape}">

  {if $userinfo.field_sections.P}
    <tr>
      <td colspan="2">
        {$lng.lbl_personal_information}:
        <hr align="left" noshade="noshade" size="1" />
      </td>
    </tr>
    {if $userinfo.default_fields.title}
      <tr>
        <td>{$lng.lbl_title}:</td>
        <td>{$userinfo.title}&nbsp;</td>
      </tr>
    {/if}

    {if $userinfo.default_fields.firstname}
      <tr>
        <td>{$lng.lbl_first_name}:</td>
        <td>{$userinfo.firstname}&nbsp;</td>
      </tr>
    {/if}

    {if $userinfo.default_fields.lastname}
      <tr>
        <td>{$lng.lbl_last_name}:</td>
        <td>{$userinfo.lastname}&nbsp;</td>
      </tr>
    {/if}

    {if $userinfo.default_fields.phone}
      <tr>
        <td>{$lng.lbl_phone}:</td>
        <td>{$userinfo.phone}&nbsp;</td>
      </tr>
    {/if}

    {if $userinfo.default_fields.fax}
      <tr>
        <td>{$lng.lbl_fax}:</td>
        <td>{$userinfo.fax}&nbsp;</td>
      </tr>
    {/if}

    {if $userinfo.default_fields.email}
      <tr>
        <td>{$lng.lbl_email}:</td>
        <td>{$userinfo.email}&nbsp;</td>
      </tr>
    {/if}

    {if $userinfo.default_fields.url}
      <tr>
        <td>{$lng.lbl_web_site}:</td>
        <td>{$userinfo.url}&nbsp;</td>
      </tr>
    {/if}

    {if $userinfo.default_fields.tax_number}
      <tr>
        <td>{$lng.lbl_tax_number}:</td>
        <td>{$userinfo.tax_number}&nbsp;</td>
      </tr>
    {/if}

    {foreach from=$userinfo.additional_fields item=v}
      {if $v.section eq 'C' or $v.section eq 'P'}
        <tr>
        <td>{$v.title}:</td>
        <td>{$v.value}&nbsp;</td>
      </tr>
      {/if}
    {/foreach}

  {/if}

  {if $userinfo.field_sections.B}
    <tr>
      <td colspan="2">
        {$lng.lbl_billing_address}:
        <hr align="left" noshade="noshade" size="1" />
      </td>
    </tr>

    {if $userinfo.default_address_fields.title}
      <tr>
        <td>{$lng.lbl_title}:</td>
        <td>{$userinfo.b_title}&nbsp;</td>
      </tr>
    {/if}

    {if $userinfo.default_address_fields.firstname}
      <tr>
        <td>{$lng.lbl_first_name}:</td>
        <td>{$userinfo.b_firstname}&nbsp;</td>
      </tr>
    {/if}

    {if $userinfo.default_address_fields.lastname}
      <tr>
        <td>{$lng.lbl_last_name}:</td>
        <td>{$userinfo.b_lastname}&nbsp;</td>
      </tr>
    {/if}

    {if $userinfo.default_address_fields.address}
      <tr>
        <td>{$lng.lbl_address}:</td>
        <td>{$userinfo.b_address}{if $userinfo.b_address_2}<br />{$userinfo.b_address_2}{/if}&nbsp;</td>
      </tr>
    {/if}

    {if $userinfo.default_address_fields.city}
      <tr>
        <td>{$lng.lbl_city}:</td>
        <td>{$userinfo.b_city}&nbsp;</td>
      </tr>
    {/if}

    {if $userinfo.default_address_fields.state}
      <tr>
        <td>{$lng.lbl_state}:</td>
        <td>{$userinfo.b_statename}&nbsp;</td>
      </tr>
    {/if}

    {if $userinfo.default_address_fields.country}
      <tr>
        <td>{$lng.lbl_country}:</td>
        <td>{$userinfo.b_countryname}&nbsp;</td>
      </tr>
    {/if}

    {if $userinfo.default_address_fields.zipcode}
      <tr>
        <td>{$lng.lbl_zip_code}:</td>
        <td>{$userinfo.b_zipcode}&nbsp;</td>
      </tr>
    {/if}

    {foreach from=$userinfo.additional_fields item=v}
      {if $v.section eq 'B'}
        <tr>
        <td>{$v.title}:</td>
        <td>{$v.value}&nbsp;</td>
      </tr>
      {/if}
    {/foreach}

  {/if}

  {if $userinfo.field_sections.S}
    <tr>
      <td colspan="2">
        {$lng.lbl_shipping_address}:
        <hr align="left" noshade="noshade" size="1" />
      </td>
    </tr>

    {if $userinfo.default_address_fields.title}
      <tr>
        <td>{$lng.lbl_title}:</td>
        <td>{$userinfo.s_title}&nbsp;</td>
      </tr>
    {/if}

    {if $userinfo.default_address_fields.firstname}
      <tr>
        <td>{$lng.lbl_first_name}:</td>
        <td>{$userinfo.s_firstname}&nbsp;</td>
      </tr>
    {/if}

    {if $userinfo.default_address_fields.lastname}
      <tr>
        <td>{$lng.lbl_last_name}:</td>
        <td>{$userinfo.s_lastname}&nbsp;</td>
      </tr>
    {/if}

    {if $userinfo.default_address_fields.address}
      <tr>
        <td>{$lng.lbl_address}:</td>
        <td>{$userinfo.s_address}{if $userinfo.s_address_2}<br />{$userinfo.s_address_2}{/if}&nbsp;</td>
      </tr>
    {/if}

    {if $userinfo.default_address_fields.city}
      <tr>
        <td>{$lng.lbl_city}:</td>
        <td>{$userinfo.s_city}&nbsp;</td>
      </tr>
    {/if}

    {if $userinfo.default_address_fields.state}
      <tr>
        <td>{$lng.lbl_state}:</td>
        <td>{$userinfo.s_statename}&nbsp;</td>
      </tr>
    {/if}

    {if $userinfo.default_address_fields.country}
      <tr>
        <td>{$lng.lbl_country}:</td>
        <td>{$userinfo.s_countryname}&nbsp;</td>
      </tr>
    {/if}

    {if $userinfo.default_address_fields.zipcode}
      <tr>
        <td>{$lng.lbl_zip_code}:</td>
        <td>{$userinfo.s_zipcode}&nbsp;</td>
      </tr>
    {/if}

    {foreach from=$userinfo.additional_fields item=v}
      {if $v.section eq 'S'}
        <tr>
        <td>{$v.title}:</td>
        <td>{$v.value}&nbsp;</td>
      </tr>
      {/if}
    {/foreach}
  {/if}

  {assign var="is_header" value=""}
  {foreach from=$userinfo.additional_fields item=v}
    {if $v.section eq 'A'}
      {if $is_header ne 'Y'}
        <tr>
          <td colspan="2">
            {$lng.lbl_additional_information}:
            <hr align="left" noshade="noshade" size="1" />
          </td>
        </tr>
        {assign var="is_header" value="Y"}
      {/if}
      <tr>
        <td>{$v.title}:</td>
        <td>{$v.value}&nbsp;</td>
      </tr>
    {/if}
  {/foreach} 

</table>
