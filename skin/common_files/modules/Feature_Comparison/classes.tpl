{*
$Id: classes.tpl,v 1.4 2010/07/19 07:09:21 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_product_feature_classes}

{$lng.txt_product_classes_note}

<script type="text/javascript" src="{$SkinDir}/js/popup_image_selection.js"></script>

{if $class eq '' and $smarty.get.new ne 'Y' and $classes}

<form action="classes.php" method="post" name="classesform">
<input type="hidden" name="mode" value="update" />

<script type="text/javascript">
//<![CDATA[
checkboxes_form = 'classesform';
checkboxes = new Array({foreach from=$classes item=v key=k}{if $k gt 0},{/if}'ids[{$v.fclassid}]'{/foreach});
//]]>
</script>

<script type="text/javascript" src="{$SkinDir}/js/change_all_checkboxes.js"></script>

<div style="line-height:170%"><a href="javascript:change_all(true);">{$lng.lbl_check_all}</a> / <a href="javascript:change_all(false);">{$lng.lbl_uncheck_all}</a></div>
<table cellspacing="1" cellpadding="3">
<tr class="TableHead">
  <td width="15">&nbsp;</td>
  <td>{$lng.lbl_feature_class}</td>
  <td>{$lng.lbl_enabled}</td>
  <td>{$lng.lbl_orderby}</td>
{if ($is_admin_user and $single_mode eq '') and ($active_modules.Simple_Mode eq '' or $single_mode eq '')}
  <td>{$lng.lbl_provider}</td>
{/if}
</tr>
{foreach from=$classes item=v}
<tr {cycle name=c1 values='class="TableSubHead",'}>
  <td>{if $v.other_provider eq 0}<input type="checkbox" name="ids[{$v.fclassid}]" value="Y" />{else}&nbsp;{/if}</td>
  <td>{if $class.fclassid eq $v.fclassid}<b>{$v.class}</b>{else}<a href="classes.php?fclassid={$v.fclassid}">{$v.class}</a>{/if}</td>
  <td align="center"><select name="update[{$v.fclassid}][avail]">
  <option value="Y"{if $v.avail eq 'Y'} selected="selected"{/if}>{$lng.lbl_yes}</option>
  <option value=""{if $v.avail ne 'Y'} selected="selected"{/if}>{$lng.lbl_no}</option>
  </select></td>
  <td align="center"><input type="text" size="3" name="update[{$v.fclassid}][orderby]" value="{$v.orderby}" /></td>
{if ($is_admin_user and $single_mode eq '') and ($active_modules.Simple_Mode eq '' or $single_mode eq '')}
  <td>{$v.provider_login|default:$login}</td>
{/if}
</tr>
{foreachelse}
<tr>
  <td colspan="5" align="center">{$lng.lbl_feature_class_not_found}</td>
</tr>
{/foreach}
{if $classes ne ''}
<tr>
  <td colspan="3" class="main-button">
    <input class="big-main-button" type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
  </td>
  <td colspan="2" align="right">
    <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('ids', 'ig'))) {ldelim}document.classesform.mode.value = 'delete'; document.classesform.submit();{rdelim}" />
  </td>
</tr>
{/if}
</table>
</form>

{else}

{capture name=dialog}
<form action="classes.php" method="post" name="classform">
<input type="hidden" name="mode" value="add" />
<input type="hidden" name="add[fclassid]" value="{$class.fclassid}" />
<input type="hidden" name="fclassid" value="{$class.fclassid}" />

{include file="main/language_selector.tpl" script="classes.php?fclassid=`$class.fclassid`&amp;"}
<br />

<table cellspacing="1" cellpadding="2">
<tr>
  <td>{$lng.lbl_image}</td>
  <td>&nbsp;</td>
  {if $class ne ''}{assign var="button" value=$lng.lbl_save}{else}{assign var="button" value=$lng.lbl_add}{/if}
  {if $class.is_image ne 'Y'}{assign var="no_delete" value="Y"}{/if}
  <td>{include file="main/edit_image.tpl" type="F" id=$class.fclassid delete_url="classes.php?mode=delete_image&amp;fclassid=`$class.fclassid`" button_name=$button no_delete=$no_delete}</td>
</tr>
<tr>
  <td>{$lng.lbl_feature_class}</td>
  <td width="5"><font class="Star">*</font></td>
  <td><input type="text" name="add[class]" value="{$class.class|escape}" /></td>
</tr>
<tr>
  <td>{$lng.lbl_enabled}</td>
  <td>&nbsp;</td>
  <td><select name="add[avail]">
  <option value="Y"{if $class.avail eq 'Y' or $class.fclassid gt 0} selected="selected"{/if}>{$lng.lbl_yes}</option>
  <option value=""{if $class.avail ne 'Y' and $class.fclassid gt 0} selected="selected"{/if}>{$lng.lbl_no}</option>
  </select></td>
