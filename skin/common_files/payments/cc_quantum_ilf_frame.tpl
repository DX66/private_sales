{*
$Id: cc_quantum_ilf_frame.tpl,v 1.1 2010/05/21 08:32:53 joy Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<iframe src="{$ilf_src}" height="500" width="800" frameborder="0" onload="return frameLoaded();"></iframe>

<script type="text/javascript">
{literal}
function refreshSession(k, ip) {
	if (!k || !ip)
		return false;

  var post_url = 'cc_quantum_ilf.php?frame_refresh=' + Math.random();

  var data = {
    ip: ip,
    k: k
  };

	var request = {
    type: 'POST',
    url: post_url,
    data: data
  };

	return ajax.query.add(request)
}
{/literal}

setInterval("refreshSession('{$ilf_key}', '{$ilf_ip}')", 20000);
var msg_confirmation = '{$lng.msg_payment_cancel_confirmation_js|wm_remove|escape:"javascript"}';
</script>

<div align="center">
  {include file="customer/buttons/button.tpl" button_title=$lng.lbl_cancel href="javascript:if(confirm(msg_confirmation))window.location='`$cancel_url`'" additional_button_class="main-button" js_to_href="Y"}
</div>
