{*
$Id: affiliate_search_category.tpl,v 1.1 2010/05/21 08:32:16 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

{if $categories}

  <script type="text/javascript" src="{$SkinDir}/js/affiliate_categories.js"></script>

  {capture name=dialog}

    {include file="main/affiliate_categories.tpl" level=0}

  {/capture}
  {include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_categories extra='width="100%"'}

  <br />
{/if}

{if $category}

  {capture name=dialog}

    {include file="main/banner_html_code.tpl"}

  {/capture}
  {include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_banner_html_code extra='width="100%"'}

{/if}
