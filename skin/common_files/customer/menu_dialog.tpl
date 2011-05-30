{*
$Id: menu_dialog.tpl,v 1.2 2010/07/22 08:33:38 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="menu-dialog{if $additional_class} {$additional_class}{/if}">
  <div class="title-bar {if $link_href} link-title{/if}">
    {strip}

      {if $link_href}
        <span class="title-link">
          <a href="{$link_href}" class="title-link"><img src="{$ImagesDir}/spacer.gif" alt=""  /></a>
        </span>
      {/if}

      <img class="icon ajax-minicart-icon" src="{$ImagesDir}/spacer.gif" alt="" />
      <h2>{$title}</h2>

    {/strip}
  </div>
  <div class="content">
    {$content}
  </div>
</div>
