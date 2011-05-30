{*
$Id: recommends.tpl,v 1.1.2.1 2010/08/16 08:38:26 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $printable ne 'Y' and $recommends}

  {capture name=dialog}

    {include file="customer/simple_products_list.tpl" title=$lng.txt_recommends_comment products=$recommends class="rproducts"}

  {/capture}

  {if $nodialog}
    {$smarty.capture.dialog}
  {else}
    {include file="customer/dialog.tpl" content=$smarty.capture.dialog title=$lng.txt_recommends_comment}
  {/if}

{/if}
