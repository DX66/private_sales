{*
$Id: cc_test.tpl,v 1.1 2010/05/21 08:32:53 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>X-CART TEST</h1>
{$lng.txt_cc_test_top_text}<br />
<br />

{capture name=dialog}

  <form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

    <table cellspacing="10">

      <tr>
        <td>{$lng.lbl_cc_test_merchantid}:</td>
        <td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
      </tr>

      <tr>
        <td>{$lng.lbl_cc_order_prefix}:</td>
        <td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
      </tr>

      <tr>
        <td>{$lng.lbl_use_preauth_method}:</td>
        <td>
          <select name="use_preauth">
            <option value="">{$lng.lbl_auth_and_capture_method}</option>
            <option value="Y"{if $module_data.use_preauth eq 'Y'} selected="selected"{/if}>{$lng.lbl_auth_method}</option>
          </select>
        </td>
      </tr>

    </table>

    <br />
    <br />

    <input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />

  </form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
