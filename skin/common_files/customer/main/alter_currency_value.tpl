{*
$Id: alter_currency_value.tpl,v 1.1.2.1 2010/12/15 11:57:05 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{*
Use {alter_currency value=..} smarty function for maximal performance
*}
{strip}

{if $alter_currency_value eq ""}
  {assign var="alter_currency_value" value="0"}
{/if}

{if $config.General.alter_currency_symbol ne ""}

  (

  {if $plain_text_message eq ""}
    <span class="nowrap">
  {/if}

  {alt_currency value=$alter_currency_value rate=$config.General.alter_currency_rate assign="cf_value" display_sign=$display_sign}

  {assign var="cf_value" value=$cf_value|abs_value|formatprice}

  {if $tag_id ne "" and $plain_text_message eq ""}
    {assign var="cf_value" value="<span id=\"`$tag_id`\">`$cf_value`</span>"}
  {/if}

  {$config.General.alter_currency_format|replace:"x":$cf_value|replace:"$":$config.General.alter_currency_symbol}

  {if $plain_text_message eq ""}
    </span>
  {/if}

  )

{/if}

{/strip}
