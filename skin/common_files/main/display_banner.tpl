{*
$Id: display_banner.tpl,v 1.1.2.1 2010/10/22 07:52:53 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=url}
{if $type ne 'ssi'}{$current_location}{else}{$xcart_web_dir}{/if}/banner.php?bid={$banner.bannerid}{if $test_area}&amp;test={$smarty.now}{/if}&amp;partner={$partner}{if $productid}&amp;productid={$productid}{elseif $categoryid}&amp;categoryid={$categoryid}{elseif $manufacturerid}&amp;manufacturerid={$manufacturerid}{/if}
{/capture}

{if $banner.open_blank ne 'Y'}
  {if $smarty.get.type eq 'iframe'}
    {assign var='target' value="_parent"}
  {else}
    {assign var='target' value="_self"}
  {/if}
{else}
  {assign var='target' value="_blank"}
{/if}

{if $type eq 'iframe'}

  <iframe marginwidth="0" marginheight="0" frameborder="0" scrolling="no" style="border: 0px none; width: {$banner.banner_x|default:$config.XAffiliate.xaff_def_banner_x}px; height:{$banner.banner_y|default:$config.XAffiliate.xaff_def_banner_y}px;" src="{$smarty.capture.url}&amp;type=iframe"></iframe>

{elseif $type eq 'ssi'}

  <!--#include virtual="{$smarty.capture.url}&amp;type=ssi" -->

{elseif $type eq 'js'}

  <script type="text/javascript" src="{$smarty.capture.url}&amp;type=js"></script>

{else}

  {if $banner.banner_type eq 'P'}

    {capture name=a}
      <a href="{$catalogs.customer}/product.php?productid={$product.productid}{if $partner ne ''}&amp;bid={$banner.bannerid}&amp;partner={$partner}{/if}" target="{$target}">
    {/capture}
    
  {elseif $banner.banner_type eq 'C'}

    {capture name=a}
      <a href="{$catalogs.customer}/home.php?cat={$categoryid}{if $partner ne ''}&amp;bid={$banner.bannerid}&amp;partner={$partner}{/if}" target="{$target}">
    {/capture}

  {elseif $banner.banner_type eq 'F'}

    {capture name=a}
      <a href="{$catalogs.customer}/manufacturers.php?manufacturerid={$manufacturerid}{if $partner ne ''}&amp;bid={$banner.bannerid}&amp;partner={$partner}{/if}" target="{$target}">
    {/capture}

  {else}
    {capture name=a}
      <a href="{$catalogs.customer}/home.php{if $partner ne ''}?bid={$banner.bannerid}&amp;partner={$partner}{/if}" target="{$target}">
    {/capture}
  {/if}

  {if $banner.banner_type eq 'T'}

    {$smarty.capture.a}{$banner.body}</a>

  {elseif $banner.banner_type eq 'G'}

    {capture name=link}
      {$smarty.capture.a}<img src="{$current_location}/image.php?type=B&amp;id={$banner.bannerid}{if $partner ne ''}&amp;partner={$partner}{/if}" border="0" alt="{if $banner.alt ne ''}{$banner.alt|escape}{/if}" /></a>
    {/capture}

    {if $banner.legend eq ''}
      {$smarty.capture.link}

    {elseif $banner.direction eq 'U'}
      <div align="center">
        {$banner.legend|escape}<br />
        {$smarty.capture.link}
      </div>
          
    {elseif $banner.direction eq 'L'}
      <table border="0">
        <tr>
          <td>{$banner.legend|escape}</td>
          <td>{$smarty.capture.link}</td>
        </tr>
      </table>

    {elseif $banner.direction eq 'D'}
      <div align="center">
        {$smarty.capture.link}<br />
        {$banner.legend|escape}
      </div>
      
    {elseif $banner.direction eq 'R'}
      <table border="0">
        <tr>
          <td>{$smarty.capture.link}</td>
          <td>{$banner.legend|escape}</td>
        </tr>
      </table>

    {/if}

  {elseif $banner.banner_type eq 'P'}

    <table border="0">

      {if $banner.is_image eq 'Y'}
        <tr>
          <td align="center">{$smarty.capture.a}<img src="{$current_location}/image.php?id={$product.productid}&amp;type=T" border="0" alt="{$product.product|amp}" /></a></td>
        </tr>
      {/if}

      {if $banner.is_name eq 'Y'}
        <tr>
          <td align="center">{$smarty.capture.a}{$product.product}</a></td>
        </tr>
      {/if}

      {if $banner.is_descr eq 'Y'}
        <tr>
          <td align="center">{$product.descr}</td>
        </tr>
      {/if}

      {if $banner.is_add eq 'Y'}
        <tr>
          <td align="center"><a href="{$catalogs.customer}/cart.php?mode=add&amp;productid={$product.productid}&amp;amount=1&amp;from=partner&amp;bid={$banner.bannerid}{if $partner}&amp;partner={$partner}{/if}{if $iframe_referer}&amp;iframe_referer={$iframe_referer}{/if}" target="{$target}">{$label|default:'CLICK HERE TO ORDER'}</a></td>
        </tr>
      {/if}

    </table>

  {elseif $banner.banner_type eq 'C'}

    <table border="0">

      {if $banner.is_image eq 'Y'}
        <tr>
          <td align="center">{$smarty.capture.a}<img src="{$current_location}/image.php?id={$category.categoryid}&amp;type=C" border="0" alt="{$category.category|escape}" /></a></td>
        </tr>
      {/if}

      {if $banner.is_name eq 'Y'}
        <tr>
          <td align="center">{$smarty.capture.a}{$category.category}</a></td>
        </tr>
      {/if}

      {if $banner.is_descr eq 'Y'}
        <tr>
          <td align="center">{$category.description}</td>
        </tr>
      {/if}

    </table>

  {elseif $banner.banner_type eq 'F'}

    <table border="0">

      {if $banner.is_image eq 'Y'}
        <tr>
          <td align="center">{$smarty.capture.a}<img src="{$current_location}/image.php?id={$manufacturer.manufacturerid}&amp;type=M" border="0" alt="{$manufacturer.manufacturer|escape}" /></a></td>
        </tr>
      {/if}

      {if $banner.is_name eq 'Y'}
        <tr>
          <td align="center">{$smarty.capture.a}{$manufacturer.manufacturer}</a></td>
        </tr>
      {/if}

      {if $banner.is_descr eq 'Y'}
        <tr>
          <td align="center">{$manufacturer.descr}</td>
        </tr>
      {/if}

    </table>

  {elseif $banner.banner_type eq 'M'}

    {$banner.body|mrb_prepare}

  {/if}

{/if}
