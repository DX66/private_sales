{*
$Id: giftcert.tpl,v 1.4.2.1 2010/12/15 09:44:40 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_gift_certificate}

{if ($config.Gift_Certificates.allow_customer_select_tpl eq "Y" and $usertype eq "C") or $is_admin_user}
{assign var="allow_tpl" value='1'}
{else}
{assign var="allow_tpl" value=''}
{/if}

{include file="check_email_script.tpl"}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
var txt_recipient_invalid = "{$lng.txt_recipient_invalid|wm_remove|escape:javascript|strip_tags|replace:"\n":" "|replace:"\r":" "}";
var txt_amount_invalid = "{$lng.txt_amount_invalid|wm_remove|escape:javascript|strip_tags|replace:"\n":" "|replace:"\r":" "}";
var txt_gc_enter_mail_address = "{$lng.txt_gc_enter_mail_address|wm_remove|escape:javascript|strip_tags|replace:"\n":" "|replace:"\r":" "}";

{if $usertype eq "C"}
var orig_mode = "gc2cart";
{else}
var orig_mode = "{$smarty.get.mode|wm_remove|escape:javascript}";
{/if}
var min_gc_amount = {$min_gc_amount|default:0};
var max_gc_amount = {$max_gc_amount|default:0};
var is_c_area = {if $usertype eq "C"}true{else}false{/if};
var enablePostMailGC = "{$config.Gift_Certificates.enablePostMailGC}";

{literal}
function check_gc_form() {
  if (document.gccreate.recipient.value == "") {
    document.gccreate.recipient.focus();
    alert (txt_recipient_invalid);
    return false;
  }
    if (document.gccreate.purchaser.value == "") {
        document.gccreate.purchaser.focus();
        alert (txt_gc_enter_mail_address);
        return false;
    }

  var num = convert_number(document.gccreate.amount.value);
  if (!check_is_number(document.gccreate.amount.value) || (is_c_area && (num < min_gc_amount || (max_gc_amount > 0 && num > max_gc_amount)))) {
    document.gccreate.amount.focus();
      alert (txt_amount_invalid);
    return false;
  }

  if (enablePostMailGC == 'Y') {
    if (document.gccreate.send_via[0].checked)
      if (document.gccreate.recipient_email.value == '') { 
        alert (txt_gc_enter_mail_address);
        document.gccreate.recipient_email.focus();
        return false;
      } else if (!checkEmailAddress(document.gccreate.recipient_email)) {
        document.gccreate.recipient_email.focus();
        return false;
      }
    if (document.gccreate.send_via[1].checked) {
      var was_error = false;

      if (document.gccreate.recipient_firstname.value == "") {
        was_error = true;
        document.gccreate.recipient_firstname.focus();
      }  
      else if (document.gccreate.recipient_lastname.value == "") {
        was_error = true;
        document.gccreate.recipient_lastname.focus();
      }  
      else if (document.gccreate.recipient_address.value == "") {
        was_error = true;
        document.gccreate.recipient_address.focus();
      }  
      else if (document.gccreate.recipient_city.value == "") {
        was_error = true;
        document.gccreate.recipient_city.focus();
      }  
      else if (document.gccreate.recipient_zipcode.value == "") {
        was_error = true;
        document.gccreate.recipient_zipcode.focus();
      }

      if (was_error) {
        alert (txt_gc_enter_mail_address);
        return false;
      }
    }

  } else if (document.gccreate.recipient_email.value == '') { 
    alert (txt_gc_enter_mail_address);
    document.gccreate.recipient_email.focus();
    return false;
  }
  else if (!checkEmailAddress(document.gccreate.recipient_email)) {
    document.gccreate.recipient_email.focus();
        return false;
  }

  return true;
}

function formSubmit() {
  if (check_gc_form()) {
    document.gccreate.mode.value = orig_mode;
    document.gccreate.target = ''
    document.gccreate.submit();
  }
}
//]]>
</script>
{/literal}

{if $config.Gift_Certificates.enablePostMailGC eq "Y" and $allow_tpl}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
{literal}
function switchPreview() {
  if (document.gccreate.send_via[0].checked) {
    document.getElementById('preview_button').style.display='none';
    document.getElementById('preview_template').style.display='none';
  }
  if (document.gccreate.send_via[1].checked) {
    document.getElementById('preview_button').style.display='';
    document.getElementById('preview_template').style.display='';
  }
}

