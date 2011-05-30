{*
$Id: order_cust_processed_subj.tpl,v 1.1 2010/05/21 08:32:14 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}{$config.Company.company_name}: {$lng.eml_order_cust_processed_subj|substitute:"orderid":$order.orderid}
