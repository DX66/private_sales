{*
$Id: payment_info.tpl,v 1.4 2010/07/23 14:29:48 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset={$default_charset|default:"iso-8859-1"}" />
  <meta http-equiv="X-UA-Compatible" content="IE=8" />
  <title>{$lng.txt_xpc_payment_info}</title>
  {include file="meta.tpl"}
  <link rel="stylesheet" type="text/css" href="{$SkinDir}/css/skin1_admin.css" />
  <link rel="stylesheet" type="text/css" href="{$SkinDir}/modules/XPayments_Connector/payment_info.css" />
</head>
<body class="xpc-payment-info" {$reading_direction_tag}>

<h1>{$lng.txt_xpc_transaction_list}</h1>
<table cellspacing="0" class="xpc-trans-list">

  <tr>
    <th>{$lng.txt_xpc_date_time}</th>
    <th>{$lng.txt_xpc_type}</th>
    <th>{$lng.txt_xpc_result_payment_status}</th>
    <th>{$lng.txt_xpc_transaction_result}</th>
    <th>{$lng.lbl_total}</th>
  </tr>

  {foreach from=$data.transactions item=t}

    <tr class="xpc-tran-separator">
      <td colspan="5">&nbsp;</td>
    </tr>

    <tr class="xpc-tran-header">
      <td class="xpc-date-cell">
        <strong>{$t.date|date_format:'%h %d, %Y'}</strong>
        {$t.date|date_format:'%H:%M:%S'}
      </td>
      <td class="xpc-type-cell">{$t.action}</td>
      <td class="xpc-status-cell">{$t.payment_status}</td>
      <td class="xpc-result-cell xpc-result-{$t.status|lower|replace:' ':'-'}">{$t.status}</td>
      <td class="xpc-total-cell last">{$t.total}</td>
    </tr>

    {if $t.message or $t.txnid or $t.fields}
      <tr>
        <td colspan="5" class="xpc-message-cell">

          <table cellspacing="0" class="xpc-tran-data">

            {if $t.message}
              <tr>
                <td class="xpc-tran-field-name">{$lng.txt_xpc_message}:</td>
                <td>{$t.message}</td>
              </tr>
            {/if}

            {if $t.txnid}
              <tr>
                <td class="xpc-tran-field-name">{$lng.txt_xpc_txnid}:</td>
                <td>{$t.txnid}</td>
              </tr>
            {/if}

            {if $t.fields}
              <tr>
                <td class="xpc-tran-field-name">{$lng.txt_xpc_other_info}:</td>
                <td>
  
                  {list2matrix assign="fields" assign_width="cell_width" list=$t.fields row_length=2}
                  <table cellspacing="0" class="xpc-tran-fields">

                    {foreach from=$fields item=row}
                      <tr>
                        {foreach from=$row item=field}  

                          {if $field}
                            <td class="xpc-tran-key xpc-tran-field-name">{$field.name}:</td>
                            <td class="xpc-tran-value">{$field.value}</td>
                          {else}
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                          {/if}

                        {/foreach}
                      </tr>

                    {/foreach}
                  </table>

                </td>
              </tr>
            {/if}

          </table>

        </td>
      </tr>
    {/if}

  {/foreach}

</table>

</body>
</html>
