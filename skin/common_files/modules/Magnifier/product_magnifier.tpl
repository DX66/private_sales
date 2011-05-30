{*
$Id: product_magnifier.tpl,v 1.4.2.2 2010/08/25 09:09:34 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $zoomer_images}
  {capture name=dialog}
    
    {if not $use_popup}
      <div id="magnifier_wrapper" style="width:{$config.Magnifier.magnifier_width}px;height:{$config.Magnifier.magnifier_height}px;">
    {/if}
    
    <div id="magnifier_div">
      <a href="http://www.adobe.com/go/getflashplayer" target="blank">{$lng.lbl_get_latest_flash_player}</a>
    </div>

<script type="text/javascript">
//<![CDATA[

var path = '{$SkinDir}/modules/Magnifier/magnifier.swf';
var xmlImages = "{$xcart_web_dir}/magnifier_xml.php?{if $imageid ne ""}imageid={$imageid}{else}productid={$product.productid|default:$productid}{/if}";
var skinPath = "{$magnifier_sets.url_skins_folder}/{$config.Magnifier.magnifier_skin}";
var showCloseButton = "{$use_popup}";

{if $config.Magnifier.magnifier_image_popup eq 'Y' or $popup_mode eq "Y"}
var flash_background = '#000000';
{assign var="use_popup" value="Y"}
{else}
var flash_background = '#ffffff';
{/if}

{literal}
if (window.swfobject) {
  swfobject.embedSWF(
    path,
    'magnifier_div', 
    '100%', '100%', 
    '8.0.0',
    false,
    {
      xmlImages: xmlImages,
      skinPath: skinPath,
      showCloseButton: showCloseButton
    },
    {
      id: 'flash_magnifier',
      style: 'background-color: ' + flash_background
    }
  );

  if (showCloseButton == 'Y') {
    setTimeout(
      function() {
        if (document.getElementById('flash_magnifier') && document.getElementById('flash_magnifier').focus)
          document.getElementById('flash_magnifier').focus();
      },
      500
    );
  }
}
{/literal}
//]]>
</script>
    
    {if not $use_popup}
      </div>
    {/if}

  {/capture}

  {if $nodialog or $use_popup eq "Y"}
    {$smarty.capture.dialog}
  {else}
    {include file="customer/dialog.tpl" title=$lng.lbl_x_magnifier content=$smarty.capture.dialog}
  {/if}

{/if}
