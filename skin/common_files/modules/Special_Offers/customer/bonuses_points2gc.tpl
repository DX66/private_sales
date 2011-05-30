{*
$Id: bonuses_points2gc.tpl,v 1.3.2.3 2010/12/15 11:57:06 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="check_email_script.tpl"}
<script type="text/javascript">
//<![CDATA[
var offers_bp_rate = {$config.Special_Offers.offers_bp_rate|default:0};
var offers_bp_min = {$config.Special_Offers.offers_bp_min|default:0};
var offers_bp_max = {$bonus.points|default:0};
var msg_not_enough = "{$lng.lbl_sp_bpconv_not_enough|wm_remove|escape:javascript}";
var msg_too_low = "{$lng.lbl_sp_bpconv_too_low|wm_remove|escape:javascript}";
var txt_recipient_invalid = "{$lng.txt_recipient_invalid|wm_remove|escape:javascript|strip_tags}";
var txt_amount_invalid = "{$lng.txt_amount_invalid|wm_remove|escape:javascript|strip_tags}";
//]]>
</script>

<script type="text/javascript" src="{$SkinDir}/modules/Special_Offers/customer/bonuses_points2gc.js"></script>
{include file="check_zipcode_js.tpl"}

<div class="giftcert-header">
  <img src="{$ImagesDir}/spacer.gif" alt="" />
  {$lng.txt_gc_header}
</div>
<div class="clearing"></div>

{include file="customer/subheader.tpl" title=$lng.lbl_gift_certificate_details}

<form name="gccreate" action="bonuses.php" method="post" onsubmit="javascript: return check_gc_form();">
  <input type="hidden" name="mode" value="points" />

  <table cellspacing="1" class="data-table giftcert-table">
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
      <td>
        <input type="text" name="recipient" size="30" value="{$giftcert.recipient|escape:"html"}" />
      </td>
    </tr>

    <tr>
      <td colspan="3">
        <div class="giftcert-title">2. {$lng.lbl_gc_add_message}</div>
        {$lng.lbl_gc_add_message_subtitle}
      </td>
    </tr>

    <tr>
      <td class="data-name">{$lng.lbl_message}</td>
      <td class="data-required">&nbsp;</td>
      <td>
        <textarea name="message" rows="8" cols="50">{$giftcert.message}</textarea>
      </td>
    </tr>

    <tr>
      <td colspan="3">
        <div class="giftcert-title">3. {$lng.lbl_gc_choose_amount}</div>
      </td>
    </tr>

    <tr>
      <td colspan="3">
        {capture name="rate"}
          <strong>{currency value=$config.Special_Offers.offers_bp_rate}</strong> {alter_currency value=$config.Special_Offers.offers_bp_rate}
        {/capture}
        {$lng.txt_sp_bpconv_details|substitute:"points":$bonus.points:"min":$config.Special_Offers.offers_bp_min:"rate":$smarty.capture.rate}
      </td>
    </tr>

    <tr>
      <td class="data-name">{$lng.lbl_sp_customer_bonus_points}:</td>
      <td class="data-required">*</td>
      <td>
        <input type="text" id="bp_amount" name="amount" size="6" maxlength="5" value="{$giftcert.amount}" onchange="conv_amount()" />
        x
        <strong>{$config.Special_Offers.offers_bp_rate}</strong>
        =
        {$config.General.currency_symbol}<strong id='converted_amount'>{multi x=$giftcert.amount y=$config.Special_Offers.offers_bp_rate format="%.2f"}</strong>
        &nbsp;&nbsp;
        <input type="button" value="{$lng.lbl_sp_recalculate|strip_tags:false|escape}" onclick="javascript: conv_amount();" />
      </td>
    </tr>
 
    <tr>
      <td colspan="2">&nbsp;</td>
      <td id="bp_conv_err" class="error-message"></td>
    </tr>

    <tr>
      <td colspan="3" class="giftcert-title">4. {$lng.lbl_email_address}</td>
    </tr>

    <tr>
      <td colspan="3" class="giftcert-subtitle">{$lng.lbl_gc_enter_email}</td>
    </tr>

    <tr>
      <td class="data-name">{$lng.lbl_email}</td>
      <td class="data-required">*</td>
      <td><input type="text" name="recipient_email" size="30" value="{$giftcert.recipient_email|escape}" /></td>
    </tr>

  </table>

  <div class="center">
    <div class="halign-center button-row">
      {include file="customer/buttons/button.tpl" button_title=$lng.lbl_gc_create type="input"}
    </div>
  </div>

</form>
