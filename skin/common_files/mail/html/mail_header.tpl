{*
$Id: mail_header.tpl,v 1.1 2010/05/21 08:32:16 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<br /><font size="2">
{assign var="link" value="<a href=\"$http_location/\" target=\"_blank\">`$config.Company.company_name`</a>"}
{$lng.eml_mail_header|substitute:"company":$link}
</font>

