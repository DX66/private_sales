{*
$Id: giftcert.tpl,v 1.3.2.2 2010/12/15 09:44:43 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.lbl_gift_certificate}</h1>

{include file="check_email_script.tpl"}
{include file="check_zipcode_js.tpl"}
<script type="text/javascript" src="{$SkinDir}/modules/Gift_Certificates/func.js"></script>

<script type="text/javascript">
//<![CDATA[
var txt_recipient_invalid = "{$lng.txt_recipient_invalid|wm_remove|escape:javascript|strip_tags}";
var txt_amount_invalid = "{$lng.txt_amount_invalid|wm_remove|escape:javascript|strip_tags}";
var txt_gc_enter_mail_address = "{$lng.txt_gc_enter_mail_address|wm_remove|escape:javascript|strip_tags}";
var lbl_giftcertid_is_empty = '{$lng.lbl_giftcertid_is_empty|wm_remove|escape:javascript}';

var orig_mode = "gc2cart";

var min_gc_amount = {$min_gc_amount|default:0};
var max_gc_amount = {$max_gc_amount|default:0};
var enablePostMailGC = {if $config.Gift_Certificates.enablePostMailGC eq 'Y'}true{else}false{/if};
//]]>
</script>

<div class="giftcert-header">
  <img src="{$ImagesDir}/spacer.gif" alt="" />
  {$lng.txt_gc_header}
</div>
<div class="clearing"></div>

{if $login}

  {capture name=dialog}

    <div class="text-block">{$lng.txt_gift_certificate_checking_msg}</div>

    {if $smarty.get.gcid and $gc_array eq ""}
      <span class="error-message">{$lng.err_gc_not_found}</span>
    {/if}

    <form action="giftcert.php" method="get" name="registergiftcert" onsubmit="javascript: if (this.gcid.value != '') return true; alert(lbl_giftcertid_is_empty); return false;">

      <div class="valign-middle">
        <label class="input-block">
          {$lng.lbl_gift_certificate}:
          <input type="text" size="25" maxlength="16" name="gcid" value="{$smarty.get.gcid|escape:"html"}" class="input-field" />
        </label>
        {include file="customer/buttons/submit.tpl" type="input"}
      </div>
    </form>

    {if $gc_array}

      <hr />

      <table cellspacing="0" class="data-table" summary="{$lng.lbl_gift_certificate_checking|escape}">

        <tr>
          <td class="data-name">{$lng.lbl_gc_id}:</td>
          <td>{$gc_array.gcid}</td>
        </tr>

        <tr>
          <td class="data-name">{$lng.lbl_amount}:</td>
          <td>{currency value=$gc_array.amount}</td>
        </tr>

        <tr>
          <td class="data-name">{$lng.lbl_remain}:</td>
          <td>{currency value=$gc_array.debit}</td>
        </tr>

        <tr>
          <td class="data-name">{$lng.lbl_status}:</td>
          <td>
            {if $gc_array.status eq "P"}
              {$lng.lbl_pending}

            {elseif $gc_array.status eq "A"}
              {$lng.lbl_active}

            {elseif $gc_array.status eq "B"}
              {$lng.lbl_blocked}

            {elseif $gc_array.status eq "D"}
              {$lng.lbl_disabled}

            {elseif $gc_array.status eq "E"}
              {$lng.lbl_expired}

            {elseif $gc_array.status eq "U"}
              {$lng.lbl_used}

            {/if}
          </td>
        </tr>
      </table>

    {/if}
  {/capture}
  {include file="customer/dialog.tpl" title=$lng.lbl_gift_certificate_checking content=$smarty.capture.dialog}

{/if}

