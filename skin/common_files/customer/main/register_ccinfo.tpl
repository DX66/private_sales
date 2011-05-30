{*
$Id: register_ccinfo.tpl,v 1.3 2010/07/30 08:32:11 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $checkout_module eq 'One_Page_Checkout' and $main eq 'checkout'}

  {include file="modules/One_Page_Checkout/profile/cc_info.tpl" hide_header="Y"}

{else}
  
  {if $config.General.check_cc_number eq "Y" and ($payment_cc_data.type eq "C" or $payment_data.paymentid eq 1 or  ($payment_data.processor_file eq "ps_paypal_pro.php" and $payment_cc_data.paymentid ne $payment_data.paymentid)) and $payment_cc_data.disable_ccinfo ne "Y"}
    {assign var='is_cc_required' value='Y'}
    {if $payment_cc_data.disable_ccinfo ne "C" and $payment_cc_data ne ""}
    {assign var='is_cvv2_required' value='Y'}
    {/if}
  {/if}
  
  <tr style="display: none;">
    <td>{include file="check_cc_number_script.tpl"}</td>
  </tr>
  
  {if $hide_header ne "Y"}
    <tr>
      <td class="register-section-title" colspan="3">
        <div>
          <a name="ccinfo"></a>
          <label>{$lng.lbl_credit_card_information}</label>
        </div>
      </td>
    </tr>
  {/if}
  
  <tr>
    <td class="data-name">
      {if $hide_header eq 'Y'}
        <a name="ccinfo"></a>
      {/if}
      {$lng.lbl_cc_type}
    </td>
    <td{if $is_cc_required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
    <td>
  
      {if #safeCCNum# eq ""}
        <select name="card_type[{unique_key}]" {if $is_cvv2_required eq 'Y'}onchange="javascript: markCVV2(this)"{/if}>
          {foreach from=$card_types item=ct}
            {if $ct.code ne "AMEX" or $payment_data.processor_file ne "ps_paypal_pro.php" or $config.paypal_amex eq "Y"}
            <option value="{$ct.code}"{if $userinfo.card_type eq $ct.code} selected="selected"{/if}>{$ct.type}</option>
            {/if}
          {/foreach}
        </select>
        {if $smarty.get.err eq 'fields' and $userinfo.card_type eq ''}
          <span class="error-message">&lt;&lt;</span>
        {/if}
      {else}
        {#safeCCType#}
        <input type="hidden" name="card_type[{unique_key}]" value="{#safeCCType#}" />
      {/if}
    </td>
  </tr>
  
  <tr>
    <td class="data-name">{$lng.lbl_cc_name_explanation}</td>
    <td{if $is_cc_required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
    <td>
  
      {if #safeCCNum# eq ""}
  
        {if $userinfo.b_firstname ne ''}
          {assign var="card_firstname" value=$userinfo.b_firstname}
        {else}
          {assign var="card_firstname" value=$userinfo.firstname}
        {/if}
  
        {if $userinfo.b_lastname ne ''}
          {assign var="card_lastname" value=$userinfo.b_lastname}
        {else}
          {assign var="card_lastname" value=$userinfo.lastname}
        {/if}
  
        <input type="text" name="card_name[{unique_key}]" size="32" maxlength="50" value="{if $userinfo.card_name ne ""}{$userinfo.card_name|escape}{else}{$card_firstname|escape}{if $card_firstname ne ''} {/if}{$card_lastname|escape}{/if}" />
  
        {if $smarty.get.err eq 'fields' and $userinfo.card_name eq ''}
          <span class="error-message">&lt;&lt;</span>
        {/if}
  
      {else}
        {#safeCCName#}
       <input type="hidden" name="card_name[{unique_key}]" value="{#safeCCName#}" />
      {/if}
    </td>
  </tr>
  
  <tr>
    <td class="data-name">{$lng.lbl_cc_number_explanation}</td>
    <td{if $is_cc_required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
    <td>
  
      {if #safeCCNum# eq ""}
        <input type="text" name="card_number[{unique_key}]" size="32" maxlength="20" value="{if $smarty.get.err eq 'fields'}{$userinfo.card_number}{elseif $userinfo.card_number_w ne '' and $payment_data.paymentid ne ''}{$userinfo.card_number_w}{/if}" />
  
        {if $smarty.get.err eq 'fields' and $userinfo.card_number eq ''}
          <span class="error-message">&lt;&lt;</span>
        {/if}
  
      {else}
        {#safeCCNum#}
        <input type="hidden" name="card_number[{unique_key}]" value="{#safeCCNum#}" />
      {/if}
    </td>
  </tr>
  
  {if $config.General.uk_oriented_ccinfo eq "Y" or $force_uk_ccinfo}
    <tr>
      <td class="data-name">{$lng.lbl_cc_validfrom}</td>
      <td>&nbsp;</td>
      <td>
        {html_select_date prefix="card_valid_from_" display_days=false start_year="-5" month_format="%m" time=$userinfo.card_valid_from_time|default:'--' use_unique_key=true year_empty="" month_empty=""}
      </td>
    </tr>
  {/if}
  
  <tr>
    <td class="data-name">{$lng.lbl_cc_expiration}</td>
    <td{if $is_cc_required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
    <td>
  
      {if #safeCCNum# eq ""}
        {html_select_date prefix="card_expire_" display_days=false end_year="+10" month_format="%m" time=$userinfo.card_expire_time use_unique_key=true}
      {else}
        {#safeCCExp#}
        <input type="hidden" name="card_expire[{unique_key}]" value="{#safeCCExp#}" />
      {/if}
    </td>
  </tr>
  
  {if $payment_cc_data.disable_ccinfo eq "N" or ($payment_cc_data.disable_ccinfo eq "" and $config.General.enable_manual_cc_cvv2 eq 'Y')}
    {foreach from=$card_types item=ct}
    {if $userinfo.card_type eq $ct.code and $ct.cvv2 eq ''}{assign var='is_cvv2_required' value='N'}{/if}
    {/foreach}
    <tr>
      <td class="data-name">{$lng.lbl_cc_cvv2}</td>
      <td id="cvv2_star"{if $is_cvv2_required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
      <td class="valign-middle">
  
        {if #safeCCNum# eq ""}
          <input type="text" name="card_cvv2[{unique_key}]" size="4" maxlength="4" value="{if $smarty.get.err eq 'fields'}{$userinfo.card_cvv2}{elseif $userinfo.card_cvv2_w ne '' and $payment_data.paymentid ne ''}{$userinfo.card_cvv2_w}{/if}" />
          {include file="customer/main/popup_help_link.tpl" section="CVV2" title=$lng.lbl_what_is_cvv2}
          {if $smarty.get.err eq 'fields' and $userinfo.card_cvv2 eq ''}
            <span class="error-message">&lt;&lt;</span>
          {/if}
        {else}
          {#safeCCcvv2#}
          <input type="hidden" name="card_cvv2[{unique_key}]" value="{#safeCCcvv2#}" />
        {/if}
      </td>
    </tr>
  {/if}
  
  {if $config.General.uk_oriented_ccinfo eq "Y" or $force_uk_ccinfo}
    <tr>
      <td class="data-name">{$lng.lbl_cc_issueno}</td>
      <td>&nbsp;</td>
      <td>
        <input type="text" name="card_issue_no[{unique_key}]" size="4" maxlength="2" value="" /><br />
        {$lng.lbl_cc_leave_empty}
      </td>
    </tr>
  {/if}
  
{/if}
