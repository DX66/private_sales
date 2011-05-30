{*
$Id: partner_orders_export.tpl,v 1.1 2010/05/21 08:31:59 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{section name=ri loop=$report}
{$report[ri].orderid}{$delimiter}{$report[ri].login}{$delimiter}{$report[ri].firstname}{$delimiter}{$report[ri].lastname}{$delimiter}{$report[ri].b_address}{$delimiter}{$report[ri].b_address_2}{$delimiter}{$report[ri].b_city}{$delimiter}{$report[ri].b_state}{$delimiter}{$report[ri].b_country}{$delimiter}{$report[ri].subtotal}{$delimiter}{$report[ri].commissions}{$delimiter}{$report[ri].paid}
{/section}
