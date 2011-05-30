{*
$Id: html_catalog.tpl,v 1.3 2010/07/07 05:04:59 slam Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript" src="{$SkinDir}/js/html_catalog.js"></script>
<script type="text/javascript" language="JavaScript">
var txt_drop_old_catalog_warning = "{$lng.txt_drop_old_catalog_warning|wm_remove|escape:javascript}";
var lbl_required_tags_are_missing = "{$lng.lbl_required_tags_are_missing|wm_remove|escape:javascript}";
var lbl_format_template_is_invalid = "{$lng.lbl_format_template_is_invalid|wm_remove|escape:javascript}";
var template_max_length = {$template_max_length};

var templates = {ldelim}
{foreach from=$templates key=tk item=t name=templates}
  {$tk}: {ldelim}
    keywords: [
{foreach from=$t.keywords key=kw item=kwd name=keywords}
      {ldelim}keyword: '{$kw}', required: {if $kwd.required}true{else}false{/if}{rdelim}{if $smarty.foreach.keywords.last eq false},{/if}

{/foreach}
    ]
  {rdelim}{if $smarty.foreach.templates.last eq false},{/if}

{/foreach}
{rdelim};
</script>

{include file="page_title.tpl" title=$lng.lbl_html_catalog}

{$lng.txt_html_catalog_top_text}<br />
<br />

{capture name=dialog}

{if $config.SEO.clean_urls_enabled eq 'Y'}
<strong>{$lng.lbl_note}:</strong> {$lng.txt_html_catalog_note_clean_ulr_enable}<br />
{else}
<strong>{$lng.lbl_note}:</strong> {$lng.txt_html_catalog_note_clean_ulr_disable|substitute:href:'configuration.php?option=SEO'}<br />
{/if}
<br />

<form action="html_catalog.php" method="post" name="htmlcatalogform" onsubmit="javascript: return !document.htmlcatalogform.drop_pages.checked || confirm(txt_drop_old_catalog_warning);">
<input type="hidden"  name="mode" value="catalog_gen" />

<table cellpadding="5" cellspacing="1" width="100%" border="0">

<tr valign="top">
  <td colspan="2">
    <strong>{$lng.lbl_gen_catalogs_for_langs}:</strong><br />
    <table width="100%">
    <tr class="TableHead">
      <th width="15%">{$lng.lbl_language}</th>
      <th width="25%">{$lng.lbl_catalog_path}</th>
      <th width="60%">{$lng.txt_html_catalog_was_generated_in}</th>
    </tr>
    {if not $is_cat_empty}
      {foreach from=$cat_info item=c}
        <tr{cycle values=", class='TableSubHead'"}>
            <td>{$c.language}</td>
            <td><input type="text" name="lngcat[{$c.lang_code}]" value="{$c.path|escape}"/></td>
            <td>
              {if $c.path ne ''}
                {$c.cat_dir|amp}<br />
                <a href="{$c.cat_url|amp}" target="_blank">{$c.cat_url|amp}</a>
              {/if}
            </td>
        </tr>
      {/foreach}
    {else}
      {foreach from=$all_languages item=l}
        <tr>
          <td>{$l.language}</td>
          <td><input type="text" name="lngcat[{$l.code}]"{if $l.code eq $config.default_customer_language} value="{$default_catalog_path|escape}"{/if} /></td>
        </tr>
      {/foreach}
    {/if}
    </table>
  </td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_drop_old_catalog}:</td>
  <td>
    <input type="checkbox" name="drop_pages" />
    <span class="SmallText">{$lng.txt_drop_old_catalog_note}</span>
  </td>
</tr>

