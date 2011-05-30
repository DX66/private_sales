{*
$Id: payment_methods.tpl,v 1.1.2.4 2011/02/18 08:15:06 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if not $use_paypal_flow}
  {include file="page_title.tpl" title=$lng.lbl_payment_methods}
{/if}

<br />

{$lng.txt_payment_methods_top_text}<br />
<br />

{$lng.txt_payment_methods_top_text_order_prefix}<br />
<br />

{if not $is_paypal_exists}
  {include file="admin/main/paypal_pec.tpl"}
{/if}

{if $recent_payment_methods}

  {capture name=dialog}

    {$lng.txt_recent_payment_methods}<br />
    <br />

    <table cellpadding="4" cellspacing="1">

      {foreach from=$recent_payment_methods item=rpm name=rpm}

        <tr{interline class="TableSubHead" name=rpm}>
          <td align="left"><a href="cc_processing.php?mode=update&amp;cc_processor={$rpm.script}">{$rpm.name}</a></td>
          <td><a href="cc_processing.php?mode=update&amp;cc_processor={$rpm.script}">{$lng.lbl_configure}</a></td>
          <td class="delete-icon"><a href="cc_processing.php?mode=delete&amp;paymentid={$rpm.paymentid}" class="delete-link">{$lng.lbl_delete}</a></td>
          <td style="padding-left: 20px;">
            {capture name=tt assign=ttitle}
              {if $rpm.background eq 'Y'}
                {$lng.lbl_payment_background}
              {elseif $rpm.background eq 'I'}
                {$lng.lbl_payment_iframe}
              {else}
                {$lng.lbl_payment_webbased}
                {assign var="has_webbased" value=true}
              {/if}
            {/capture}
            {include file="main/tooltip_js.tpl" text=$lng.txt_payment_methods_types title=$ttitle id=pglnk_`$rpm.paymentid`}
          </td>
        </tr>

      {/foreach}

    </table>
    
    {if $has_webbased and $smarty.foreach.rpm.total gt 4}
      {$lng.txt_payment_methods_add_pg_ips|substitute:"target":"_blank"}
    {elseif $has_webbased}
      {$lng.txt_payment_methods_add_pg_ips|substitute:"target":"_self"}
    {/if}

  {/capture}

  {include file="dialog.tpl" title=$lng.lbl_recent_payment_methods content=$smarty.capture.dialog extra='width="100%"'}

  <br />
  <br />

{/if}

