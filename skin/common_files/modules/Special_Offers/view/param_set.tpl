{*
$Id: param_set.tpl,v 1.1 2010/05/21 08:32:49 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<table cellpadding="3" cellspacing="0" border="0" width="100%" class="sp-product-set-box">
{assign var="is_first" value=true}
{if $params}
{foreach from=$params item=param}
{if ($param.setid eq $set_id and $param.param_type ne "S") or ($force_display eq "Y")}
  {assign var="is_first" value=false}
  <tr class="TableSubHead">
    <td width="1%">&#8226;</td>
    {if $param.param_type eq "P"}
    <td><a href="product_modify.php?productid={$param.param_id}">{$param.product}</a> [{$param.param_qnty} {$lng.lbl_sp_items}]</td>
    {elseif $param.param_type eq "C"}
    {if $param.param_arg eq "Y"}
      {assign var="subcats" value="&amp;subcats=Y"}
    {else}
      {assign var="subcats" value=""}
    {/if}
    {assign var="link" value="<a href=\"search.php?mode=search&amp;cat=`$param.param_id``$subcats`\">`$param.category`</a>"}
    <td>{$lng.lbl_sp_products_from_cat_s|substitute:"cat":$link} [{$param.param_qnty} {$lng.lbl_sp_items}]</td>
    {elseif $param.param_type eq "Z"}
    <td><a href="zones.php?zoneid={$param.param_id}">{$param.zone_name}</a> [{$lng.lbl_sp_zone_type}: {if $param.param_arg eq "B"}{$lng.lbl_sp_zone_billing}{else}{$lng.lbl_sp_zone_shipping}{/if}]</td>
    {/if}
  </tr>
  {/if}
  {/foreach}
  {/if}
  {if $is_first}
  <tr class="TableSubHead">
    <td colspan="2" align="center">{if $item_type eq "B" and $empty_params_lbl ne ""}{$empty_params_lbl}{else}{$lng.lbl_no_products_selected}{/if}</td>
  </tr>
{/if}
</table>