</tr>
<tr>
  <td>{$lng.lbl_orderby}</td>
  <td>&nbsp;</td>
  <td><input type="text" name="add[orderby]" size="3" value="{$class.orderby}" /></td>
</tr>
<tr>
    <td colspan="2">&nbsp;</td>
    <td><input type="submit" name="is_save" value="{if $class ne ''}{$lng.lbl_save|strip_tags:false|escape}{else}{$lng.lbl_add|strip_tags:false|escape}{/if}" /></td>
</tr>
</table>

{if $class ne ''}
<br />
{include file="main/subheader.tpl" title=$lng.lbl_feature_class_options class="black"}
<table cellspacing="1" cellpadding="2" width="100%">
<tr class="TableHead">
  <td>&nbsp;</td>
  <td>{$lng.lbl_option}</td>
  <td>{$lng.lbl_option_hint}</td>
  <td>{$lng.lbl_option_type_format}</td>
  <td>{$lng.lbl_enabled}</td>
  <td>{$lng.lbl_show_in_search}</td>
  <td>{$lng.lbl_orderby}</td>
  <td>{$lng.lbl_variants}</td>
</tr>
{foreach from=$class.options item=v}
<tr {cycle name=c2 values='class="TableSubHead",'}>
  <td valign="top"><input type="checkbox" name="ids[{$v.foptionid}]" value="Y" /></td>
  <td valign="top"><input type="text" name="options[{$v.foptionid}][option_name]" value="{$v.option_name|escape}" size="10" /></td>
  <td valign="top"><input type="text" name="options[{$v.foptionid}][option_hint]" value="{$v.option_hint|escape}" size="10" /></td>
  <td valign="top"><select name="options[{$v.foptionid}][option_type]">
  {foreach from=$fc_option_types key=ot item=ol}
  <option value="{$ot}"{if $ot eq $v.option_type} selected="selected"{/if}>{$ol}</option>
  {/foreach}
  </select>
  <br />
  {if $formats[$v.option_type] ne ''}
  <select name="options[{$v.foptionid}][format]">
  {foreach from=$formats[$v.option_type] item=fv key=fk}
  <option value="{$fk}"{if $fk eq $v.format} selected="selected"{/if}>{$fv}</option>
  {/foreach}
  </select>
  {else}
  <center>-</center>
  {/if}
  </td>
  <td valign="top" align="center"><select name="options[{$v.foptionid}][avail]">
  <option value="Y"{if $v.avail eq 'Y'} selected="selected"{/if}>{$lng.lbl_yes}</option>
  <option value=""{if $v.avail ne 'Y'} selected="selected"{/if}>{$lng.lbl_no}</option>
  </select></td>
  <td valign="top" align="center"><select name="options[{$v.foptionid}][show_in_search]">
  <option value="Y"{if $v.show_in_search eq 'Y'} selected="selected"{/if}>{$lng.lbl_yes}</option>
  <option value=""{if $v.show_in_search ne 'Y'} selected="selected"{/if}>{$lng.lbl_no}</option>
  </select></td>
  <td valign="top" align="center"><input type="text" name="options[{$v.foptionid}][orderby]" size="3" value="{$v.orderby}" /></td>
  <td valign="top">{if ($v.option_type eq 'S' or $v.option_type eq 'M')}
  {if $foptionid ne $v.foptionid}<a href="classes.php?mode=modify_variants&amp;fclassid={$class.fclassid}&amp;foptionid={$v.foptionid}&amp;class_lng={$class_lng}#foption{$v.foptionid}">{/if}
  {if $v.variants ne ''}
  {foreach from=$v.variants item=vv}
    {$vv.variant_name}<br />
  {/foreach}
  {else}
  {$lng.lbl_variants}
  {/if}
  {if $foptionid ne $v.foptionid}</a>{/if}
  {elseif $foptionid ne $v.foptionid}<center>-</center>{/if}</td>
