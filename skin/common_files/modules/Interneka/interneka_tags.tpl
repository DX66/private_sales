{*
$Id: interneka_tags.tpl,v 1.1 2010/05/21 08:32:42 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $active_modules.Interneka ne ""}
{if $config.Interneka.interneka_per_lead eq "Y"}
<!-- begin of the link -->
<img src="https://interneka.com/affiliate/WIDLink.php?WID={$config.Interneka.interneka_id}&amp;Payment=yes&amp;OrderID={$order.order.orderid}" width="1" height="1" alt="" />
<!--- end of the link --> 
{/if}
{if $config.Interneka.interneka_per_sale eq "Y"}
<!-- begin of the link -->
<img src="https://interneka.com/affiliate/WIDLink.php?WID={$config.Interneka.interneka_id}&amp;TotalCost={$order.order.subtotal}&amp;OrderID={$order.order.orderid}" width="1" height="1" alt="" />
<!--- end of the link -->
{/if}
{/if}
