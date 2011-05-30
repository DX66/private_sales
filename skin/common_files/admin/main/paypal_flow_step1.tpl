{*
$Id: paypal_flow_step1.tpl,v 1.2 2010/07/20 06:47:40 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h2>{$lng.lbl_paypal_setup_cc_processing}</h2>

<div class="step1">
  <form action="payment_methods.php" method="get" name="paypalflowstep1">

    <ul>

      <li class="first">
        <input type="radio" name="accept" value="paypal" id="action_paypal" checked="checked" /><label for="action_paypal">{$lng.lbl_paypal_accept_cc_and_paypal}</label>
        <img src="{$ImagesDir}/cards_line_paypal.png" alt="" class="cc-line-paypal" />
      </li>

      <li class="last">
        <input type="radio" name="accept" value="cc" id="action_cc" /><label for="action_cc">{$lng.lbl_paypal_accept_cc}</label>
        <img src="{$ImagesDir}/cards_line.png" alt="" class="cc-line" />
      </li>

    </ul>

    <div class="note">{$lng.lbl_note}: {$lng.lbl_paypal_step1_note}</div>
    <a href="payment_methods.php?mode=finalize">{$lng.lbl_paypal_list_other_link}</a>

    <div class="buttons-line">
      <button type="submit" class="first">{$lng.lbl_next}</button>
    </div>

  </form>
</div>
