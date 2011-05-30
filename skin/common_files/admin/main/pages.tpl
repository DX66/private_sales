{*
$Id: pages.tpl,v 1.5 2010/07/30 13:01:51 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_static_pages}

{$lng.txt_static_pages_top_text}

<br /><br />

{capture name=dialog}

{include file="main/language_selector.tpl" script="pages.php?"}

{if $is_writable}

<form action="pages.php" method="post" name="pagesform_e">
<input type="hidden" name="mode" value="update" />
<input type="hidden" name="sec" value="E" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td colspan="6">{include file="main/subheader.tpl" title=$lng.lbl_embedded_level class="grey"}</td>
</tr>

{if $pages}

{capture name=embedpages}

{section name=pg loop=$pages}

{if $pages[pg].level eq "E"}

{assign var="embedded" value="found"}

<tr{cycle name="embed" values=", class='TableSubHead'"}>
<td width="5">
  <input type="checkbox" name="to_delete[{$pages[pg].pageid}]" value="Y" />
</td>
<td>
  <input type="text" name="posted_data[{$pages[pg].pageid}][orderby]" value="{$pages[pg].orderby}" size="5" />
</td>
<td>
  <b><a href="pages.php?pageid={$pages[pg].pageid}" title="{$pages[pg].filename|escape}">
    {$pages[pg].title|truncate:"40":"..."|escape}
  </a></b>
</td>
<td align="center">
  <input type="checkbox" name="posted_data[{$pages[pg].pageid}][show_in_menu]" value="Y"{if $pages[pg].show_in_menu eq 'Y'} checked="checked"{/if} />
</td>
<td>
  <input type="checkbox" name="posted_data[{$pages[pg].pageid}][active]" value="Y"{if $pages[pg].active eq "Y"} checked="checked"{/if} />
</td>
<td align="right" width="30">
  <a href="{$catalogs.customer}/pages.php?pageid={$pages[pg].pageid}&amp;mode=preview" target="previewpage">
    {$lng.lbl_preview}
  </a>
</td>
</tr>

{/if}

{/section}

{/capture}

{/if}

{if $embedded}

<tr>
<td colspan="6">
{include file="main/check_all_row.tpl" form="pagesform_e" prefix="to_delete"}
</td>
</tr>

{/if}

<tr class="TableHead">
  <td width="10">&nbsp;</td>
  <td width="10%">{$lng.lbl_pos}</td>
  <td width="60%">{$lng.lbl_page_title}</td>
  <td width="10%">{$lng.lbl_page_th_show_in_menu}</td>
  <td width="100" colspan="2">{$lng.lbl_status}</td>
</tr>

{if $embedded}

{$smarty.capture.embedpages}

<tr>
<td>&nbsp;</td>
<td colspan="5">
  <hr />
    <table cellpadding="0" cellspacing="0">
    <tr>
    <td width="5">
      <input type="checkbox" id="parse_smarty_tags" name="parse_smarty_tags" value="Y"{if $config.General.parse_smarty_tags eq "Y"} checked="checked"{/if} />
    </td>
    <td>
      <label for="parse_smarty_tags">
        {$lng.lbl_parse_smarty_tags_in_embedded_pages}
      </label>
    </td>
    </tr>
    </table>
</td>
</tr>

<tr>
  <td colspan="6" class="SubmitBox">
    <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('to_delete\[[0-9]+\]', 'gi'))) submitForm(this, 'delete');" />
    <input type="button" value="{$lng.lbl_update|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'update');" />
  </td>
</tr>

{else}

<tr>
<td align="center" colspan="6">
  {$lng.txt_no_embedded_pages}
</td>
</tr>

{/if}

<tr>
  <td colspan="6" class="SubmitBox">
    <input type="button" value="{$lng.lbl_add_new_|strip_tags:false|escape}" onclick="javascript: self.location='pages.php?level=E&amp;pageid=0';" />
  </td>
</tr>

</table>

</form>

<form action="pages.php" method="post" name="pagesform_r">
<input type="hidden" name="mode" value="update" />
<input type="hidden" name="sec" value="R" />

<table cellpadding="3" cellspacing="1" width="100%">

{if $pages}

{capture name=rootpages}

{section name=pg loop=$pages}

{if $pages[pg].level eq "R"}

{assign var="root" value="found"}

<tr{cycle name="root" values=", class='TableSubHead'"}>
  <td width="2">
    <input type="checkbox" name="to_delete[{$pages[pg].pageid}]" value="Y" />
    <input type="hidden" name="pages_array[{$pages[pg].pageid}][active]" value="Y" />
    <input type="hidden" name="posted_data[{$pages[pg].pageid}][show_in_menu]" value="" />
  </td>
  <td>
    <b><a href="pages.php?pageid={$pages[pg].pageid}" title="{$pages[pg].filename|escape}">
      {$pages[pg].title|truncate:"40":"..."|escape}
    </a></b>
  </td>
  <td align="right" width="30">
    <a href="{$xcart_web_dir}/{$pages[pg].filename}" target="previewpage">
      {$lng.lbl_preview}
    </a>
  </td>
</tr>

{/if}

{/section}

{/capture}

{/if}

<tr>
<td colspan="3">
  <br /><br />
  {include file="main/subheader.tpl" title=$lng.lbl_root_level class="grey"}
</td>
</tr>

{if $root}

<tr>
<td colspan="3">
  {include file="main/check_all_row.tpl" form="pagesform_r" prefix="to_delete"}
</td>
</tr>

{/if}

<tr class="TableHead">
<td width="2">&nbsp;</td>
<td colspan="2">
  {$lng.lbl_page_title}
</td>
</tr>

{if $root}

{$smarty.capture.rootpages}

<tr>
  <td colspan="3" class="SubmitBox">
    <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('to_delete\[[0-9]+\]', 'gi'))) submitForm(this, 'delete');" />
  </td>
</tr>

{else}

<tr>
<td align="center" colspan="3">
  {$lng.txt_no_root_pages}
</td>
</tr>

{/if}

<tr>
<td colspan="3" class="SubmitBox">
  <input type="button" value="{$lng.lbl_add_new_|strip_tags:false|escape}" onclick="javascript: self.location='pages.php?level=R&amp;pageid=0';" />

  <br /><br />

  <div align="right">
    <input type="button" value="{$lng.lbl_find_pages|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'check');" />
  </div>
</td>
</tr>

</table>
</form>

{else}

{$lng.txt_the_directory_is_not_writable|substitute:"X":$template_dir}

{/if}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_static_pages content=$smarty.capture.dialog extra='width="100%"'}

