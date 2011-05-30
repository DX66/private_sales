{*
$Id: home.tpl,v 1.1.2.1 2010/08/12 10:14:59 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<?xml version="1.0" encoding="{$default_charset|default:"iso-8859-1"}"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  {include file="customer/service_head.tpl"}
<!--[if lt IE 7]>
<script src="{$SkinDir}/customer/iefix.js" type="text/javascript"></script>
<![endif]-->
</head>
<body{if $body_onload ne ''} onload="javascript: {$body_onload}"{/if}{if $container_classes} class="{foreach from=$container_classes item=c}{$c} {/foreach}"{/if}>
<div id="page-container"{if $page_container_class} class="{$page_container_class}"{/if}>
  <div id="page-container2">
    <div id="content-container">
      <div id="content-container2">

        {include file="customer/content.tpl"}

      </div>
    </div>

    <div class="clearing">&nbsp;</div>

    <div id="header">
      {include file="customer/head.tpl"}
    </div>

    <div id="footer">

      {if $active_modules.Users_online}
        {include file="modules/Users_online/menu_users_online.tpl"}
      {/if}

      {include file="customer/bottom.tpl"}

    </div>

    {if $active_modules.SnS_connector}
      {include file="modules/SnS_connector/header.tpl"}
    {/if}

    {if $active_modules.Google_Analytics and $config.Google_Analytics.ganalytics_code}
      {include file="modules/Google_Analytics/ga_code.tpl"}
    {/if}

  </div>
</div>
{load_defer_code type="js"}
{load_defer_code type="css"}
</body>
</html>
