{*
$Id: fancy_categories.tpl,v 1.3 2010/06/08 06:17:40 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $fc_skin_path}

  <script type="text/javascript" src="{$SkinDir}/{$fc_skin_path}/func.js"></script>
  <div id="{$fancy_cat_prefix}rootmenu" class="fancycat-icons-scheme {if $config.Flyout_Menus.icons_mode eq 'C'}fancycat-icons-c{else}fancycat-icons-e{/if}">
    {if $fancy_use_cache}
      {fancycat_get_cache}

    {elseif $config.Flyout_Menus.icons_mode eq 'C'}
      {include file="`$fc_skin_path`/fancy_subcategories_exp.tpl" level=0}

    {else}
      {include file="`$fc_skin_path`/fancy_subcategories.tpl" level=0}
    {/if}
    {if $catexp}
<script type="text/javascript">
//<![CDATA[
var catexp = {$catexp|default:0};
//]]>
</script>
    {/if}
    <div class="clearing"></div>
  </div>
{/if}
