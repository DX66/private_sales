{*
$Id: rma_authorize_subj.tpl,v 1.1 2010/05/21 08:32:15 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}{$config.Company.company_name}: {$lng.eml_rma_authorize_subj|substitute:"returnid":$return.returnid}
