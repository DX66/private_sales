{*
$Id: popup_info.tpl,v 1.1.2.1 2011/03/04 14:03:25 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<?xml version="1.0" encoding="{$default_charset|default:"iso-8859-1"}"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  {include file="customer/service_head.tpl"}
  <link rel="stylesheet" type="text/css" href="{$SkinDir}/css/{#CSSFilePrefix#}.popup.css" />
  <!--[if lt IE 7]>
  <link rel="stylesheet" type="text/css" href="{$SkinDir}/css/{#CSSFilePrefix#}.popup.IE6.css" />
  <![endif]-->
</head>
<body{$reading_direction_tag}{if $body_onload ne ''} onload="javascript: {$body_onload}"{/if} class="{if $smarty.get.open_in_layer} popup-in-layer{/if}{foreach from=$container_classes item=c}{$c} {/foreach}">
<div id="page-container">
  <div id="page-container2">
    <div id="content-container">
      <div id="content-container2">
        <div id="center">
          <div id="center-main">

<!-- MAIN -->

{include file="customer/dialog_message.tpl"}

{if $template_name ne ""}
{include file=$template_name}

{elseif $pre ne ""}
{$pre}

{else}
{include file="main/error_page_not_found.tpl"}
{/if}

<!-- /MAIN -->
          </div>
        </div>
      </div>
    </div>

    <div class="clearing">&nbsp;</div>

    <div id="header">
      <div>
        {$popup_title|default:"&nbsp;"}
      </div>
    </div>

    <div id="footer">
      <div>
        <a href="javascript:void(0);" onclick="javascript: window.close();">{$lng.lbl_close_window}</a>
      </div>
    </div>

{if $active_modules.SnS_connector}
  {include file="modules/SnS_connector/header.tpl"}
{/if}

{if $active_modules.Google_Analytics ne "" and $config.Google_Analytics.ganalytics_code ne ""}
  {include file="modules/Google_Analytics/ga_code.tpl"}
{/if}

  </div>
</div>

{load_defer_code type="css"}
{load_defer_code type="js"}
</body>
</html>