{capture name=dialog}

  <a name="payment_methods"></a>
  {include file="main/language_selector.tpl" script="payment_methods.php?"}

  {include file="main/check_all_row.tpl" style="line-height: 170%;" form="pmform" prefix="posted_data.+active"}

  <form action="payment_methods.php" method="post" name="pmform">
    <input type="hidden" name="mode" value="update" />

    <table cellpadding="5" cellspacing="1" width="100%">

    <tr class="TableHead">
      <th>&nbsp;</th>
      <th width="40%">{$lng.lbl_methods}</th>
      <th width="15%" nowrap="nowrap">{$lng.lbl_special_instructions}</th>
      <th width="25%">{$lng.lbl_protocol}</th>
      <th width="10%">{$lng.lbl_membership}</th>
      {if $active_modules.Anti_Fraud}
        <th width="10%" nowrap="nowrap">{$lng.lbl_check}*</th>
      {/if}
      <th width="10%">{$lng.lbl_pos}</th>
    </tr>

    {section name=method loop=$payment_methods}

      {if $payment_methods[method].active eq "Y"}
        {cycle name="active_payments" values=" class='TableSubHeadPayment2', class='TableSubHeadPayment1'" assign=active_trcolor}
      {elseif $active_trcolor eq " class='TableSubHeadPayment2'"}
        {cycle name="inactive_payments" values=' class="TableSubHead",' assign=inactive_trcolor}
      {else}
        {cycle name="inactive_payments" values=', class="TableSubHead"' assign=inactive_trcolor}
      {/if}

      <tr{if $payment_methods[method].active eq "Y"}{$active_trcolor}{else}{$inactive_trcolor}{/if}>

        <td valign="top"{if $payment_methods[method].module_name ne ""} rowspan="2"{/if}>

          {if $payment_methods[method].disable_checkbox}
            <input type="hidden" name="posted_data[{$payment_methods[method].paymentid}][active]" value="Y" />
          {/if}

          <input type="checkbox" name="posted_data[{$payment_methods[method].paymentid}][active]" value="Y"{if $payment_methods[method].active eq "Y"} checked="checked"{/if}{if $payment_methods[method].disable_checkbox} disabled="disabled"{/if}{if $payment_methods[method].control_checkbox} onclick="javascript: markDisabledCB(this);"{/if} />

        </td>

        <td valign="top">

          <input type="text" size="30" name="posted_data[{$payment_methods[method].paymentid}][payment_method]" value="{$payment_methods[method].payment_method|escape:"html"}" />
          <br />
          {if $payment_methods[method].payment_script ne 'payment_giftcert.php'}
            <table cellpadding="1" cellspacing="0">
              <tr>
                <td class="FormButton">{$lng.lbl_cod_extra_charge}:</td>
                <td><input type="text" size="8" name="posted_data[{$payment_methods[method].paymentid}][surcharge]" value="{$payment_methods[method].surcharge|default:"0"|formatprice}" /></td>
                <td>
                  <select name="posted_data[{$payment_methods[method].paymentid}][surcharge_type]">
                    <option value="%"{if $payment_methods[method].surcharge_type eq "%"} selected="selected"{/if}>%</option>
                    <option value="$"{if $payment_methods[method].surcharge_type eq "$"} selected="selected"{/if}>{$config.General.currency_symbol}</option>
                  </select>
                </td>
              </tr>
            </table>
            {if $payment_methods[method].processor_file eq ""}
              <table cellpadding="1" cellspacing="0">
                <tr>
                  <td><input type="checkbox" id="is_cod_{$payment_methods[method].paymentid}" name="posted_data[{$payment_methods[method].paymentid}][is_cod]" value="Y"{if $payment_methods[method].is_cod eq 'Y'} checked="checked"{/if} /></td>
                  <td class="FormButton"><label for="is_cod_{$payment_methods[method].paymentid}">{$lng.lbl_cash_on_delivery_method}</label></td>
                </tr>
              </table>
            {/if}
          {/if}

      </td>

      <td valign="top" nowrap="nowrap">
        <textarea name="posted_data[{$payment_methods[method].paymentid}][payment_details]" cols="20" rows="3">{$payment_methods[method].payment_details|escape:"html"}</textarea>
      </td>

      <td valign="top">
        <select name="posted_data[{$payment_methods[method].paymentid}][protocol]" style="width:100%">
          <option value="http"{if $payment_methods[method].protocol eq "http"} selected="selected"{/if}>HTTP</option>
          <option value="https"{if $payment_methods[method].protocol eq "https"} selected="selected"{/if}>HTTPS</option>
        </select>
      </td>

      <td valign="top"{if $payment_methods[method].module_name ne ""} rowspan="2"{/if}>
        {include file="main/membership_selector.tpl" field="posted_data[`$payment_methods[method].paymentid`][membershipids][]" data=$payment_methods[method] is_short="Y"}
      </td>

      {if $active_modules.Anti_Fraud}
        <td valign="top"{if $payment_methods[method].module_name ne ""} rowspan="2"{/if}>
          <input type="checkbox" name="posted_data[{$payment_methods[method].paymentid}][af_check]" value="Y"{if $payment_methods[method].af_check eq 'Y'} checked="checked"{/if} />
        </td>
      {/if}
      <td valign="top"{if $payment_methods[method].module_name ne ""} rowspan="2"{/if}>
        <input type="text" size="2" maxlength="5" name="posted_data[{$payment_methods[method].paymentid}][orderby]" value="{$payment_methods[method].orderby}"{if $payment_methods[method].disable_checkbox} disabled="disabled"{/if}{if $payment_methods[method].control_checkbox} onchange="javascript: changeDisabledOrderBy(this);"{/if} />
      </td>
    </tr>

    {if $payment_methods[method].module_name ne ""}
      <tr{if $payment_methods[method].active eq "Y"}{$active_trcolor}{else}{$inactive_trcolor}{/if}>
        <td colspan="3" valign="bottom">
          {if $payment_methods[method].type eq "C"}
            {$lng.lbl_credit_card_processor}
          {elseif $payment_methods[method].type eq "H"}
            {$lng.lbl_check_processor}
          {else}
            {assign var=type value="ps"}
            {$lng.lbl_ps_processor}
          {/if}
          <strong>{$payment_methods[method].module_name}</strong>:
            <a href="cc_processing.php?mode=update&amp;cc_processor={$payment_methods[method].processor}">{$lng.lbl_configure}</a>
          {if not $payment_methods[method].disable_checkbox}
          | <a href="cc_processing.php?mode=delete&amp;paymentid={$payment_methods[method].paymentid}">{$lng.lbl_delete}</a>
          {/if}

          {if not $payment_methods[method].disable_checkbox}

            {if $payment_methods[method].is_down or $payment_methods[method].in_testmode}
              <table cellpadding="2">
                {if $payment_methods[method].is_down}
                  <tr>
                    <td><img src="{$ImagesDir}/icon_warning_small.gif" alt="" /></td>
                    <td>
                      <font class="AdminSmallMessage">
                        {if $payment_methods[method].down_lbl}
                          {$payment_methods[method].down_lbl}
                        {else}
                          {$lng.txt_cc_processor_requirements_failed|substitute:"processor":$payment_methods[method].module_name}
                        {/if}
                      </font>
                    </td>
                  </tr>
                {/if}
                {if $payment_methods[method].in_testmode}
                  <tr>
                    <td><img src="{$ImagesDir}/icon_warning_small.gif" alt="" /></td>
                    <td><font class="AdminSmallMessage">{$lng.txt_cc_processor_in_text_mode|substitute:"processor":$payment_methods[method].module_name}</font></td>
                  </tr>
                {/if}
              </table>
            {/if}

          {/if}

          </td>
        </tr>
      {/if}

    {/section}

    <tr>
      <td colspan="{if $active_modules.Anti_Fraud}7{else}6{/if}" class="main-button">
        <div id="sticky_content">
          <input type="submit" class="big-main-button" value="{$lng.lbl_apply_changes|strip_tags:false|escape}" />
        </div>
      </td>
    </tr>

    {if $active_modules.Anti_Fraud}
      <tr>
        <td colspan="7" style="padding-top: 10px;">*) {$lng.txt_af_payment_method_note}</td>
      </tr>
    {/if}

  </table>

  <br />
  <br />
  <a name='section_force_offline_paymentid'></a>
  {getvar var=offline_payment_methods}
  {if $offline_payment_methods ne ''}
  <table cellpadding="2" cellspacing="1" width="100%">
    <tr>
      <td nowrap="nowrap"><strong>{$lng.lbl_note_for_zero_cost_orders}</strong></td>
      <td align="center" width="100%">
        <select name="force_offline_paymentid" style="width: 100%;">
          {foreach from=$offline_payment_methods item=v key=k}
          <option value='{$k}'{if $config.Egoods.force_offline_paymentid eq $k} selected="selected"{/if}>{$v|escape}</option>
          {/foreach}
        </select>
      </td>  
      <td nowrap="nowrap">{include file="main/tooltip_js.tpl" type="img" id="what_is_force_offline_paymentid" text=$lng.opt_descr_force_offline_paymentid}</td>
      <td nowrap="nowrap">
        {include file="buttons/button.tpl" button_title=$lng.lbl_set_val href="javascript: document.pmform.mode.value='change_force_offline_paymentid';document.pmform.submit();"}
      </td>
    </tr>  
  </table>

  {/if}

  </form>

{/capture}

{include file="dialog.tpl" title=$lng.lbl_payment_methods content=$smarty.capture.dialog extra='width="100%"'}

<br />
<br />

{include file="admin/main/cc_processing.tpl"}

<script type="text/javascript" src="{$SkinDir}/js/payment_methods.js"></script>