{capture name=dialog}

  {if $amount_error}
    <div class="error-message">{$lng.txt_amount_invalid}</div>
  {/if}

  <form name="gccreate" action="giftcert.php" method="post" onsubmit="javascript: return check_gc_form();">
    <input type="hidden" name="gcindex" value="{$smarty.get.gcindex|escape:"html"}" />
    <input type="hidden" name="mode" value="gc2cart" />

    <table cellspacing="1" class="data-table giftcert-table" summary="{$lng.lbl_gift_certificate_details|escape}">
      <tr>
        <td colspan="3">
          <div class="giftcert-title">1. {$lng.lbl_gc_whom_sending}</div>
          {$lng.lbl_gc_whom_sending_subtitle}
        </td>
      </tr>

      <tr>
        <td class="data-name">{$lng.lbl_from}</td>
        <td class="data-required">*</td>
        <td>
          <input type="text" name="purchaser" size="30" value="{if $giftcert.purchaser}{$giftcert.purchaser|escape:"html"}{else}{if $userinfo.firstname ne ''}{$userinfo.firstname|escape} {/if}{$userinfo.lastname|escape}{/if}" />
        </td>
      </tr>

      <tr>
        <td class="data-name">{$lng.lbl_to}</td>
        <td class="data-required">*</td>
        <td><input type="text" name="recipient" size="30" value="{$giftcert.recipient|escape:"html"}" /></td>
      </tr>

      <tr>
        <td colspan="3">
          <div class="giftcert-title">2. {$lng.lbl_gc_add_message}</div>
          {$lng.lbl_gc_add_message_subtitle}
        </td>
      </tr>

      <tr>
        <td class="data-name">{$lng.lbl_message}</td>
        <td class="data-required"></td>
        <td><textarea name="message" rows="8" cols="50">{$giftcert.message}</textarea></td>
      </tr>

      <tr>
        <td colspan="3">
          <div class="giftcert-title">3. {$lng.lbl_gc_choose_amount}</div>
          {$lng.lbl_gc_choose_amount_subtitle}
        </td>
      </tr>

      <tr>
        <td class="data-name">{$config.General.currency_symbol}</td>
        <td class="data-required">*</td>
        <td>
          <input type="text" name="amount" size="10" maxlength="9" value="{$giftcert.amount|formatprice}" />
          {if $min_gc_amount gt 0 or $max_gc_amount gt 0}
            {$lng.lbl_gc_amount_msg}
            {if $min_gc_amount gt 0}
              {$lng.lbl_gc_from} {include file="currency.tpl" value=$min_gc_amount}
            {/if}
            {if $max_gc_amount gt 0}
              {$lng.lbl_gc_through} {include file="currency.tpl" value=$max_gc_amount}
            {/if}
          {/if}
        </td>
      </tr>

      <tr>
        <td colspan="3"><div class="giftcert-title">4. {$lng.lbl_gc_choose_delivery_method}</div></td>
      </tr>

      <tr>
        <td colspan="3" class="giftcert-delivery-method">

          {if $config.Gift_Certificates.enablePostMailGC eq "Y"}
            <label>
              <input type="radio" name="send_via" value="E" onclick="javascript: switchPreview();"{if $giftcert.send_via ne "P"} checked="checked"{/if} />
              {$lng.lbl_gc_send_via_email}
            </label>
          {else}
            <input type="hidden" name="send_via" value="E" />
          {/if}
        </td>
      </tr>

      <tr>
        <td colspan="3" class="giftcert-subtitle">{$lng.lbl_gc_enter_email}</td>
      </tr>

      <tr>
        <td class="data-name">{$lng.lbl_email}</td>
        <td class="data-required">*</td>
        <td><input type="text" name="recipient_email" size="30" value="{$giftcert.recipient_email|escape}" /></td>
      </tr>

      {if $config.Gift_Certificates.enablePostMailGC eq "Y"}

        <tr>
          <td colspan="3" class="giftcert-h-separator"><hr /></td>
        </tr>

        <tr>
          <td colspan="3" class="giftcert-delivery-method">
            <label>
              <input id="gc_send_p" type="radio" name="send_via" value="P" onclick="javascript: switchPreview();"{if $giftcert.send_via eq "P"} checked="checked"{/if} />
              {$lng.lbl_gc_send_via_postal_mail}
            </label>
          </td>
        </tr>

        <tr>
          <td colspan="3" class="giftcert-subtitle">{$lng.txt_gc_enter_postal_mail}</td>
        </tr>

        <tr>
          <td class="data-name">{$lng.lbl_first_name}</td>
          <td class="data-required">*</td>
          <td><input type="text" name="recipient_firstname" size="30" value="{$giftcert.recipient_firstname|escape}" /></td>
        </tr>

        <tr>
          <td class="data-name">{$lng.lbl_last_name}</td>
          <td class="data-required">*</td>
          <td><input type="text" name="recipient_lastname" size="30" value="{$giftcert.recipient_lastname|escape}" /></td>
        </tr>

        <tr>
          <td class="data-name">{$lng.lbl_address}</td>
          <td class="data-required">*</td>
          <td><input type="text" name="recipient_address" size="40" value="{$giftcert.recipient_address|escape}" /></td>
        </tr>

        <tr>
          <td class="data-name">{$lng.lbl_city}</td>
          <td class="data-required">*</td>
          <td><input type="text" name="recipient_city" size="30" value="{$giftcert.recipient_city|escape}" /></td>
        </tr>

        {if $config.General.use_counties eq "Y"}
          <tr>
            <td class="data-name">{$lng.lbl_county}</td>
            <td class="data-required">*</td>
            <td>{include file="main/counties.tpl" counties=$counties name="recipient_county" default=$giftcert.recipient_county}</td>
          </tr>
        {/if}

        <tr>
          <td class="data-name">{$lng.lbl_state}</td>
          <td class="data-required">*</td>
          <td>{include file="main/states.tpl" states=$states name="recipient_state" default=$giftcert.recipient_state default_country=$giftcert.recipient_country}</td>
        </tr>

        <tr>
          <td class="data-name">{$lng.lbl_country}</td>
          <td class="data-required">*</td>
          <td>
            <select id="recipient_country" name="recipient_country" size="1" onchange="javascript: check_zip_code_field(this, this.form.recipient_zipcode);">
              {foreach from=$countries item=c}
                <option value="{$c.country_code|escape}"{if $giftcert.recipient_country eq $c.country_code or ($c.country_code eq $config.General.default_country and $giftcert.recipient_country eq "") or ($c.country_code eq $userinfo.b_country and $giftcert.recipient_country eq "")} selected="selected"{/if}>{$c.country}</option>
              {/foreach}
            </select>
          </td>
        </tr>

        <tr style="display: none;">
          <td>
            {include file="change_states_js.tpl"}
            {include file="main/register_states.tpl" state_name="recipient_state" country_name="recipient_country" county_name="recipient_county" state_value=$giftcert.recipient_state county_value=$giftcert.recipient_county}
          </td>
        </tr>

        <tr>
          <td class="data-name">{$lng.lbl_zip_code}</td>
          <td class="data-required">*</td>
          <td>
            {include file="main/zipcode.tpl" name="recipient_zipcode" id="recipient_zipcode" val=$giftcert.recipient_zipcode zip4=$giftcert.recipient_zip4}
          </td>
        </tr>

        <tr>
          <td class="data-name">{$lng.lbl_phone}</td>
          <td class="data-required">&nbsp;</td>
          <td><input type="text" name="recipient_phone" size="30" value="{$giftcert.recipient_phone|escape}" /></td>
        </tr>

      {/if}

      <tr id="preview_template"{if $giftcert.send_via ne "P"} style="display: none;"{/if}>
        <td class="data-name">{$lng.lbl_gc_template}</td>
        <td class="data-required">&nbsp;</td>
        <td>
          <select name="gc_template">
            {foreach from=$gc_templates item=gc_tpl}
              <option value="{$gc_tpl|escape}"{if $gc_tpl eq $giftcert.tpl_file or $giftcert.tpl_file eq "" and $gc_tpl eq $config.Gift_Certificates.default_giftcert_template} selected="selected"{/if}>{$gc_tpl}</option>
            {/foreach}
          </select>
          {include file="customer/buttons/button.tpl" button_title=$lng.lbl_preview href="javascript: formPreview();" style="link"}
        </td>
      </tr>

      <tr>
        <td colspan="2">&nbsp;</td>
        <td class="buttons-row buttons-auto-separator">

          {if $smarty.get.gcindex ne ""}

            {if $active_modules.Wishlist ne "" and $action eq "wl"}
              {include file="customer/buttons/gc_update.tpl" href="javascript: if (!check_gc_form()) return false; document.gccreate.mode.value='addgc2wl'; document.gccreate.submit();"}
            {else}
              {include file="customer/buttons/gc_update.tpl" type="input" adittional_button_class="main-button"}
            {/if}

          {else}

            {include file="customer/buttons/button.tpl" button_title=$lng.lbl_gc_add_to_cart type="input" additional_button_class="main-button"}

            {if $active_modules.Wishlist and $login}
              {include file="customer/buttons/add_to_wishlist.tpl" href="javascript: if (check_gc_form()) submitForm(document.gccreate, 'addgc2wl'); return false;"}
            {/if}

          {/if}
        </td>
      </tr>

    </table>
  </form>

{/capture}
{include file="customer/dialog.tpl" title=$lng.lbl_gift_certificate_details content=$smarty.capture.dialog}
