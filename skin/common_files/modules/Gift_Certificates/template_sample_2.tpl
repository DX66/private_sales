{*
$Id: template_sample_2.tpl,v 1.1.2.1 2010/12/15 09:44:40 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{assign var="TplImages" value="`$SkinDir`/modules/Gift_Certificates/images"}

<table width="650" cellpadding="0" cellspacing="0">
<tr>
  <td>
    <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td><img src="{$TplImages}/left_top.gif" alt="" /></td>
      <td width="100%" valign="top"><img src="{$TplImages}/top_border.gif" width="100%" height="14" alt="" /></td>
      <td><img src="{$TplImages}/right_top.gif" alt="" /></td>
    </tr>
    </table>
  
    <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td><img src="{$TplImages}/left_border.gif" width="16" height="700" alt="" /></td>
      <td><img src="{$TplImages}/spacer.gif" width="34" height="1" alt="" /></td>
      <td width="100%" valign="top">
        
        <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td width="100%"><img src="{$TplImages}/company_logo_2.gif" width="103" height="31" alt="" /></td>
        </tr>
        </table><br />

        <center>
        <table cellpadding="0" cellspacing="0">
        <tr>
          <td valign="bottom">
            <img src="{$TplImages}/spacer.gif" width="1" height="200" alt="" /><br />
            <img src="{$TplImages}/theme_2.jpg" alt="" />
          </td>
          <td valign="top">
            <span class="gcHeader">{$lng.lbl_gift_certificate}</span>
            <br /><br /><br /><br /><br />

            <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td nowrap="nowrap" class="gcInfo">{$lng.lbl_gc_id}:</td>
              <td class="gcInfo">&nbsp;&nbsp;</td>
              <td class="gcInfo">{$giftcert.gcid|default:$lng.lbl_gcid_notdefined}</td>
            </tr>
            <tr>
              <td nowrap="nowrap" class="gcInfo">{$lng.lbl_from}:</td>
              <td class="gcInfo">&nbsp;&nbsp;</td>
              <td class="gcInfo">{$giftcert.purchaser|escape:"html"}</td>
            </tr>
            <tr>
              <td nowrap="nowrap" class="gcInfo">{$lng.lbl_to}:</td>
              <td class="gcInfo">&nbsp;&nbsp;</td>
              <td class="gcInfo">{$giftcert.recipient|escape:"html"}</td>
            </tr>
            <tr>
              <td nowrap="nowrap" class="gcInfo">{$lng.lbl_amount}:</td>
              <td class="gcInfo">&nbsp;&nbsp;</td>
              <td class="gcInfo">{currency value=$giftcert.amount}</td>
            </tr>
            <tr>
              <td class="gcInfo">&nbsp;&nbsp;</td>
              <td class="gcInfo">&nbsp;&nbsp;</td>
              <td class="gcInfo">&nbsp;&nbsp;</td>
            </tr>
            <tr>
              <td nowrap="nowrap" class="gcInfo">{$lng.lbl_message}:</td>
              <td class="gcInfo">&nbsp;&nbsp;</td>
              <td class="gcInfoBorder" width="123">
{$giftcert.message|escape:"html"|replace:"\n":"<br />"}
              </td>
            </tr>
            </table>
            
            <br /><br />
            <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td class="gcPostalHeader">&nbsp;{$lng.lbl_postal_address}</td>
            </tr>
            <tr>
              <td><img src="{$TplImages}/spacer.gif" width="1" height="4" alt="" /></td>
            </tr>
            <tr>
              <td><img src="{$TplImages}/delim_2.gif" width="100%" height="4" alt="" /></td>
            </tr>
            </table>
            
            <table width="90%" cellpadding="0" cellspacing="0" align="center">
            <tr>
              <td class="gcPostalInfo">&nbsp;</td>
              <td class="gcPostalInfo">&nbsp;</td>
            </tr>
            <tr>
              <td class="gcPostalInfo">{$lng.lbl_first_name}:</td>
              <td class="gcPostalInfo">{$giftcert.recipient_firstname|escape:"html"}</td>
            </tr>
            <tr>
              <td class="gcPostalInfo">{$lng.lbl_last_name}:</td>
              <td class="gcPostalInfo">{$giftcert.recipient_lastname|escape:"html"}</td>
            </tr>
            <tr>
              <td class="gcPostalInfo">{$lng.lbl_address}:</td>
              <td class="gcPostalInfo">{$giftcert.recipient_address|escape:"html"}</td>
            </tr>
            <tr>
              <td class="gcPostalInfo">{$lng.lbl_city}:</td>
              <td class="gcPostalInfo">{$giftcert.recipient_city|escape:"html"}</td>
            </tr>
            <tr>
              <td class="gcPostalInfo">{$lng.lbl_zip_code}:</td>
              <td class="gcPostalInfo">
                {include file="main/zipcode.tpl" val=$giftcert.recipient_zipcode zip4=$giftcert.recipient_zip4 static=true}
              </td>
            </tr>
{if $config.General.use_counties eq "Y"}
            <tr>
              <td class="gcPostalInfo">{$lng.lbl_county}:</td>
              <td class="gcPostalInfo">{$giftcert.recipient_countyname}</td>
            </tr>
{/if}
            <tr>
              <td class="gcPostalInfo">{$lng.lbl_state}:</td>
              <td class="gcPostalInfo">{$giftcert.recipient_statename}</td>
            </tr>
            <tr>
              <td class="gcPostalInfo">{$lng.lbl_country}:</td>
              <td class="gcPostalInfo">{$giftcert.recipient_countryname}</td>
            </tr>
            <tr>
              <td class="gcPostalInfo">{$lng.lbl_phone}:</td>
              <td class="gcPostalInfo">{$giftcert.recipient_phone|escape:"html"}</td>
            </tr>
            </table>
          </td>
        </tr>
        </table>
        </center>
      </td>
      <td><img src="{$TplImages}/spacer.gif" width="34" height="1" alt="" /></td>
      <td align="right"><img src="{$TplImages}/right_border.gif" width="20" height="700" alt="" /></td>
    </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td><img src="{$TplImages}/left_bottom.gif" alt="" /></td>
      <td valign="bottom" width="100%"><img src="{$TplImages}/bottom_border.gif" height="25" width="100%" alt="" /></td>
      <td><img src="{$TplImages}/curl.gif" alt="" /></td>
    </tr>
    </table>
  
  </td>
</tr>
</table>
