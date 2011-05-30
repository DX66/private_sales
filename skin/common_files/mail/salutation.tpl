{* $Id: salutation.tpl,v 1.1 2010/05/21 08:32:15 joy Exp $ *}
{strip}
{if $salutation ne ""}
   {$lng.eml_dear|substitute:"customer":$salutation}
{else}
   {if $firstname eq "" and $lastname eq ""}
       {$lng.eml_dear_customer}
   {else}
       {$lng.eml_dear|substitute:"customer":"`$title` `$firstname` `$lastname`"}
   {/if}
{/if}
{/strip},
