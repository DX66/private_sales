{*
$Id: taxed_price.tpl,v 1.1.2.1 2010/12/15 09:44:39 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $taxes}

  {foreach key=tax_name item=tax from=$taxes}

    {if $tax.tax_value gt 0 and  $tax.display_including_tax eq "Y"}

      {if $display_info eq ""}
        {assign var="display_info" value=$tax.display_info}
      {/if}

      {$lng.lbl_including_tax|substitute:"tax":$tax.tax_display_name}

      {if $display_info eq "V" or ($display_info eq "A" and $tax.rate_type eq "$")}

        {if not $is_subtax}
          {currency value=$tax.tax_value tag_id="tax_`$tax.taxid`"}
        {else}
          {currency value=$tax.tax_value}
        {/if}

      {elseif $display_info eq "R"}

        {if $tax.rate_type eq "$"}
          {currency value=$tax.rate_value}
        {else}
          {$tax.rate_value}%
        {/if}

      {elseif $display_info eq "A"}

        {if $tax.rate_type eq "%"}
          {$tax.rate_value}% (

          {if not $is_subtax}
            {currency value=$tax.tax_value tag_id="tax_`$tax.taxid`"}
          {else}
            {currency value=$tax.tax_value}
          {/if}
          )

        {/if}

      {/if}

      <br />

    {/if}

  {/foreach}

{/if}
