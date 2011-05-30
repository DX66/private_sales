{*
$Id: choosing.tpl,v 1.1 2010/05/21 08:32:21 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.lbl_search_results}</h1>

{capture name=dialog}

  <div class="right-box">
    {include file="customer/buttons/button.tpl" button_title=$lng.lbl_search_again href=$search_script style="link"}
  </div>

  {include file="customer/main/navigation.tpl"}

  {include file="customer/main/products.tpl"}

  {include file="customer/main/navigation.tpl"}

{/capture}  
{include file="customer/dialog.tpl" title=$lng.lbl_products content=$smarty.capture.dialog selected=$search_sort direction=$search_sort_direction sort=true additional_class="products-dialo"}