function formPreview() {
  if (check_gc_form()) {
    document.gccreate.mode.value='preview';
    document.gccreate.target='_blank'
    document.gccreate.submit();
  }
}
{/literal}
//]]>
</script>
{else}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
{literal}
function switchPreview() {
  return false;
}
{/literal}
//]]>
</script>
{/if}

{include file="check_zipcode_js.tpl"}

<table cellpadding="5">
<tr>
  <td><img src="{$ImagesDir}/gift.gif" alt="" /></td>
  <td>{$lng.txt_gc_header}</td>
</tr>
</table>
{if $login and $usertype eq "C"}
<br />
{capture name=dialog}
{$lng.txt_gift_certificate_checking_msg}
<br /><br />
{if $smarty.get.gcid and $gc_array eq ""}
<font class="ErrorMessage">{$lng.err_gc_not_found}</font>
{/if}
<form action="giftcert.php">
<table>
<tr>
  <td>{$lng.lbl_gift_certificate}:</td>
  <td><input type="text" size="25" maxlength="16" name="gcid" value="{$smarty.get.gcid|escape:"html"}" /></td>
  <td><input type="submit" value="{$lng.lbl_submit|strip_tags:false|escape}" /></td>
</tr>
</table>
</form>
{if $gc_array}
<hr size="1" noshade="noshade" />
<table>
<tr>
  <td><b>{$lng.lbl_gc_id}:</b></td>
  <td>{$gc_array.gcid}</td>
</tr>
<tr>
  <td><b>{$lng.lbl_amount}:</b></td>
  <td>{currency value=$gc_array.amount}</td>
</tr>
<tr>
  <td><b>{$lng.lbl_remain}:</b></td>
  <td>{currency value=$gc_array.debit}</td>
</tr>
<tr>
  <td><b>{$lng.lbl_status}:</b></td>
  <td>
{if $gc_array.status eq "P"}{$lng.lbl_pending}
{elseif $gc_array.status eq "A"}{$lng.lbl_active}
{elseif $gc_array.status eq "B"}{$lng.lbl_blocked}
{elseif $gc_array.status eq "D"}{$lng.lbl_disabled}
{elseif $gc_array.status eq "E"}{$lng.lbl_expired}
{elseif $gc_array.status eq "U"}{$lng.lbl_used}
{/if}
  </td>
</tr>
</table>
{/if}
{/capture}
{include file="dialog.tpl" title=$lng.lbl_gift_certificate_checking content=$smarty.capture.dialog extra='width="100%"'}
{/if}
<br />
{capture name=dialog}
{if $amount_error}
<p class="ErrorMessage">{$lng.txt_amount_invalid}</p>
{/if}
{if $usertype eq "C"}

<form name="gccreate" action="giftcert.php" method="post" onsubmit="javascript: return check_gc_form()">
<input type="hidden" name="gcindex" value="{$smarty.get.gcindex|escape:"html"}" />
<input type="hidden" name="mode" value="gc2cart" />

{else}

<form name="gccreate" action="giftcerts.php" method="post" onsubmit="javascript: return check_gc_form()">
<input type="hidden" name="mode" value="{$smarty.get.mode|escape:"html"}" />
<input type="hidden" name="gcid" value="{$smarty.get.gcid|escape:"html"}" />

{/if}
<table width="100%" cellpadding="0">
<tr>
<td colspan="3"><b><font class="ProductDetailsTitle">1. {$lng.lbl_gc_whom_sending}<br /></font></b>
{$lng.lbl_gc_whom_sending_subtitle}<br />
</td>
</tr>

<tr>
<td align="right">{$lng.lbl_from}</td>
<td><font class="Star">*</font></td>
<td align="left"><input type="text" name="purchaser" size="30" value="{if $usertype eq "A" and $smarty.get.mode eq 'add_gc'}{$config.Company.company_name|escape}{elseif $giftcert.purchaser}{$giftcert.purchaser|escape:"html"}{else}{if $userinfo.firstname ne ''}{$userinfo.firstname|escape} {/if}{$userinfo.lastname|escape}{/if}" /></td>
</tr>

<tr>
<td align="right">{$lng.lbl_to}</td>
<td><font class="Star">*</font></td>
<td align="left"><input type="text" name="recipient" size="30" value="{$giftcert.recipient|escape:"html"}" /> </td>
</tr>

