{*
$Id: config.tpl,v 1.3.2.1 2010/08/24 07:32:16 ferz Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

{include file="main/subheader.tpl" title=$lng.xmlmap_generate_section}
<form name="xmlmap_generate" method="post" action="{$smarty.server.REQUEST_URI|escape}">
  <input type="hidden" name="xmlmap[config]" value="generate" />
  {assign var="xseo_xmlmap_url" value=`$http_location`/`$config.XML_Sitemap.filename`}
  {$lng.xmlmap_generate_note|substitute:"url":$xseo_xmlmap_url}
  <br /><br />
  <input type="submit" value="{$lng.lbl_go|strip_tags:false|escape}" />
</form>

<form name="form_xmlmap" method="post" action="{$smarty.server.REQUEST_URI|escape}">
<input type="hidden" name="xmlmap[config]" value="update" />

<table width="100%">

<tr>
  <td colspan="3"><br />{include file="main/subheader.tpl" title=$lng.xmlmap_extraurls_section}</td>
</tr>

<tr class="TableHead">
  <td width="10">&nbsp;</td>
  <td width="90%">{$lng.lbl_page_url}</td>
  <td width="10%">{$lng.lbl_active}</td>
</tr>

{foreach name="xmlmap_extra_items" from=$xmlmap_extra item=item}
<tr{cycle name=$type values=", class='TableSubHead'"}>
  <td><input type="checkbox" name="xmlmap[delete][]" value="{$item.id}" /></td>
  <td align="center"><input type="text" size="88" name="xmlmap[update][{$item.id}][url]" value="{$item.url|escape}" /></td>
  <td align="center"><input type="checkbox" name="xmlmap[update][{$item.id}][active]" value="Y"{if $item.active eq 'Y'} checked="checked"{/if} /></td>
</tr>

{if $smarty.foreach.xmlmap_extra_items.last}
<tr>
  <td colspan="3" class="SubmitBox">
  <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick='javascript: if (checkMarks(this.form, new RegExp("xmlmap.delete", "ig"))) {ldelim}document.form_xmlmap.elements["xmlmap[config]"].value="delete"; document.form_xmlmap.submit();{rdelim}' />
  <input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
  </td>
</tr>
{/if}

{foreachelse}
<tr>
  <td colspan="3" align="center">{$lng.sitemap_noextraurls}</td>
</tr>
{/foreach}

<tr>
  <td colspan="3"><br />{include file="main/subheader.tpl" title=$lng.lbl_add_new}</td>
</tr>

<tr>
  <td>&nbsp;</td>
  <td align="center"><input type="text" size="88" name="xmlmap[add][url]" /></td>
  <td align="center"><input type="checkbox" name="xmlmap[add][active]" value="Y" checked="checked" /></td>
</tr>

<tr>
  <td colspan="3" class="SubmitBox">
  <input type="button" value="{$lng.lbl_add_new|strip_tags:false|escape}" onclick='javascript: document.form_xmlmap.elements["xmlmap[config]"].value="add"; document.form_xmlmap.submit();' />
  </td>
</tr>

</table>
</form>
