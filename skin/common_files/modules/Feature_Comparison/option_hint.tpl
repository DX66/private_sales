{*
$Id: option_hint.tpl,v 1.2.2.1 2010/08/17 13:03:39 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $opt.option_hint}
  {include file="main/tooltip_js.tpl" title=$opt.option_name text=$opt.option_hint id="fc_opt_help_`$opt.foptionid`" class="help-link"}
{else}
  {$opt.option_name}
{/if}
