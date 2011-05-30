{*
$Id: language_selector_short.tpl,v 1.1 2010/05/21 08:32:17 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $all_languages_cnt gt 1}
  <select{if $selector_disabled} disabled="disabled"{else} id="edit_lng" name="edit_lng" onchange="javascript: self.location='{$script|amp}edit_lng='+this.value+'&amp;old_lng={$shop_language}{if $anchor ne ''}#{$anchor}{/if}';"{/if}>
    {if $shop_language eq $config.default_admin_language and $is_no_default eq 'Y'}
      <option value="">{$lng.lbl_please_select_language}</option>
    {/if}
    {foreach from=$all_languages item=v}
      {if ($v.code ne $config.default_admin_language or $is_no_default ne 'Y') and $v.language ne ''}
        <option value="{$v.code|escape}"{if $v.code eq $shop_language} selected="selected"{/if}>{$v.language}</option>
      {/if}
    {/foreach}
  </select>
{/if}
