{*
$Id: pconf_common.tpl,v 1.1 2010/05/21 08:32:46 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $main_mode eq "manage"}
{* Provider/administrator area *}

{if $product}
{assign var="product_title" value=$product.product|truncate:30:"...":false}
{assign var="pconf_title" value="`$pconf_title`<br /><span class='ProductTitle'>`$product_title`</span>"}
{/if}

{if $mode ne "product_modify"}
{include file="page_title.tpl" title=$pconf_title}

{/if}

<br /><br />
{if $mode eq "types"}
{include file="modules/Product_Configurator/pconf_types.tpl"}

{elseif $mode eq "search"}
{include file="modules/Product_Configurator/pconf_search.tpl"}

{elseif $mode eq "product_modify"}
{include file="main/product_modify.tpl"}

{elseif $mode eq "wizard"}
{include file="modules/Product_Configurator/pconf_wizard_modify.tpl"}

{elseif $mode eq "slot"}
{include file="modules/Product_Configurator/pconf_slot_modify.tpl"}

{elseif $mode eq "product"}
{include file="main/product.tpl"}

{else}
{include file="modules/Product_Configurator/pconf_help.tpl"}
{/if}

{else}
{* Customer area *}

{if $mode eq "configure_step"}
{include file="modules/Product_Configurator/pconf_customer_step.tpl"}

{elseif $mode eq "pconf_summary"}
{include file="modules/Product_Configurator/pconf_customer_summary.tpl"}

{else}
{include file="modules/Product_Configurator/pconf_customer_product.tpl"}
{/if}

{/if}
