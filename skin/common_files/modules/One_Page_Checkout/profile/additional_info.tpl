{*
$Id: additional_info.tpl,v 1.1 2010/05/21 08:32:46 joy Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{if $section ne '' and $additional_fields ne '' and (($is_areas.A eq 'Y' and $section eq 'A') or $section ne 'A')}

  {if not $hide_header and $section eq 'A'}
    <h3>{$lng.lbl_additional_information}</h3>
  {/if}

  <ul{if $first} class="first"{/if}>
  {foreach from=$additional_fields item=v}

    {if $section eq $v.section and $v.avail eq 'Y'}

      <li class="single-field">
        {assign var=oneline value=false}
        {capture name=regfield}
          {if $v.type eq 'T'}
            <input type="text" name="additional_values[{$v.fieldid}]" id="additional_values_{$v.fieldid}" size="32" value="{$v.value|escape}" />

          {elseif $v.type eq 'C'}
            <input type="checkbox" name="additional_values[{$v.fieldid}]" id="additional_values_{$v.fieldid}" value="Y"{if $v.value eq 'Y'} checked="checked"{/if} />
            {assign var=oneline value=true}

          {elseif $v.type eq 'S'}
            <select name="additional_values[{$v.fieldid}]" id="additional_values_{$v.fieldid}">
              {foreach from=$v.variants item=o}
                <option value='{$o|escape}'{if $v.value eq $o} selected="selected"{/if}>{$o|escape}</option>
              {/foreach}
            </select>
            {assign var=oneline value=true}
          {/if}
        {/capture}
        {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield required=$v.required name=$v.title oneline=$oneline field="additional_values_`$v.fieldid`"}

      </li>

    {/if}

  {/foreach}
  </ul>

{/if}
