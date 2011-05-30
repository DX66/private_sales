{*
$Id: spambot_requirements.tpl,v 1.1 2010/05/21 08:32:42 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $spambot_requirements ne ''}

<br />

{include file="main/subheader.tpl" title=$lng.lbl_gcheckout_issues_found class="grey"}
{$spambot_requirements}
{/if}
