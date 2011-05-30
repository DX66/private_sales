{*
$Id: zone_element.tpl,v 1.1 2010/05/21 08:32:53 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<textarea cols="40" rows="{$box_size|default:3}" style="width: 100%;" name="{$name}">
{section name=id loop=$zone_elements}
{if $zone_elements[id].field_type eq $field_type}
{$zone_elements[id].field|escape}
{/if}
{/section}
</textarea>

