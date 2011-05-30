{*
$Id: customer_details_html.tpl,v 1.1.2.1 2011/01/05 13:56:37 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $login eq ''}
  {assign var=modify_url value="cart.php?mode=checkout&edit_profile&paymentid=`$paymentid`"}
{/if}

<div class="flc-checkout-address-left">

  {include file="customer/subheader.tpl" title=$lng.lbl_contact_information class="grey"}

  <table cellspacing="0" class="flc-checkout-address" summary="{$lng.lbl_contact_information|escape}">
  <tr class="hidden"><td colspan="2">&nbsp;</td></tr>

  <tr>   
    <td>{$lng.lbl_email}:</td>
    <td>{$userinfo.email}</td>
  </tr>

{if $userinfo.default_fields.title}
    <tr>
      <td>{$lng.lbl_title}:</td>
      <td>{$userinfo.title}</td>
    </tr>
{/if}

{if $userinfo.default_fields.firstname}
    <tr>
      <td>{$lng.lbl_first_name}:</td>
      <td>{$userinfo.firstname}</td>
    </tr>
{/if}

{if $userinfo.default_fields.lastname}
    <tr> 
      <td>{$lng.lbl_last_name}:</td>
      <td>{$userinfo.lastname}</td>
    </tr>
{/if}

{if $userinfo.default_fields.company}
    <tr> 
      <td>{$lng.lbl_company}:</td>
      <td>{$userinfo.company}</td>
    </tr>
{/if}

{if $userinfo.default_fields.tax_number}
    <tr>
      <td>{$lng.lbl_tax_number}:</td>
      <td>{$userinfo.tax_number}</td>
    </tr>
{/if}

{if $userinfo.default_fields.phone}
    <tr> 
      <td>{$lng.lbl_phone}:</td>
      <td>{$userinfo.phone}</td>
    </tr>
{/if}

{if $userinfo.default_fields.fax}
    <tr>  
      <td>{$lng.lbl_fax}:</td>
      <td>{$userinfo.fax}</td>
    </tr>
{/if}

{if $userinfo.default_fields.url}
    <tr>   
      <td>{$lng.lbl_web_site}:</td>
      <td>{$userinfo.url}</td>
    </tr>
{/if}

{foreach from=$userinfo.additional_fields item=v}
{if $v.section eq 'C' or $v.section eq 'P'}
    <tr>
      <td>{$v.title}:</td>
      <td>{$v.value}</td>
    </tr>
{/if}
{/foreach}

  </table>
</div>
<div class="clearing"></div>

<div class="flc-checkout-address-left">
  {include file="customer/subheader.tpl" title=$lng.lbl_billing_address class="grey"}

  <table cellspacing="0" class="flc-checkout-address" summary="{$lng.lbl_billing_address|escape}">
  <tr class="hidden"><td colspan="2">&nbsp;</td></tr>
{if $userinfo.default_address_fields.title}
    <tr>
      <td>{$lng.lbl_title}:</td>
      <td>{$userinfo.b_title}</td>
    </tr>
{/if}

{if $userinfo.default_address_fields.firstname}
    <tr>
      <td>{$lng.lbl_first_name}:</td>
      <td>{$userinfo.b_firstname}</td>
    </tr>
{/if}

{if $userinfo.default_address_fields.lastname}
    <tr>
      <td>{$lng.lbl_last_name}:</td>
      <td>{$userinfo.b_lastname}</td>
    </tr>
{/if}

{if $userinfo.default_address_fields.address}
    <tr>
      <td>{$lng.lbl_address}:</td>
      <td>
        {$userinfo.b_address}
{if $userinfo.b_address_2}
        <br />{$userinfo.b_address_2}
{/if}
      </td>
    </tr>
{/if}

{if $userinfo.default_address_fields.city}
    <tr> 
      <td>{$lng.lbl_city}:</td>
      <td>{$userinfo.b_city}</td>
    </tr>
{/if}

{if $userinfo.default_address_fields.state}
    <tr> 
      <td>{$lng.lbl_state}:</td>
      <td>{$userinfo.b_statename}</td>
    </tr>
{/if}

{if $userinfo.default_address_fields.country}
    <tr> 
      <td>{$lng.lbl_country}:</td>
      <td>{$userinfo.b_countryname}</td>
    </tr>
{/if}

