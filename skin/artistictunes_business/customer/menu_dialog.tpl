{*
$Id: menu_dialog.tpl,v 1.1 2010/05/21 08:31:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="menu-dialog{if $additional_class} {$additional_class}{/if}">
  <div class="title-bar valign-middle{if $link_href} link-title{/if}">
    <div class="title-bar-points">
    {strip}

      {if $link_href}
        <span class="title-link">
          <a href="{$link_href}" class="title-link"><img src="{$ImagesDir}/spacer.gif" alt=""  /></a>
        </span>
      {/if}

      <h2>{$title}</h2>

    {/strip}
    </div>
  </div>
  <div class="content">
    {$content}
  </div>
</div>
