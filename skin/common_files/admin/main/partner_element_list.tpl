{*
$Id: partner_element_list.tpl,v 1.3 2010/06/08 06:17:38 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>{$lng.txt_site_title}</title>
  {include file="meta.tpl"}
  <link rel="stylesheet" type="text/css" href="{$SkinDir}/css/skin1_admin.css" />
</head>
<body onload="javascript: change_images_width(self.document.documentElement.getElementsByTagName('body')[0].scrollWidth);"{$reading_direction_tag}>
<script type="text/javascript">
//<![CDATA[
var images = [
{foreach from=$elements item=v key=k name=elements} 
  [{if $v.image_type eq 'application/x-shockwave-flash'}0, 0{else}{$v.id}, {$v.image_x|default:"0"}{/if}]{if not $smarty.foreach.elements.last},{/if}
{/foreach}
]

{literal}
function change_images_width(w) {
  w -= 8;

  for (var x = 0; x < images.length; x++) {
    if (images[x][0] > 0 && images[x][1] > 370)
      document.getElementById('img'+images[x][0]).width = w;
  }
}

function zoom_open(id, x, y) {
  return window.open(
    xcart_web_dir + '/image.php?type=L&id='+id,
    'ZOOMIN_POPUP',
    'width=' + (x + 20) + ',height=' + (y + 20) + ',toolbar=no,status=no,scrollbars=no,resizable=yes,menubar=no,location=no,direction=no'
  );
}
{/literal}
//]]>
</script>

<table cellspacing="2" cellpadding="0" width="100%" class="SectionBox">

  {foreach from=$elements item=v} 
    <tr{cycle values=", class='TableSubHead'"}>
      <td class="AffiliateElmsBox">

        <table cellspacing="0" cellpadding="0" width="100%">
          <tr>
            <td colspan="2" class="AffiliateElmTitle">{$v.id}</td> 
          </tr>
          <tr>
            <td class="AffiliateElmIconBox" colspan="2">
              <a href="javascript:void(0);" onclick="javascript: zoom_open('{$v.id}','{$v.image_x}','{$v.image_y}');">
                {if $v.image_type ne 'application/x-shockwave-flash'}
                  <img id="img{$v.id}" src="{$current_location}/image.php?id={$v.id}&amp;type=L"{if $v.image_x gt 370} width="370"{/if} alt="" />
                {else}
                  <img src="{$ImagesDir}/flash_icon1.gif" alt="" />
                {/if}
              </a>
            </td>
          </tr>
          <tr>
            <td width="50%" align="left">

               {if $v.image_type ne 'application/x-shockwave-flash'}
                <a href="javascript:void(0);" onclick="javascript: window.top.document.getElementById('banner_body').value +='<#A{$v.id}#>';" class="add-elm">{$lng.lbl_add} ({$lng.lbl_clickable})</a>
                <br/>
               {/if}
              <a href="javascript:void(0);" onclick="javascript: window.top.document.getElementById('banner_body').value +='<#{$v.id}#>';" class="add-elm">{$lng.lbl_add}{if $v.image_type ne 'application/x-shockwave-flash'} ({$lng.lbl_non_clickable}){/if}</a>
              <br />
              <a href="javascript:void(0);" onclick="javascript: zoom_open('{$v.id}','{$v.image_x}','{$v.image_y}');" class="zoom-elm">{$lng.lbl_zoom_in}</a>
              <br />
              <a href="partner_banners.php?id={$v.id}&amp;mode=delete" class="delete-elm">{$lng.lbl_delete}</a>

            </td>
            <td valign="top" align="left">

              <table cellspacing="0" cellpadding="0" class="MediaElementProperties">
                <tr>
                  <td class="MediaElementProperty">{$lng.lbl_width}:</td>
                  <td>{$v.image_x} px</td>
                </tr>
                <tr>
                  <td class="MediaElementProperty">{$lng.lbl_height}:</td>
                  <td>{$v.image_y} px</td>
                </tr>
                <tr>
                  <td class="MediaElementProperty">{$lng.lbl_image_type}:</td>
                  <td>{$v.image_type|regex_replace:"/^[^\/]*\//":""}</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr> 
        </table>
      </td> 
    </tr>
  {/foreach}
</table>

</body>
</html>

