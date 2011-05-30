{*
$Id: import_options.tpl,v 1.3 2010/06/08 06:17:39 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table cellpadding="0" cellspacing="0" width="100%" {if $smarty.get.open_options ne 'Y'}style="display: none;" {/if}id="box5">
<tr>
  <td>

<br /><br />

{include file="main/subheader.tpl" title=$lng.lbl_import_options}
<table cellspacing="0" cellpadding="0" width="100%">
<tr>
  <td width="100%">{$lng.txt_import_options_text}</td>
  <td>&nbsp;&nbsp;&nbsp;</td>
  <td valign="top"><a href="javascript:void(0);" onclick="javascript: reset_form('importdata_form', importdata_form_def); change_all(false);">{$lng.lbl_reset}</a></td>
</tr>
</table>

<br /><br />

{if $import_options ne ''}
<table cellpadding="5" cellspacing="1" width="100%">
{foreach from=$import_options item=v}
<tr>
  <td>{include file=$v}</td>
</tr>
{/foreach}
</table>
{/if}

<table cellpadding="5" cellspacing="1" width="100%">
<tr>
  <td>
<br /><br />

{$lng.txt_import_data_types}

<br /><br />

{if $import_specification ne ''}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
var checkboxes_form = 'importdata_form';
var checkboxes = [{foreach from=$import_specification item=v key=k}'drop[{$k}]',{/foreach}''];
 
//]]>
</script>
<script type="text/javascript" src="{$SkinDir}/js/change_all_checkboxes.js"></script>
<div style="line-height:170%"><a href="javascript:change_all(true);">{$lng.lbl_check_all}</a> / <a href="javascript:change_all(false);">{$lng.lbl_uncheck_all}</a></div>
{/if}

<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
  <td width="5%" nowrap="nowrap">{$lng.lbl_drop}</td>
  <td width="15%">{$lng.lbl_data_type}</td>
  <td width="80%">{$lng.lbl_columns}</td>
</tr>

{foreach key=section_name item=section_data from=$import_specification}
{if not $section_data.no_import}
{cycle values=" , class='TableSubHead'" assign="tr_class"}
<tr{$tr_class}>
  <td align="center"{if $section_data.import_note} rowspan="2"{/if}><input type="checkbox" id="drop_{$section_name}" name="drop[{$section_name}]" value="Y" /></td>
  <td><label for="drop_{$section_name}">{$section_name}</label></td>
  <td>
{foreach key=col_name item=col_data from=$section_data.columns}
{if $col_data.required}
<strong class="upper">{$col_name}</strong>
{else}
<span class="upper">{$col_name}</span>
{/if}
&nbsp;
{/foreach}
  </td>
</tr>
{if $section_data.import_note}
<tr{$tr_class}>
  <td colspan="2" style="padding-left: 20px;"><b>{$lng.lbl_note}:</b> {$section_data.import_note}</td>
</tr>
{/if}
{/if}
{/foreach}

</table>

  </td>
</tr>
</table>

  </td>
</tr>
</table>