<tr>
<td colspan="3"><b><font class="ProductDetailsTitle"><br />2. {$lng.lbl_gc_add_message}<br /></font></b>
{$lng.lbl_gc_add_message_subtitle}<br />
</td>
</tr>

<tr>
<td align="right">{$lng.lbl_message}</td>
<td><font class="Star"></font></td>
<td align="left"><textarea name="message" rows="8" cols="50">{$giftcert.message}</textarea></td>
</tr>

<tr>
<td colspan="3"><b><font class="ProductDetailsTitle"><br />3. {$lng.lbl_gc_choose_amount}<br /></font></b>
{$lng.lbl_gc_choose_amount_subtitle}<br />
</td>
</tr>

<tr>
<td align="right">{$config.General.currency_symbol}</td>
<td><font class="Star">*</font></td>
<td align="left"><input type="text" name="amount" size="10" maxlength="9" value="{$giftcert.amount|formatprice}" />
{if $usertype eq "C" and ($min_gc_amount gt 0 or $max_gc_amount gt 0)}{$lng.lbl_gc_amount_msg} {if $min_gc_amount gt 0}{$lng.lbl_gc_from} {$config.General.currency_symbol}{$min_gc_amount|formatprice}{/if} {if $max_gc_amount gt 0}{$lng.lbl_gc_through} {$config.General.currency_symbol}{$max_gc_amount|formatprice}{/if}{/if}
</td></tr>

<tr><td colspan="3">&nbsp;</td></tr>

<tr><td colspan="3"><b><font class="ProductDetailsTitle"><br />4. {$lng.lbl_gc_choose_delivery_method}<br /><br /></font></b></td>
</tr>

<tr><td colspan="3">
<table cellspacing="0" cellpadding="0"><tr>
{if $config.Gift_Certificates.enablePostMailGC eq "Y"}
<td align="right"><input id="gc_send_e" type="radio" name="send_via" value="E" onclick="switchPreview();"{if $giftcert.send_via ne "P"} checked="checked"{/if} /></td>
{else}
<input type="hidden" name="send_via" value="E" />
{/if}
<td><label for="gc_send_e"><b>{$lng.lbl_gc_send_via_email}</b></label></td>
</tr></table></td>
</tr>

<tr><td colspan="3">{$lng.lbl_gc_enter_email}<br /><br /></td></tr>

<tr>
<td nowrap="nowrap" align="right">{$lng.lbl_email}</td>
<td><font class="Star">*</font></td>
<td align="left"><input type="text" name="recipient_email" size="30" value="{$giftcert.recipient_email|escape}" /></td>
</tr>

{if $config.Gift_Certificates.enablePostMailGC eq "Y"}

<tr><td colspan="3">&nbsp;</td></tr>

<tr><td colspan="3"><table cellspacing="0" cellpadding="0" width="100%"><tr><td bgcolor="#CCCCCC"><img src="{$ImagesDir}/null.gif" class="Spc" alt="" /><br /></td></tr></table></td></tr>

<tr><td colspan="3">&nbsp;</td></tr>

<tr><td colspan="3"><table cellspacing="0" cellpadding="0"><tr>
<td align="right"><input id="gc_send_p" type="radio" name="send_via" value="P" onclick="switchPreview();"{if $giftcert.send_via eq "P"} checked="checked"{/if} /></td>
<td><label for="gc_send_p"><b>{$lng.lbl_gc_send_via_postal_mail}</b></label></td>
</tr></table></td>
</tr>

<tr><td colspan="3">{$lng.txt_gc_enter_postal_mail}<br /><br /></td></tr>

<tr>
<td nowrap="nowrap" align="right">{$lng.lbl_first_name}</td>
<td><font class="Star">*</font></td>
<td align="left"><input type="text" name="recipient_firstname" size="30" value="{$giftcert.recipient_firstname|escape}" /></td>
</tr>

<tr>
<td nowrap="nowrap" align="right">{$lng.lbl_last_name}</td>
<td><font class="Star">*</font></td>
<td align="left"><input type="text" name="recipient_lastname" size="30" value="{$giftcert.recipient_lastname|escape}" /></td>
</tr>

