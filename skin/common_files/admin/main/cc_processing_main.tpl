{*
$Id: cc_processing_main.tpl,v 1.1 2010/05/21 08:31:59 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

{if $processing_module}

  <div class="back-to-payments">
    <a href="{$catalogs.admin}/payment_methods.php">{$lng.lbl_back_to_payment_gateways_page}</a>
  </div>

  {include file=$processing_module}

  {if $module_data.testmode ne "N" and $module_test_mode_description}
    <br /><br />
    <b>{$lng.lbl_testmode_notes}</b><br />
    <div class="cctest-description">{$module_test_mode_description}</div>
  {/if}

  {if $module_data and $module_data.status ne "1"}
    <br />
    <br />

    {capture name=dialog}
      <table cellpadding="2" cellspacing="1" width="100%">
        <tr>
          <td>
            <br />
            {if $module_data.failed_func eq "httpsmod"}
              <font class="AdminTitle">{$lng.txt_no_https_modules_detected}</font>

            {elseif $module_data.failed_func eq "testexec"}
              <font class="AdminTitle">{$lng.txt_file_none_exe_no_exists|substitute:"file":$module_data.failed_param}</font>

            {else}
              <font class="AdminTitle">{$lng.txt_some_requirements_failed}</font>

            {/if}

            &nbsp;&nbsp;&nbsp;
            <a href="{$catalogs.admin}/general.php#PaymentMethods" title="{$lng.lbl_environment_info|escape}">{$lng.lbl_check_environment_link} &gt;&gt;</a>
          </td>
        </tr>
      </table>
    {/capture}
    {include file="dialog.tpl" title=$lng.lbl_warning content=$smarty.capture.dialog extra='width="100%"'}

  {/if}

{/if}
