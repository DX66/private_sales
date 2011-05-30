{*
$Id: ie_delimiter.tpl,v 1.1 2010/05/21 08:32:53 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $saved_delimiter eq ''}{assign var="saved_delimiter" value=$smarty.get.delimiter}{/if}
<select name="{$field_name|default:"delimiter"}">
  <option value=";"{if $saved_delimiter eq ";"} selected="selected"{/if}>{$lng.lbl_semicolon}</option>
  <option value=","{if $saved_delimiter eq ","} selected="selected"{/if}>{$lng.lbl_comma}</option>
  <option value="tab"{if $saved_delimiter eq "\t"} selected="selected"{/if}>{$lng.lbl_tab}</option>
</select>
