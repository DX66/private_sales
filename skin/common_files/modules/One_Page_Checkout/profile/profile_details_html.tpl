{*
$Id: profile_details_html.tpl,v 1.1 2010/05/21 08:32:46 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="opc-checkout-profile">

  {if $userinfo.field_sections.B}
    {if $userinfo.login ne ''}
      {include file="modules/One_Page_Checkout/profile/address_book_link.tpl" type='B'}
    {/if}
    <div class="opc-section-container">
      {include file="customer/main/address_details_html.tpl" address=$userinfo.address.B default_fields=$userinfo.default_address_fields}
      {if $userinfo.login eq ''}
        <div class="address-line">
          {$lng.lbl_email}: {$userinfo.email}<br />
        </div>
      {/if}
    </div>
  {/if}
  {if $userinfo.field_sections.S and $ship2diff}
    <div class="optional-label">
      <label for="ship2diff">
        <input type="checkbox" id="ship2diff" name="ship2diff" value="Y" checked="checked" disabled="disabled" />
        {$lng.lbl_ship_to_different_address}
      </label>
    </div>
    {if $userinfo.login ne ''}
      {include file="modules/One_Page_Checkout/profile/address_book_link.tpl" type='S'}
    {/if}
    <div class="opc-section-container">
      {include file="customer/main/address_details_html.tpl" address=$userinfo.address.S default_fields=$userinfo.default_address_fields}
    </div>
  {/if}

  {if $userinfo.field_sections.P}
    <h3>{$lng.lbl_personal_details}</h3>
    <div class="opc-section-container">
      <div class="address-line">
        {if $userinfo.default_fields.title and $userinfo.title ne ''}{$userinfo.title} {/if}
        {if $userinfo.default_fields.firstname and $userinfo.firstname ne ''}{$userinfo.firstname} {/if}
        {if $userinfo.default_fields.lastname and $userinfo.lastname ne ''}{$userinfo.lastname}{/if}
      </div>

      <div class="address-line">
        {if $userinfo.default_fields.company and $userinfo.company ne ''}
          {$lng.lbl_company}: {$userinfo.company}<br />
        {/if}
        {if $userinfo.default_fields.url and $userinfo.url ne ''}
          {$lng.lbl_url}: {$userinfo.url}<br />
        {/if}
        {if $userinfo.default_fields.ssn and $userinfo.ssn ne ''}
          {$lng.lbl_ssn}: {$userinfo.ssn}<br />
        {/if}
        {if $userinfo.default_fields.tax_number and $userinfo.tax_number ne ''}
          {$lng.lbl_tax_number}: {$userinfo.tax_number}<br />
        {/if}

        {foreach from=$userinfo.additional_fields item=v}
          {if $v.section eq 'P' and $v.value ne ''}
            {$v.title}: {$v.value}<br />
          {/if}
        {/foreach}
      </div>
    </div>
  {/if}

  {if $userinfo.field_sections.A}
    <h3>{$lng.lbl_additional_information}</h3>
    <div class="opc-section-container">
      <div class="address-line">
        {foreach from=$userinfo.additional_fields item=v}
          {if $v.section eq 'A'}
            {$v.title}: {$v.value}<br />
          {/if}
        {/foreach}
      </div>
    </div>
  {/if}

  <div class="button-row" align="center">
    {include file="customer/buttons/button.tpl" additional_button_class="main-button edit-profile" button_title=$lng.lbl_change}
  </div>

</div>
