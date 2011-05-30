{*
$Id: vote_subbar.tpl,v 1.1 2010/05/21 08:32:20 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<ul class="star-{$level}">
  <li class="star-{$level}">
    {if $rating.allow_add_rate}
      <a href="product.php?mode=add_vote&amp;productid={$productid}&amp;vote={$stars.levels[$level]}{if $is_pconf}&amp;pconf={$current_product.productid}&amp;slot={$slot}{/if}"{if $rating.full_stars gt $level} class="full"{/if}{if $stars.titles[$level]} title="{$stars.titles[$level]|escape}"{/if}>
        {if $config.UA.browser eq 'MSIE' and $config.UA.version lt 7}
          <span class="bg"></span>
        {/if}
        {if $rating.full_stars eq $level and $rating.percent gt 0}
          <img src="{$ImagesDir}/spacer.gif" alt="" style="width: {$rating.percent}%;" />
        {/if}
      </a>
    {else}
      <span{if $rating.full_stars gt $level} class="full"{/if}>
        {if $config.UA.browser eq 'MSIE' and $config.UA.version lt 7}
          <span class="bg"></span>
        {/if}
        {if $rating.full_stars eq $level and $rating.percent gt 0}
          <img src="{$ImagesDir}/spacer.gif" alt="" style="width: {$rating.percent}%;" />
        {/if}
      </span>
    {/if}
    {if $stars.length gt $level+1}
      {include file="modules/Customer_Reviews/vote_subbar.tpl" level=$level+1}
    {/if}
  </li>
</ul>
