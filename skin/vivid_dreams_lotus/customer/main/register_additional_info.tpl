{*
$Id: register_additional_info.tpl,v 1.1 2010/05/21 08:33:04 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $section ne '' and $additional_fields ne '' and (($is_areas.A eq 'Y' and $section eq 'A') or $section ne 'A')}

  {if $hide_header eq "" and $section eq 'A'}
    <tr>
      <td colspan="3" class="register-section-title">
        <div>
          <label>{$lng.lbl_additional_information}</label>
        </div>
      </td>
    </tr>
  {/if}

  {foreach from=$additional_fields item=v}
    {if $section eq $v.section and $v.avail eq 'Y'}
      <tr>
        <td class="data-name"><label for="additional_values_{$v.fieldid}">{$v.title|default:$v.field}</label></td>
        <td{if $v.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
        <td>

          {if $v.type eq 'T'}
            <input type="text" class="exclude-style" name="additional_values[{$v.fieldid}]" id="additional_values_{$v.fieldid}" size="32" value="{$v.value|escape}" />

          {elseif $v.type eq 'C'}
            <input type="checkbox" name="additional_values[{$v.fieldid}]" id="additional_values_{$v.fieldid}" value="Y"{if $v.value eq 'Y'} checked="checked"{/if} />

          {elseif $v.type eq 'S'}
            <select name="additional_values[{$v.fieldid}]" id="additional_values_{$v.fieldid}">
              {foreach from=$v.variants item=o}
                <option value='{$o|escape}'{if $v.value eq $o} selected="selected"{/if}>{$o|escape}</option>
              {/foreach}
            </select>
          {/if}
        </td>
      </tr>
    {/if}
  {/foreach}

{/if}
