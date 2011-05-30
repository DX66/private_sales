{*
$Id: australia_post.tpl,v 1.3 2010/06/08 06:17:40 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
var lbl_required_field_is_empty = "{$lng.lbl_required_field_is_empty|strip_tags|wm_remove|escape:javascript}";
var lbl_tracking_number = "{$lng.lbl_tracking_number|strip_tags|wm_remove|escape:javascript}";
var lbl_field_format_is_invalid = "{$lng.lbl_field_format_is_invalid|wm_remove|escape:javascript}";

{literal}
function trim(str) {
  return str.replace(/^\s*|\s*$/g,"");
}

function contains(inString,inChar) {
  if(inString.indexOf(inChar) > -1)
        return true;
    else
        return false;
}

function validateSubmit(frm) {

  if (!frm) 
    return false;

  if(trim(frm.visibleField.value) == "" || trim(frm.visibleField.value) == null) {
    alert(substitute(lbl_required_field_is_empty, 'field', lbl_tracking_number));
    return false;
  }
  else if(contains(frm.visibleField.value,"*") || contains(frm.visibleField.value,"%")) {
    alert(substitute(lbl_field_format_is_invalid, 'field', lbl_tracking_number));
    return false;
  }
  else {
    if (frm.articleConsChoice.value == 'Article') {
      frm.mastershipmentnumber.value = '';
      frm.shipmentnumber.value = frm.visibleField.value;
    }
    else if (frm.articleConsChoice.value == 'Consignment') {
      frm.shipmentnumber.value = '';
      frm.mastershipmentnumber.value = frm.visibleField.value;
    }

  return true;
  }
}
{/literal}
//]]>
</script>

<form action="http://auspost.com.au/track/display.asp" method="get" name="getTrackNum" id="getTrackNum" target="_blank" onsubmit="javascript: return validateSubmit(this);">
  <input name="username" value="genpub" type="hidden" />
  <input name="password" value="genpub" type="hidden" />
  <input name="LOGOFF_URL" value="/tower/" type="hidden" />
  <input name="PASSTHRU_KEY" value="SHIPMENT_SEARCH" type="hidden" />
  <input name="mastershipmentnumber"  value="" type="hidden" />
  <input name="shipmentnumber" value="{$order.tracking|escape}" type="hidden" />
  <input name="SHIPMENT_FLEX_FIELD_9" value="DOMESTIC" type="hidden" />
  <input name="articleConsChoice" value="Article" type="hidden" />

  <input type="hidden" name="id" id="id" value="{$order.tracking|escape}" />
  <input type="hidden" name="type" id="type" value="article" />


  
  <input name="visibleField" value="{$order.tracking|escape}" type="hidden" />

  <table cellspacing="0" class="data-table">
  <tr>
    <td><input name="radio1" type="radio" value="Article" onclick="document.getTrackNum.articleConsChoice.value='Article';document.getTrackNum.type.value='article'" checked/>{$lng.lbl_article}</td>
    <td><input name="radio1" type="radio" value="Consignment" onclick="document.getTrackNum.articleConsChoice.value='Consignment';document.getTrackNum.type.value='consignment';"/>{$lng.lbl_consignment}</td>
    <td><input type="submit" value="{$lng.lbl_track_it|strip_tags:false|escape}" /></td>
  </tr>
  </table>
  {$lng.txt_apost_redirection}
</form>
