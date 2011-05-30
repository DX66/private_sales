{*
$Id: payment_cc_form.tpl,v 1.1 2010/05/21 08:32:53 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
  
{assign var=cc_current_year value=$smarty.now|date_format:"%Y"}
{assign var=month_format value="%01d"}
  
<div id="payment-form">

  <table cellpadding="1" cellspacing="3" border="0" align="center">
    <tr style="display: none;">
      <td>{include file="check_cc_number_script.tpl"}</td>
    </tr>
  
  {if $cc_form_fields.card_type}
    <tr>
      <td class="data-name">
        {$lng.lbl_cc_type}
      </td>
      <td class="data-required">{if $cc_form_fields.card_type.required eq 'Y'}*{/if}</td>
      {assign var=cc_types value=$cc_form_fields.card_type.cc_types}
      <td>
        <select name="{$cc_form_fields.card_type.field_name|default:"card_type"}">
          {foreach from=$card_types item=ct}
            {if $cc_types[$ct.code] or $cc_types eq ''}
              <option value="{$cc_types[$ct.code]}">{$ct.type}</option>
            {/if}
          {/foreach}
        </select>
      </td>
    </tr>
  {/if}
  
  {if $cc_form_fields.card_name}
    <tr>
      <td class="data-name">{$lng.lbl_cc_name_explanation}</td>
      <td class="data-required">{if $cc_form_fields.card_name.required eq 'Y'}*{/if}</td>
      <td>
        <input type="text" name="{$cc_form_fields.card_name.field_name|default:"card_name"}" size="32" maxlength="50" value="" />
      </td>
    </tr>
  {/if}
  
  {if $cc_form_fields.card_number}
    <tr>
      <td class="data-name">{$lng.lbl_cc_number_explanation}</td>
      <td class="data-required">{if $cc_form_fields.card_number.required eq 'Y'}*{/if}</td>
      <td>
        {if $cc_form_fields.card_number.split}
          {foreach from=$cc_form_fields.card_number.fields item=fn}
            <input type="text" name="{$fn}" size="4" maxlength="4" value="" autocomplete="off" />&nbsp;
          {/foreach}
        {else}
          <input type="text" name="{$cc_form_fields.card_number.field_name|default:"card_number"}" size="32" maxlength="20" value="" autocomplete="off" />
        {/if}
      </td>
    </tr>
  {/if}
  
  {if $cc_form_fields.valid_from_year or $cc_form_fields.valid_from_month}

    {if $cc_form_fields.valid_from_year.year_format}
      {assign var=cc_current_year value=$smarty.now|date_format:$cc_form_fields.valid_from_year.year_format}
    {/if}

    {if $cc_form_fields.valid_from_month.format}
      {assign var=month_format value=$cc_form_fields.valid_from_month.format}
    {/if}

    <tr>
      <td class="data-name">{$lng.lbl_cc_validfrom}</td>
      <td>&nbsp;</td>
      <td>
        <select name="{$cc_form_fields.valid_from_month.field_name|default:"valid_from_month"}">
        {section name=vm loop=13 start=1}
          <option value="{%vm.index%}">{%vm.index%}</option>
        {/section}
        </select>
        &nbsp;/&nbsp;
        <select name="{$cc_form_fields.valid_from_year.field_name|default:"valid_from_year"}">
        {section name=vy loop=`$cc_current_year-5` start=$cc_current_year}
          <option value="{%vy.index%|string_format:"`$month_format`"}">{%vy.index%}</option>
        {/section}
        </select>
      </td>
    </tr>
  {/if}
  
  {if $cc_form_fields.exp_year or $cc_form_fields.exp_month}

    {if $cc_form_fields.exp_year.year_format}
      {assign var=cc_current_year value=$smarty.now|date_format:$cc_form_fields.exp_year.year_format}
    {/if}

    {if $cc_form_fields.exp_year.year_format}
      {assign var=month_format value=$cc_form_fields.exp_month.format}
    {/if}

    <tr>
      <td class="data-name">{$lng.lbl_cc_expiration}</td>
      <td class="data-required">{if $is_cc_required eq 'Y'}*{/if}</td>
      <td>
        <select name="{$cc_form_fields.exp_month.field_name|default:"exp_month"}">
        {section name=em loop=13 start=1}
          <option value="{%em.index%|string_format:"`$month_format`"}">{%em.index%|string_format:"`$month_format`"}</option>
        {/section}
        </select>
        &nbsp;/&nbsp;
        <select name="{$cc_form_fields.exp_year.field_name|default:"exp_year"}">
        {section name=ey loop=`$cc_current_year+11` start=$cc_current_year}
          <option value="{%ey.index%}">{%ey.index%}</option>
        {/section}
        </select>
      </td>
    </tr>

  {elseif $cc_form_fields.exp_date}

    <tr>
      <td class="data-name">{$lng.lbl_cc_expiration}{if $cc_form_fields.exp_date.format} ({$cc_form_fields.exp_date.format}){/if}</td>
      <td class="data-required">{if $cc_form_fields.exp_date.required eq 'Y'}*{/if}</td>
      <td>
        <input type="text" name="{$cc_form_fields.exp_date.field_name|default:"exp_date"}" size="6" maxlength="4" value="" />
      </td>
    </tr>

  {/if}
  
  {if $cc_form_fields.card_cvv2}
    <tr>
      <td class="data-name">{$lng.lbl_cc_cvv2}</td>
      <td class="data-required">{if $cc_form_fields.card_cvv2.required eq 'Y'}*{/if}</td>
      <td class="valign-middle">
        <input type="text" name="{$cc_form_fields.card_cvv2.field_name|default:"card_cvv2"}" size="4" maxlength="4" value="" />
        {include file="customer/main/popup_help_link.tpl" section="CVV2" title=$lng.lbl_what_is_cvv2}
      </td>
    </tr>
  {/if}
  
  {if $cc_form_fields.issue_no}
    <tr>
      <td class="data-name">{$lng.lbl_cc_issueno}</td>
      <td>&nbsp;</td>
      <td>
        <input type="text" name="{$cc_form_fields.issue_no.field_name|default:"issue_no"}" size="4" maxlength="2" value="" /><br />
        {$lng.lbl_cc_leave_empty}
      </td>
    </tr>
  {/if}
  
  </table>
  
  <br />

  <div class="payment-note halign-center">  
    {$lng.txt_cc_direct_post_note|substitute:"payment_name":$payment}
  </div>
  
  <div class="halign-center">
    <div class="buttons-row">
      {include file="customer/buttons/button.tpl" button_title=$lng.lbl_cancel href="`$xcart_web_dir`/cart.php?mode=checkout&amp;paymentid=`$paymentid`" style="link"}
      <div class="button-separator"></div>
      {include file="customer/buttons/button.tpl" type="input" additional_button_class="main-button" button_title=$lng.lbl_submit}
    </div>
  </div>
  
</div>
