{*
$Id: copyright.tpl,v 1.1.2.1 2010/08/06 07:56:24 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{$lng.lbl_copyright} &copy; {$config.Company.start_year}{if $config.Company.start_year lt $config.Company.end_year}-{$smarty.now|date_format:"%Y"}{/if} {$config.Company.company_name|escape}
