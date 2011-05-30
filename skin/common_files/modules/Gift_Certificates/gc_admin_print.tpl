{*
$Id: gc_admin_print.tpl,v 1.3 2010/07/02 11:52:50 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
{if $css_file ne ""}
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$default_charset|default:"iso-8859-1"}" />
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<title></title>
<link rel="stylesheet" type="text/css" href="{$SkinDir}/modules/Gift_Certificates/{$css_file}" />
</head>
{/if}
<body{$reading_direction_tag}>
{if $config.Gift_Certificates.print_giftcerts_separated eq "Y"}
{assign var="separator" value="<div style='page-break-after: always;'><!--[if IE 7]><br style='height: 0px; line-height: 0px;' /><![endif]--></div>"}
{else}
{assign var="separator" value="<br /><hr size='1' noshade="noshade" /><br />"}
{/if}
{foreach name=giftcerts from=$giftcerts key=key item=giftcert}
{include file="modules/Gift_Certificates/`$giftcert.tpl_file`"}
{if not $smarty.foreach.giftcerts.last}
{$separator}
{/if}
{/foreach}
</body>
</html>
