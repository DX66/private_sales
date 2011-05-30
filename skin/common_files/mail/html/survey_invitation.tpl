{*
$Id: survey_invitation.tpl,v 1.1 2010/05/21 08:32:16 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}
<p>{$lng.eml_dear_customer},

<p>{$lng.eml_survey_invitation|substitute:"link":$link}

{include file="mail/html/signature.tpl"}
