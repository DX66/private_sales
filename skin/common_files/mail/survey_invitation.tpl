{*
$Id: survey_invitation.tpl,v 1.1 2010/05/21 08:32:15 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/mail_header.tpl"}

{$lng.eml_dear_customer},

{$lng.eml_survey_invitation_txt|substitute:"link":$link}

{include file="mail/signature.tpl"}
