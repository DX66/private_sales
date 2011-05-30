{*
$Id: vote_bar.tpl,v 1.1.2.1 2010/12/02 14:21:25 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="creviews-rating-box">
  <div class="creviews-vote-bar{if $rating.allow_add_rate} allow-add-rate{/if}" title="{if $rating.total gt 0}{$lng.txt_rating_note|substitute:avg:$rating.rating_level:rating:$rating.total|escape}{else}{$lng.lbl_not_rated_yet|escape}{/if}">

 {section loop=`$stars.length` name=vote_subbar}
  <ul class="star-{$smarty.section.vote_subbar.index}">
    <li class="star-{$smarty.section.vote_subbar.index}">
      {if $rating.allow_add_rate}
        <a href="product.php?mode=add_vote&amp;productid={$productid}&amp;vote={$stars.levels[$smarty.section.vote_subbar.index]}{if $is_pconf}&amp;pconf={$current_product.productid}&amp;slot={$slot}{/if}"{if $rating.full_stars gt $smarty.section.vote_subbar.index} class="full"{/if}{if $stars.titles[$smarty.section.vote_subbar.index]} title="{$stars.titles[$smarty.section.vote_subbar.index]|escape}"{/if}>
          {if $config.UA.browser eq 'MSIE' and $config.UA.version lt 7}
            <span class="bg"></span>
          {/if}
          {if $rating.full_stars eq $smarty.section.vote_subbar.index and $rating.percent gt 0}
            <img src="{$ImagesDir}/spacer.gif" alt="" style="width: {$rating.percent}%;" />
          {/if}
        </a>
      {else}
        <span{if $rating.full_stars gt $smarty.section.vote_subbar.index} class="full"{/if}>
          {if $config.UA.browser eq 'MSIE' and $config.UA.version lt 7}
            <span class="bg"></span>
          {/if}
          {if $rating.full_stars eq $smarty.section.vote_subbar.index and $rating.percent gt 0}
            <img src="{$ImagesDir}/spacer.gif" alt="" style="width: {$rating.percent}%;" />
          {/if}
        </span>
      {/if}
  {/section}
  {section loop=`$stars.length` name=vote_subbar}
    </li>   </ul>
  {/section}

  </div>
  {load_defer file="modules/Customer_Reviews/vote_bar.js" type="js"}

  {if not $rating.allow_add_rate and $rating.forbidd_reason}
    <div class="creviews-rating">

      {if $rating.forbidd_reason eq 'already_added'}
        {$lng.txt_you_have_rated_this_product}
      {elseif $rating.forbidd_reason eq 'unlogged'}
        {$lng.lbl_sign_in_to_rate}
      {/if}

    </div>
  {/if}

</div>
