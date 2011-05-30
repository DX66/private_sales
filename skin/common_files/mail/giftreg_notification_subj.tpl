{*
$Id: giftreg_notification_subj.tpl,v 1.1 2010/05/21 08:32:14 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if not $display_only_body}{config_load file="$skin_config"}{$config.Company.company_name}: {/if}{$mail_data.subj|default:$lng.eml_giftreg_notification_subj}
