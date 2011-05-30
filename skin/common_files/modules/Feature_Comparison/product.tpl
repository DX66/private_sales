{*
$Id: product.tpl,v 1.1 2010/05/21 08:32:21 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{foreach from=$product.features.options item=v}
  <tr>
    <td class="property-name" nowrap="nowrap">
    {include file="modules/Feature_Comparison/option_hint.tpl" opt=$v}
    </td>
    <td class="property-value" valign="top" colspan="2">

      {if $v.option_type eq 'S'}

        {$v.variants[$v.value].variant_name}

      {elseif $v.option_type eq 'M'}

        {foreach from=$v.variants item=o}
          {if $o.selected ne ''}
            {$o.variant_name}<br />
          {/if}
        {/foreach}

      {elseif $v.option_type eq 'B'}

        {if $v.value eq 'Y'}
          {$lng.lbl_yes}
        {else}
          {$lng.lbl_no}
        {/if}

      {elseif ($v.option_type eq 'N' or $v.option_type eq 'D') and $v.value ne ''}

        {$v.formated_value}

      {else}

        {$v.value|replace:"\n":"<br />"}

      {/if}

    </td>
  </tr>
{/foreach}
