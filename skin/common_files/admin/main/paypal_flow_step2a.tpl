{*
$Id: paypal_flow_step2a.tpl,v 1.1 2010/05/21 08:31:59 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h2>{$lng.lbl_paypal_choose_solution_cc_and_paypal}</h2>

<div class="step2a">

  <table cellspacing="0" cellpadding="0">

    <tr class="card-title">
      <td>{$lng.lbl_paypal_wps}</td>
      <td>&nbsp;</td>
      <td>{$lng.lbl_paypal_wpp}</td>
    </tr>

    <tr>

      <td class="wps">

        <div class="card-header">
          {$lng.lbl_paypal_easy_started}<br />
          {$lng.lbl_paypal_no_monthly_fee}
          <a href="javascript:popup('http://www.paypal.com/en_US/m/demo/demo_wps/demo_WPS.html',570,365);">{$lng.lbl_paypal_quick_demo}</a>
        </div>
        <div class="card-body">
          <ul class="features">
            <li>{$lng.lbl_paypal_wps_li_1}</li>
            <li>{$lng.lbl_paypal_wps_li_2}</li>
            <li>{$lng.lbl_paypal_wps_li_3}</li>
          </ul>
          <strong>{$lng.lbl_pricing}</strong>
          <ul class="pricing">
            <li>{$lng.lbl_paypal_wps_price_li_1}</li>
            <li>{$lng.lbl_paypal_wps_price_li_2}</li>
            <li>{$lng.lbl_paypal_wps_price_li_3}</li>
          </ul>
          <form action="payment_methods.php" method="post" name="paypalflowstep2a1">
            <input type="hidden" name="mode" value="set_methods" />
            <input type="hidden" name="methods[]" value="paypal" />
            <input type="hidden" name="paypal_solution" value="wps" />

            <button type="submit">{$lng.lbl_select}</button>
          </form>
        </div>

      </td>

      <td class="separator"><img src="{$ImagesDir}/spacer.gif" alt="" /></td>

      <td class="wpp">

        <div class="card-header">
          {$lng.lbl_paypal_advanced_solution}
          <a href="javascript:popup('http://www.paypal.com/en_US/m/demo/wppro/paypal_demo_load_560x355.html',570,365);">{$lng.lbl_paypal_quick_demo}</a>
        </div>
        <div class="card-body">
          <ul class="features">
            <li>{$lng.lbl_paypal_wpp_li_1}</li>
            <li>{$lng.lbl_paypal_wpp_li_2}</li>
            <li>{$lng.lbl_paypal_wpp_li_3}</li>
            <li>{$lng.lbl_paypal_wpp_li_4}</li>
           </ul>
          <strong>{$lng.lbl_pricing}</strong>
          <ul class="pricing">
            <li>{$lng.lbl_paypal_wpp_price_li_1}</li>
            <li>{$lng.lbl_paypal_wpp_price_li_2}</li>
            <li>{$lng.lbl_paypal_wpp_price_li_3}</li>
          </ul>
          <form action="payment_methods.php" method="post" name="paypalflowstep2a2">
            <input type="hidden" name="mode" value="set_methods" />
            <input type="hidden" name="methods[]" value="paypal" />
            <input type="hidden" name="paypal_solution" value="wpp" />

            <button type="submit">{$lng.lbl_select}</button>
          </form>
        </div>

      </td>

    </tr>
  </table>

  <div class="note">{$lng.lbl_paypal_step2_note}</div>
  <a href="payment_methods.php?accept=cc">{$lng.lbl_paypal_full_list_link}</a>

  <div class="buttons-line">
    <button type="button" class="first" onclick="javascript: history.go(-1);">{$lng.lbl_back}</button>
  </div>

</form>
