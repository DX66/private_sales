{*
$Id: choosing_options_list.tpl,v 1.1 2010/05/21 08:32:21 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.lbl_X_features|substitute:product_type:$current_class}</h1>

<p class="text-block">{$lng.txt_choosing_by_features_note_step2}</p>
 
{capture name=dialog} 

  <p>
    <strong>{$lng.lbl_note}:</strong>&nbsp;{$lng.txt_not_necessary_fill_all_fields}
  </p>

  <form action="choosing.php" method="post" name="choosingoptionsform" onsubmit="javascript: return skipDefaultValue(this);">
    <input type="hidden" name="mode" value="select_options" />

    <table cellspacing="0" class="data-table width-100 fcomp-options-table" summary="{$lng.lbl_select_features|escape}">

      {foreach from=$options item=v}
        <tr>
          <td class="data-name">
          {include file="modules/Feature_Comparison/option_hint.tpl" opt=$v}
          </td>
          <td class="data-width-100">

            {if $v.option_type eq 'S' or  $v.option_type eq 'M'}

              {if $v.option_type eq 'S'}
                <select name="options[{$v.foptionid}]">
                  <option value="">{$lng.lbl_ignored}</option>
              {else}
                <select name="options[{$v.foptionid}][]" multiple="multiple" size="5">
              {/if}

              {foreach from=$v.variants item=vv}
                <option value="{$vv.fvariantid}"{if $v.option_type eq 'M'}{foreach from=$choosing.options[$v.foptionid] item=s}{if $s eq $vv.fvariantid} selected="selected"{/if}{/foreach}{else}{if $choosing.options[$v.foptionid] eq $vv.fvariantid and $choosing.options[$v.foptionid]|cat:"|" ne '|'} selected="selected"{/if}{/if}>{$vv.variant_name}</option>
              {/foreach}

              </select>

              {if $v.option_type eq 'M'}
                <br />
                {$lng.lbl_hold_ctrl_key}
              {/if}

            {elseif $v.option_type eq 'N'}

              <input type="text" name="options[{$v.foptionid}][0]" maxlength="11" size="11" value="{$choosing.options[$v.foptionid].0|default:$lng.lbl_from}"{if not $choosing.options[$v.foptionid].0} class="default-value"{/if} />
              &nbsp;-&nbsp;
              <input type="text" name="options[{$v.foptionid}][1]" maxlength="11" size="11" value="{$choosing.options[$v.foptionid].1|default:$lng.lbl_to}"{if not $choosing.options[$v.foptionid].1} class="default-value"{/if} />

            {elseif $v.option_type eq 'D'}

              <table cellspacing="0" cellpadding="0" summary="{$v.option_name|escape}">
                {if $choosing.options[$v.foptionid].0.Date_Year ne 0}
                  {assign var="cur_time" value=$choosing.options[$v.foptionid].0}
                {else}
                  {assign var="cur_time" value="--"}
                {/if} 
                <tr>
                  <td>{$lng.lbl_from}:</td>
                  <td>{html_select_date field_array="options[`$v.foptionid`][0]" start_year="-5" year_empty=$lng.lbl_ignored|wm_remove month_empty=$lng.lbl_ignored|wm_remove day_empty=$lng.lbl_ignored|wm_remove time=$cur_time}</td>
                </tr>

                {if $choosing.options[$v.foptionid].1.Date_Year ne 0}
                  {assign var="cur_time" value=$choosing.options[$v.foptionid].1}
                {else}
                  {assign var="cur_time" value="--"}
                {/if} 
                <tr>
                  <td>{$lng.lbl_through}:</td>
                  <td>{html_select_date field_array="options[`$v.foptionid`][1]" start_year="+5" year_empty=$lng.lbl_ignored|wm_remove month_empty=$lng.lbl_ignored|wm_remove day_empty=$lng.lbl_ignored|wm_remove time=$cur_time}</td>
                </tr>
              </table>

            {elseif $v.option_type eq 'B'}
              <select name="options[{$v.foptionid}]">
                <option value=""{if $choosing.options[$v.foptionid] eq ""} selected="selected"{/if}>{$lng.lbl_ignored}</option>
                <option value="Y"{if $choosing.options[$v.foptionid] eq "Y"} selected="selected"{/if}>{$lng.lbl_yes}</option>
                <option value="N"{if $choosing.options[$v.foptionid] eq "N"} selected="selected"{/if}>{$lng.lbl_no}</option>
               </select>

            {else}
              <input type="text" size="40" name="options[{$v.foptionid}]" value="{$choosing.options[$v.foptionid]}" /><br />
                <div class="fcomp-options-label-including">
                <label>
                  <input type="radio" name="including[{$v.foptionid}]" value="all"{if $choosing.including[$v.foptionid] eq "all" or $choosing.including[$v.foptionid] eq ""} checked="checked"{/if} />
                  {$lng.lbl_all_word}
                </label>
&nbsp;
                <label>
                  <input type="radio" name="including[{$v.foptionid}]" value="any"{if $choosing.including[$v.foptionid] eq "any"} checked="checked"{/if}  />
                  {$lng.lbl_any_word}
                </label>
&nbsp;
                <label>
                  <input type="radio" name="including[{$v.foptionid}]" value="phrase" {if $choosing.including[$v.foptionid] eq "phrase"} checked="checked"{/if} />
                  {$lng.lbl_exact_phrase}
                </label>
                </div>
            {/if}

           </td>
        </tr>
      {/foreach}

      <tr>
        <td>&nbsp;</td>
        <td class="button-row">
          {include file="customer/buttons/search.tpl" type="input"}
        </td>
      </tr>
    </table>
  </form>

{/capture} 
{include file="customer/dialog.tpl" title=$section_head|default:$lng.lbl_select_features content=$smarty.capture.dialog noborder=true}