</tr>
{if $v.foptionid eq $foptionid and $foptionid ne ''}
<input type="hidden" name="_foptionid" value="{$foptionid}" />
<tr>
  <td colspan="8">
  <a name="foption{$foptionid}"></a>
  <table cellspacing="0" cellpadding="1">
  <tr>
    <td><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
  </tr>
  <tr>
    <td><img src="{$ImagesDir}/spacer.gif" width="100" height="1" alt="" /></td>
    <td bgcolor="#CCCCCC" width="100%">
    <table cellspacing="1" cellpadding="2" bgcolor="#ffffff" width="100%">
    <tr>
      <td width="3" height="3"><img src="{$ImagesDir}/spacer.gif" width="3" height="3" alt="" /></td>
    </tr>
    <tr>
      <td width="3"><img src="{$ImagesDir}/spacer.gif" width="3" height="1" alt="" /></td>
      <td>{include file="main/subheader.tpl" title=$lng.lbl_option_variants class="black"}
      <table cellspacing="1" cellpadding="2" width="100%">
  {if $v.variants ne ''}
  {foreach from=$v.variants item=vv}
      <tr>
        <td width="15"><input type="checkbox" name="vids[{$vv.fvariantid}]" value="Y" /></td>
        <td width="15"><input type="text" name="variants[{$vv.fvariantid}][variant_name]" value="{$vv.variant_name|escape}" size="70" /></td>
        <td><input type="text" name="variants[{$vv.fvariantid}][orderby]" value="{$vv.orderby|escape}" size="4" /></td>
      </tr>
  {/foreach}
      <tr>
        <td>&nbsp;</td>
        <td colspan="2"><input type="button" value="{$lng.lbl_delete_selected_variants|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('vids', 'ig'))) {ldelim} document.classform.mode.value = 'delete_variants'; document.classform.submit();{rdelim}" />&nbsp;&nbsp;<input type="submit" name="is_update_variants" value="{$lng.lbl_update|strip_tags:false|escape}" /></td>
      </tr>
  {/if}
      <tr>
        <td>&nbsp;</td>
        <td colspan="2">{include file="main/subheader.tpl" title=$lng.lbl_add_option_variant}</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td width="15"><input type="text" name="new_variant" value="" size="70" /></td>
        <td><input type="text" name="new_orderby" value="" size="4" /></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td colspan="2"><input type="submit" name="is_add_variant" value="{$lng.lbl_add|strip_tags:false|escape}" /></td>
      </tr>
      </table></td>
      <td width="3"><img src="{$ImagesDir}/spacer.gif" width="3" height="1" alt="" /></td>
      <td valign="top" width="8" nowrap="nowrap"><a href="classes.php?fclassid={$class.fclassid}{if $class_lng ne $config.default_admin_language}&amp;class_lng={$class_lng}{/if}"><img src="{$ImagesDir}/delete_record.gif" alt="" /></a></td>
      <td width="3"><img src="{$ImagesDir}/spacer.gif" width="3" height="1" alt="" /></td>
    </tr>
    <tr>
      <td width="3" height="3"><img src="{$ImagesDir}/spacer.gif" width="3" height="3" alt="" /></td>
    </tr>
    </table>
    </td>
    <td width="3"><img src="{$ImagesDir}/spacer.gif" width="3" height="1" alt="" /></td>
  </tr>
  <tr>
    <td><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
  </tr>
  </table>
  </td>
</tr>
{/if}
{foreachelse}
<tr>
  <td colspan="8" align="center">{$lng.lbl_options_not_found}</td>
</tr>
{/foreach}
{if $class.options ne ''}
<tr>
    <td>&nbsp;</td> 
    <td colspan="7">{if $class ne ''}<br /><input type="button" value="{$lng.lbl_delete_selected_options|strip_tags:false|escape}" onclick="javascript: document.classform.mode.value = 'delete_options'; document.classform.submit();" />&nbsp;&nbsp;{/if}<input type="submit" name="is_update" value="{$lng.lbl_update|strip_tags:false|escape}" /></td> 
</tr> 
{/if}
<tr>
  <td>&nbsp;</td>
  <td colspan="7"><br />{include file="main/subheader.tpl" title=$lng.lbl_add_new_option}</td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td valign="top"><input type="text" name="new_option[option_name]" value="" size="10" /></td>
  <td valign="top"><input type="text" name="new_option[option_hint]" value="" size="10" /></td>
  <td valign="top"><select name="new_option[option_type]" onchange="javascript: document.getElementById('new_option_variants').disabled = !(this.value == 'S' || this.value == 'M');">
  {foreach from=$fc_option_types key=ot item=ol}
  <option value="{$ot}">{$ol}</option>
  {/foreach}
  </select></td>
  <td valign="top" align="center"><select name="new_option[avail]">
  <option value="Y">{$lng.lbl_yes}</option>
  <option value="">{$lng.lbl_no}</option>
  </select></td>
  <td valign="top" align="center"><select name="new_option[show_in_search]">
  <option value="Y">{$lng.lbl_yes}</option>
  <option value="N">{$lng.lbl_no}</option>
  </select></td>
  <td valign="top" align="center"><input type="text" name="new_option[orderby]" size="3" value="" /></td>
  <td valign="top"><textarea disabled="disabled" id="new_option_variants" name="new_option_variants" rows="5" cols="15"></textarea></td>
</tr>
<tr>
    <td>&nbsp;</td>
    <td colspan="7"><input type="submit" name="is_add" value="{$lng.lbl_add|strip_tags:false|escape}" /></td>
</tr>
</table>
{/if}

</form>
{/capture}
{if $class.fclassid gt 0}{assign var="dialog_title" value=$lng.lbl_modify_feature_class}{else}{assign var="dialog_title" value=$lng.lbl_add_feature_class}{/if}
{include file="dialog.tpl" title=$dialog_title content=$smarty.capture.dialog extra='width="100%"'}
{/if}