<tr>
  <td style="vertical-align: top;" class="FormButton">{$lng.lbl_generate_html_pages_for}:</td>
  <td>
      <input type="checkbox" id="gen_action_categories" name="gen_action[categories]" value="1" checked="checked" />
      <label for="gen_action_categories">{$lng.lbl_categories}</label><br />
      <input type="checkbox" id="gen_action_products" name="gen_action[products]" value="2" checked="checked" />
      <label for="gen_action_products">{$lng.lbl_products}</label><br />
      {if $active_modules.Manufacturers eq "Y"}
        <input type="checkbox" id="gen_action_manufacturers" name="process_manufacturers" value="Y" checked="checked" />
        <label for="gen_action_manufacturers">{$lng.lbl_manufacturers}</label><br />
      {/if}
      <input type="checkbox" id="gen_action_pages" name="process_staticpages" value="Y" checked="checked" />
      <label for="gen_action_pages">{$lng.lbl_static_pages}</label><br />
      {if $active_modules.Sitemap eq "Y"}
        <input type="checkbox" id="gen_action_sitemap" name="process_sitemap" value="Y" checked="checked" />
        <label for="gen_action_sitemap">{$lng.sitemap_location}</label><br />
      {/if}
      {if $active_modules.Products_Map eq "Y"}
        <input type="checkbox" id="gen_action_pmap" name="process_pmap" value="Y" checked="checked" />
        <label for="gen_action_pmap">{$lng.pmap_location}</label><br />
      {/if}
  </td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_walk_through_categories}:</td>
  <td>
  <select name="start_category" style="width: 250px;">
    <option value="">{$lng.lbl_root_categories}</option>
    {foreach from=$categories item=c key=cid}
      <option value="{$cid}">{$c|amp}</option>
    {/foreach}
  </select>
  </td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_walk_through_subcategories}:</td>
  <td><input type="checkbox" name="process_subcats" checked="checked" /></td>
</tr>

<tr>
  <td class="FormButton">{$lng.lbl_pages_per_pass}:</td>
  <td>
    <select name="pages_per_pass">
      <option value="0">{$lng.lbl_all}</option>
      <option>100</option>
      <option>50</option>
      <option>20</option>
      <option>10</option>
    </select>
    &nbsp;<span class="SmallText">{$lng.txt_html_pages_per_pass_note}</span>
  </td>
</tr>

{foreach from=$templates key=tk item=t}
<tr>
  <td style="vertical-align: top;" class="FormButton">{$t.label}:</td>
  <td>
    <input type="hidden" name="templates[{$tk}]" value="{$t.template|escape}"/>
    <img src="{$ImagesDir}/plus.gif" alt="" id="mark_{$tk}_plus" onclick="javascript: hcSwitchTemplateEditor(this);" />
    <img src="{$ImagesDir}/minus.gif" alt="" id="mark_{$tk}_minus" onclick="javascript: hcSwitchTemplateEditor(this);" style="display: none;" />
    <a href="javascript:void(0);" onclick="javascript: hcSwitchTemplateEditor(this);" id="example_{$tk}">{$t.template|amp}.html</a>
    <div id="edit_{$tk}" class="HCEdit" style="display: none;">
      <table cellspacing="0" cellpadding="0">
        <tr>
          <td>
            <input type="text" id="template_{$tk}" value="{$t.template|escape}" maxlength="{$template_max_length}" class="HCTemplateValid" onkeyup="javascript: hcCheckTemplateStatus(this);" onchange="javascript: hcCheckTemplateStatus(this);" />
          </td>
          <td>
            <input type="button" value="{$lng.lbl_apply|escape}" class="HCApply" id="apply_{$tk}" onclick="javascript: hcApplyTemplate(this);" /><a href="javascript:void(0);" onclick="javascript: hcReset('{$tk}');">{$lng.lbl_reset}</a>
          </td>
        </tr>
        <tr>
          <td>
            <div class="HCKeywords">
              {foreach from=$t.keywords key=kw item=kwd}
              <div class="HCKeyword{if $kwd.used}Exists{/if}"><a href="javascript:void(0);" id="keyword_{$tk}_{$kw}" onclick="javascript: hcSwitchKeyword('{$tk}', this);" title="{$kwd.alt|escape}">{$kw}</a></div>
              {/foreach}
            </div>
            <div class="HCErrorMsg" id="error_{$tk}"></div>
          </td>
          <td style="padding-left: 5px; vertical-align: top; padding-top: 3px;">
            <div class="HCKeywords">
              {include file="main/tooltip_js.tpl" id="tooltip_`$tk`" text=$lng.txt_html_catalog_template_help|substitute:"rtags":$t.rtags:"nrtags":$t.nrtags:"length":$template_max_length}
            </div>
          </td>
        </tr>
      </table>
    </div>
  </td>
</tr>
{/foreach}

<tr>
  <td class="FormButton">{$lng.lbl_html_catalog_name_delimiter}:</td>
  <td>
    <select name="name_delimiter">
      {foreach from=$name_delimiters key=d item=dname}
        <option value="{$d}"{if $name_delim eq $d} selected="selected"{/if}>{$dname}</option>
      {/foreach}
    </select>
  </td>
</tr>

<tr>
  <td colspan="2" align="center" class="SubmitBox"><input type="submit" value="{$lng.lbl_generate_catalog|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_html_catalog content=$smarty.capture.dialog extra='width="100%"'}
