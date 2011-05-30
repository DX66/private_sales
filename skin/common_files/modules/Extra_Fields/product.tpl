{*
$Id: product.tpl,v 1.1 2010/05/21 08:32:20 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{foreach from=$extra_fields item=v}
  {if $v.active eq "Y" and $v.field_value}
    <tr>
      <td class="property-name">{$v.field}</td>
      <td class="property-value" colspan="2">{$v.field_value}</td>
    </tr>
  {/if}
{/foreach}
