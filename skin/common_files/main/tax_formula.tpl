{*
$Id: tax_formula.tpl,v 1.1 2010/05/21 08:32:18 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td><input type="text" size="70" id="{$name}" name="{$name}" value="={$value}" readonly="readonly" style="WIDTH: 100%;" /></td>
</tr>

<tr>
  <td align="right">
<input type="button" value="{$lng.lbl_undo|strip_tags:false|escape}" onclick="javacript: undoFormula('{$name}', 'U');" />
<input type="button" value="{$lng.lbl_redo|strip_tags:false|escape}" onclick="javacript: undoFormula('{$name}', 'R');" />
<input type="button" value="{$lng.lbl_clear|strip_tags:false|escape}" onclick="javacript: addElm('{$name}', '=', '=');" />
  </td>
</tr>

<tr>
  <td>
<input type="button" value=" + " onclick="javascript: addElm('{$name}', '+', 'O');" />
<input type="button" value=" - " onclick="javascript: addElm('{$name}', '-', 'O');" />
<input type="button" value=" * " onclick="javascript: addElm('{$name}', '*', 'O');" />
<input type="button" value=" / " onclick="javascript: addElm('{$name}', '/', 'O');" />
  </td>
</tr>

<tr>
  <td>
  <select id="unit_{$name}">
  <option value="">&nbsp;</option>
{foreach key=key item=item from=$taxes_units}
    <option value="{$key}">{$key}{if $key ne $item} ({$item}){/if}</option>
{/foreach}
  </select>&nbsp;
  <input type="button" value="{$lng.lbl_add|strip_tags:false|escape}" onclick="javascript: if(document.getElementById('unit_{$name}').value != '') addElm('{$name}', document.getElementById('unit_{$name}').value, 'V');" />
  </td>
</tr>

<tr> 
  <td>
  <input type="text" id="value_{$name}" />
  <input type="button" size="8" value="{$lng.lbl_add|strip_tags:false|escape}" onclick="javascript: document.getElementById('value_{$name}').value = (isNaN(parseFloat(document.getElementById('value_{$name}').value)) ? '' : Math.abs(parseFloat(document.getElementById('value_{$name}').value))); if (document.getElementById('value_{$name}').value != '') addElm('{$name}', document.getElementById('value_{$name}').value, 'V');" />
  </td> 
</tr> 

</table>