{if $userinfo.default_address_fields.zipcode}
    <tr> 
      <td>{$lng.lbl_zip_code}:</td>
      <td>{include file="main/zipcode.tpl" val=$userinfo.b_zipcode zip4=$userinfo.b_zip4 static=true}</td>
    </tr>
{/if}

{foreach from=$userinfo.additional_fields item=v}
{if $v.section eq 'B'}
    <tr>
      <td>{$v.title}:</td>
      <td>{$v.value}</td>
    </tr>
{/if}
{/foreach}

{if $login ne ''}
    {assign var=modify_url value="javascript: popupOpen('popup_address.php?mode=select&amp;for=cart&amp;type=B');"}
    {assign var=link_href value="popup_address.php?mode=select&for=cart&type=B"}
{/if}
    <tr><td colspan="2">{include file="customer/buttons/modify.tpl" href=$modify_url link_href=$link_href|default:$modify_url style="link"}</td></tr>

  </table>
</div>

<div class="flc-checkout-address-right">
  {include file="customer/subheader.tpl" title=$lng.lbl_shipping_address class="grey"}

  <table cellspacing="0" class="flc-checkout-address" summary="{$lng.lbl_shipping_address|escape}">
  <tr class="hidden"><td colspan="2">&nbsp;</td></tr>
{if $userinfo.default_address_fields.title}
    <tr>
      <td>{$lng.lbl_title}:</td>
      <td>{$userinfo.s_title}</td>
    </tr>
{/if}

{if $userinfo.default_address_fields.firstname}
    <tr>
      <td>{$lng.lbl_first_name}:</td>
      <td>{$userinfo.s_firstname}</td>
    </tr>
{/if}

{if $userinfo.default_address_fields.lastname}
    <tr> 
      <td>{$lng.lbl_last_name}:</td>
      <td>{$userinfo.s_lastname}</td>
    </tr>
{/if}

{if $userinfo.default_address_fields.address}
    <tr> 
      <td>{$lng.lbl_address}:</td>
      <td>
        {$userinfo.s_address}
{if $userinfo.s_address_2}
        <br />{$userinfo.s_address_2}
{/if}
      </td>
    </tr>
{/if}

{if $userinfo.default_address_fields.city}
    <tr> 
      <td>{$lng.lbl_city}:</td>
      <td>{$userinfo.s_city}</td>
    </tr>
{/if}

{if $userinfo.default_address_fields.state}
    <tr> 
      <td>{$lng.lbl_state}:</td>
      <td>{$userinfo.s_statename}</td>
    </tr>
{/if}

{if $userinfo.default_address_fields.country}
    <tr> 
      <td>{$lng.lbl_country}:</td>
      <td>{$userinfo.s_countryname}</td>
    </tr>
{/if}

{if $userinfo.default_address_fields.zipcode}
    <tr> 
      <td>{$lng.lbl_zip_code}:</td>
      <td>{include file="main/zipcode.tpl" val=$userinfo.s_zipcode zip4=$userinfo.s_zip4 static=true}</td>
    </tr>
{/if}

{foreach from=$userinfo.additional_fields item=v}
{if $v.section eq 'S'}
    <tr>
      <td>{$v.title}:</td>
      <td>{$v.value}</td>
    </tr>
{/if}
{/foreach}

{if $login ne ''}
    {assign var=modify_url value="javascript: popupOpen('popup_address.php?mode=select&amp;for=cart&amp;type=S');"}
    {assign var=link_href value="popup_address.php?mode=select&for=cart&type=S"}
{/if}
    <tr><td colspan="2">{include file="customer/buttons/modify.tpl" href=$modify_url link_href=$link_href|default:$modify_url style="link"}</td></tr>

  </table>
</div>
<div class="clearing"></div>

{capture name=addfields}
{foreach from=$userinfo.additional_fields item=v}
{if $v.section eq 'A'}
    <tr>
      <td>{$v.title}:</td>
      <td>{$v.value}</td>
    </tr>
{/if}
{/foreach}
{/capture}

{if $smarty.capture.addfields ne ""}

<div class="flc-checkout-address-left">
  {include file="customer/subheader.tpl" title=$lng.lbl_additional_information class="grey"}

  <table cellspacing="0" class="flc-checkout-address" summary="{$lng.lbl_additional_information|escape}">
    {$smarty.capture.addfields}
  </table>
</div>
<div class="clearing"></div>

{/if}
