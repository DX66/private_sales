{*
$Id: preview.tpl,v 1.1 2010/05/21 08:31:58 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $template}
  {config_load file="$skin_config"}
  {if $use_default_css}
    <link rel="stylesheet" type="text/css" href="{$SkinDir}/css/skin1_admin.css" />
  {/if}
  {include file=$template}
{/if}
