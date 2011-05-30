{*
$Id: common.tpl,v 1.1 2010/05/21 08:32:21 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $main eq "comparison" and $mode eq 'product_list' and $classes ne ''}
{include file="modules/Feature_Comparison/comparison_classes_list.tpl"}

{elseif $main eq "comparison"}
{include file="modules/Feature_Comparison/comparison.tpl"}

{elseif $main eq "choosing" and $view_classes_list}
{include file="modules/Feature_Comparison/choosing_classes_list.tpl"}

{elseif $main eq "choosing" and $view_options_list}
{include file="modules/Feature_Comparison/choosing_options_list.tpl"}

{elseif $main eq "choosing"}
{include file="modules/Feature_Comparison/choosing.tpl"}

{/if}
