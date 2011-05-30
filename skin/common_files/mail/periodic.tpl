{*
$Id: periodic.tpl,v 1.1 2010/05/21 08:32:14 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/mail_header.tpl"}


{$lng.eml_periodic_title|substitute:"period":$periodic_subject_period}
{$lng.lbl_shop_url}: {$http_location}

{if $config.Maintenance_Agent.periodic_visits eq "Y"}

{$lng.eml_periodic_shop_visits}
--------------------

{if $stat_visits ne ""}
{foreach from=$stat_visits item=stat}
{if $stat.usertype eq "A" or $stat.usertype eq "P"}
{$lng.eml_periodic_visits_staff|substitute:"login":$stat.login:"usertype":$stat.usertype_txt:"visits":$stat.visits:"last_date":$stat.date_time_formated}
{else}
{if $stat.visits_all gt 0}
{$lng.eml_periodic_visits_ordinary|substitute:"usertype":$stat.usertype_txt:"unique":$stat.visits_unique:"visits_all":$stat.visits_all:"last_date":$stat.date_time_formated}
{else}
{$stat.usertype_txt}: {$lng.eml_periodic_no_visits}
{/if}
{/if}
{/foreach}
{else}
{$lng.eml_periodic_no_visits}
{/if}
{/if}{* periodic_visits ne "" *}

{if $config.Maintenance_Agent.periodic_orders eq "Y"}

{$lng.eml_periodic_shop_orders}
--------------------

{if $stat_orders}
{$lng.lbl_processed}: {$stat_orders.P}
{$lng.lbl_failed}/{$lng.lbl_declined}: {$stat_orders.F}
{$lng.lbl_not_finished}: {$stat_orders.I}
{$lng.lbl_queued}: {$stat_orders.Q}
{/if}
{/if}{* periodic_orders eq "Y" *}

{if $config.Maintenance_Agent.periodic_logs ne ""}

{$lng.eml_periodic_shop_logs}
--------------------

{if $stat_logs_data ne ""}
{foreach key=label item=data from=$stat_logs_data}
{$stat_log_names.$label|default:$label}:
{$data}
{/foreach}
{else}
{$lng.eml_periodic_no_logs}
{/if}
{/if}{* periodic_logs ne "" *}

{include file="mail/signature.tpl"}