<tr>
<td nowrap="nowrap" align="right">{$lng.lbl_address}</td>
<td><font class="Star">*</font></td>
<td align="left"><input type="text" name="recipient_address" size="40" value="{$giftcert.recipient_address|escape}" /></td>
</tr>

<tr>
<td nowrap="nowrap" align="right">{$lng.lbl_city}</td>
<td><font class="Star">*</font></td>
<td align="left"><input type="text" name="recipient_city" size="30" value="{$giftcert.recipient_city|escape}" /></td>
</tr>

{if $config.General.use_counties eq "Y"}
<tr>
<td nowrap="nowrap" align="right">{$lng.lbl_county}</td>
<td><font class="Star">*</font></td>
<td align="left">
{include file="main/counties.tpl" counties=$counties name="recipient_county" default=$giftcert.recipient_county}
</td>
</tr>
{/if}

<tr>
<td nowrap="nowrap" align="right">{$lng.lbl_state}</td>
<td><font class="Star">*</font></td>
<td align="left">
{include file="main/states.tpl" states=$states name="recipient_state" default=$giftcert.recipient_state default_country=$giftcert.recipient_country}
</td></tr>

<tr>
<td nowrap="nowrap" align="right">{$lng.lbl_country}</td>
<td><font class="Star">*</font></td>
<td align="left">
<select id="recipient_country" name="recipient_country" size="1" onchange="javascript: check_zip_code_field(this, document.gccreate.recipient_zipcode);">
{section name=country_idx loop=$countries}
<option value="{$countries[country_idx].country_code|escape}"{if $giftcert.recipient_country eq $countries[country_idx].country_code} selected="selected"{elseif $countries[country_idx].country_code eq $config.General.default_country and $giftcert.recipient_country eq ""} selected="selected"{elseif $countries[country_idx].country_code eq $userinfo.b_country and $giftcert.recipient_country eq ""} selected="selected"{/if}>{$countries[country_idx].country}</option>
{/section}
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
<td nowrap="nowrap" align="right">{$lng.lbl_zip_code}</td>
<td><font class="Star">*</font></td>
<td align="left">
  {include file="main/zipcode.tpl" name="recipient_zipcode" id="recipient_zipcode" val=$giftcert.recipient_zipcode zip4=$giftcert.recipient_zip4}
</td>
</tr>

<tr>
<td nowrap="nowrap" align="right">{$lng.lbl_phone}</td>
<td></td>
<td align="left"><input type="text" name="recipient_phone" size="30" value="{$giftcert.recipient_phone|escape}" /></td>
</tr>

{/if}

{if $allow_tpl}
<tr id="preview_template" {if $giftcert.send_via ne "P"}style="display: none;"{else}style="display: '';"{/if}>
<td nowrap="nowrap" align="right">{$lng.lbl_gc_template}</td>
<td>&nbsp;</td>
<td align="left">
<select name="gc_template">
{foreach from=$gc_templates item=gc_tpl}
<option value="{$gc_tpl|escape}"{if $gc_tpl eq $giftcert.tpl_file or $giftcert.tpl_file eq "" and $gc_tpl eq $config.Gift_Certificates.default_giftcert_template} selected="selected"{/if}>{$gc_tpl}</option>
{/foreach}
</select>
</td>
</tr>

<tr><td colspan="3">&nbsp;</td></tr>
{/if}{* admin *}

</table>
</form>
<br />
<center>

<table cellspacing="0" cellpadding="0">
<tr>
{if $allow_tpl}
<td id="preview_button" {if $giftcert.send_via ne "P"}style="display: none;"{/if}><input type="button" value="{$lng.lbl_preview|escape}" onclick="javascript: formPreview();" /></td>
<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
{/if}

<td class="main-button">
  {if $is_admin_user and $smarty.get.mode eq "modify_gc"}
    {if $smarty.get.gcindex ne "" or ($usertype eq "A" and $smarty.get.mode eq "modify_gc")}
      <input type="button" value="{$lng.lbl_gc_update|escape}" onclick="javascript: formSubmit();" />
    {else}
      <input type="button" value="{$lng.lbl_gc_add_to_cart|escape}" onclick="javascript: formSubmit();" />
    {/if}
  {else}
    <input type="button" value="{$lng.lbl_gc_create|escape}" onclick="javascript: formSubmit();" />
  {/if}
</td>

</tr>
</table>
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_gift_certificate_details content=$smarty.capture.dialog extra='width="100%"'}
