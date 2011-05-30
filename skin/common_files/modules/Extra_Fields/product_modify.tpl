{*
$Id: product_modify.tpl,v 1.2 2010/07/13 15:07:39 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{foreach name=exf from=$extra_fields item=ef}

{if $smarty.foreach.exf.first}
<tr>
  {if $geid ne ''}<td class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="2"><hr /></td>
</tr>
{/if}
<tr> 
{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[efields][{$ef.fieldid}]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$ef.field}:</td>
  <td class="ProductDetails">
  <input type="text" name="efields[{$ef.fieldid}]" size="70" value="{if $ef.is_value eq 'Y'}{$ef.field_value|escape:html}{else}{$ef.value|escape:html}{/if}" />
  </td>
</tr>
{/foreach}
