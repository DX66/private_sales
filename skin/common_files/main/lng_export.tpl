{*
$Id: lng_export.tpl,v 1.1 2010/05/21 08:32:17 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{section name=di loop=$data}
{$data[di].name}{$csv_delimiter}{$data[di].value}{$csv_delimiter}{$data[di].descr}{$csv_delimiter}{$data[di].topic}
{/section}
