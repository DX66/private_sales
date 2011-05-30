{*
$Id: partner_report_export.tpl,v 1.1 2010/05/21 08:31:59 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{section name=ri loop=$report}
{$report[ri].login}{$delimiter}{$report[ri].firstname}{$delimiter}{$report[ri].lastname}{$delimiter}{$report[ri].sum_paid}{$delimiter}{$report[ri].sum_nopaid}{$delimiter}{$report[ri].sum}{$delimiter}{$report[ri].min_paid}
{/section}
