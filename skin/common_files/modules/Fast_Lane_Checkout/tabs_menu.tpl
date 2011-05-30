{*
$Id: tabs_menu.tpl,v 1.1 2010/05/21 08:32:21 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="flc-tabs-container">
  <div class="flc-tabs">
    <ul class="flc-progress-bar">
    {foreach item=step name=checkout_tabs from=$checkout_tabs}
      <li>
        <div class="flc-step">
          <div class="flc-tab-cart-line">
            {if $step.selected eq "Y"}<img src="{$ImagesDir}/spacer.gif" alt="" />{/if}
          </div>

          <div class="flc-tab-marks-line">
            <div class="{if $smarty.foreach.checkout_tabs.first}flc-tab-first {elseif $smarty.foreach.checkout_tabs.last}flc-tab-last {/if}{if $step.selected_after}flc-tab-line-full{elseif $step.selected_before}flc-tab-line-half{/if}">
              <img src="{$ImagesDir}/spacer.gif" class="flc-tab-line-img1" alt="" />
              <img src="{$ImagesDir}/spacer.gif" class="flc-tab-line-img2" alt="" />
              <img src="{$ImagesDir}/spacer.gif" class="flc-tab-line-img3" alt="" />
            </div>
          </div>

        </div>

        <div class="flc-tab-links">
          {if $step.link ne "" and $step.selected_before}
            <a href="{$step.link|amp}">{$step.title}</a>
          {else}
            {$step.title}
          {/if}
        </div>
      </li>
    {/foreach}
  </ul>
</div>
<div class="clearing"></div>
</div>
