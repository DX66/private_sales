{*
$Id: cc_info.tpl,v 1.2 2010/07/21 13:57:48 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $config.General.check_cc_number eq "Y"}
  {assign var='is_cc_required' value='Y'}
  {assign var='is_cvv2_required' value='Y'}
{/if}

{if $hide_header ne "Y"}
  <a name="ccinfo"></a>
  <h3>{$lng.lbl_credit_card_information}</h3>
{/if}

<ul>
  <li class="single-field">
    {capture name=regfield}
    {if #safeCCNum# eq ""}
      <select name="card_type[{unique_key}]" {if $is_cvv2_required eq 'Y'}onchange="javascript: markCVV2(this)"{/if} id="card_type">
        {foreach from=$card_types item=ct}
          <option value="{$ct.code}"{if $userinfo.card_type eq $ct.code} selected="selected"{/if}>{$ct.type}</option>
        {/foreach}
      </select>
    {else}
      {#safeCCType#}
      <input type="hidden" name="card_type[{unique_key}]" id="card_type" value="{#safeCCType#}" />
    {/if}
    {/capture}
    {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield required=$is_cc_required name=$lng.lbl_cc_type field="card_type"}
  </li>

  <li class="single-field">
    {capture name=regfield}
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

      <input type="text" name="card_name[{unique_key}]" size="32" maxlength="50" value="{if $userinfo.card_name ne ""}{$userinfo.card_name|escape}{else}{$card_firstname|escape}{if $card_firstname ne ''} {/if}{$card_lastname|escape}{/if}" id="card_name" />

    {else}

      {#safeCCName#}
     <input type="hidden" name="card_name[{unique_key}]" value="{#safeCCName#}" id="card_name" />

    {/if}
    {/capture}
    {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield required=$is_cc_required name=$lng.lbl_cc_name_explanation field="card_name"}
  </li>

  <li class="single-field">
    {capture name=regfield}
    {if #safeCCNum# eq ""}
      <input type="text" name="card_number[{unique_key}]" size="32" maxlength="20" value="{if $smarty.get.err eq 'fields'}{$userinfo.card_number}{elseif $userinfo.card_number_w ne '' and $payment_data.paymentid ne ''}{$userinfo.card_number_w}{/if}" id="card_number" />

      {if $smarty.get.err eq 'fields' and $userinfo.card_number eq ''}
        <span class="error-message">&lt;&lt;</span>
      {/if}

    {else}
      {#safeCCNum#}
      <input type="hidden" name="card_number[{unique_key}]" value="{#safeCCNum#}" />
    {/if}
    {/capture}
    {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield required=$is_cc_required name=$lng.lbl_cc_number_explanation field="card_number"}
  </li>

  {if $config.General.uk_oriented_ccinfo eq "Y" or $force_uk_ccinfo}
  <li class="single-field">
    {capture name=regfield}
      {html_select_date prefix="card_valid_from_" display_days=false start_year="-5" month_format="%m" time=$userinfo.card_valid_from_time|default:'--' use_unique_key=true year_empty="" month_empty=""}
    {/capture}
    {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield name=$lng.lbl_cc_validfrom field="card_valid_from"}
  </li>
  {/if}

  <li class="single-field">
    {capture name=regfield}
    {if #safeCCNum# eq ""}
      {html_select_date prefix="card_expire_" display_days=false end_year="+10" month_format="%m" time=$userinfo.card_expire_time use_unique_key=true}
    {else}
      {#safeCCExp#}
      <input type="hidden" name="card_expire[{unique_key}]" value="{#safeCCExp#}" id="card_expire" />
    {/if}
    {/capture}
    {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield required=$is_cc_required name=$lng.lbl_cc_expiration}
  </li>
  
  {if $payment_cc_data.disable_ccinfo eq "N" or ($payment_cc_data.disable_ccinfo eq "" and $config.General.enable_manual_cc_cvv2 eq 'Y')}
    {foreach from=$card_types item=ct}
      {if $userinfo.card_type eq $ct.code and $ct.cvv2 eq ''}{assign var='is_cvv2_required' value='N'}{/if}
    {/foreach}
    <li class="single-field">
      {capture name=regfield}
      {if #safeCCNum# eq ""}
        <input type="text" name="card_cvv2[{unique_key}]" size="4" maxlength="4" value="{if $smarty.get.err eq 'fields'}{$userinfo.card_cvv2}{elseif $userinfo.card_cvv2_w ne '' and $payment_data.paymentid ne ''}{$userinfo.card_cvv2_w}{/if}" style="width:50px;" id="card_cvv2" />
        {include file="customer/main/popup_help_link.tpl" section="CVV2" title=$lng.lbl_what_is_cvv2}
      {else}
        {#safeCCcvv2#}
        <input type="hidden" name="card_cvv2[{unique_key}]" value="{#safeCCcvv2#}" />
      {/if}
      {/capture}
      {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield required=$is_cvv2_required name=$lng.lbl_cc_cvv2 field="card_cvv2"}
    </li>
  {/if}

  {if $config.General.uk_oriented_ccinfo eq "Y" or $force_uk_ccinfo}
    <li class="single-field">
      {capture name=regfield}
        <input type="text" name="card_issue_no[{unique_key}]" size="4" maxlength="2" value="" /><br />
        {$lng.lbl_cc_leave_empty}
      {/capture}
      {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield name=$lng.lbl_cc_issueno field="card_issue_no"}
    </li>
  {/if}

</ul>
