{*
$Id: index.tpl,v 1.1 2010/05/21 08:32:05 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $section eq "Password_Recovery"}
{include file="help/Password_Recovery.tpl"}

{elseif $section eq "Password_Recovery_message"}
{include file="help/Password_Recovery_message.tpl"}

{elseif $section eq "Password_Recovery_error"}
{include file="help/Password_Recovery.tpl"}

{else}
{include file="page_title.tpl" title=$lng.lbl_help_zone}

{if $section eq "FAQ"}
{include file="help/FAQ_HTML.tpl"}

{elseif $section eq "contactus"}
{include file="help/contactus.tpl"}

{elseif $section eq "about"}
{include file="help/about.tpl"}

{elseif $section eq "business"}
{include file="help/business.tpl"}

{elseif $section eq "conditions"}
{include file="help/conditions.tpl"}

{elseif $section eq "publicity"}
{include file="help/publicity.tpl"}

{else}
{include file="help/general.tpl"}
{/if}

{/if}
