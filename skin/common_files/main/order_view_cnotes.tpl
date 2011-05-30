{*
$Id: order_view_cnotes.tpl,v 1.4 2010/07/21 10:00:16 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{$lng.txt_site_title}</title>
{include file="meta.tpl"}
<style type="text/css">
//<![CDATA[
{literal}
* {
  padding: 0px;
  margin: 0px;
  font-family: Verdana, Arial, Helvetica, Sans-serif;
  color: #2c3e49;
  font-size: 10px;
}
{/literal}
//]]>
</style>
</head>
<body{$reading_direction_tag}>

<form action="order.php" method="post" name="cnotesform">
  <input type="hidden" name="mode" value="cnote" />
  <input type="hidden" name="orderid" value="{$order.orderid}" />

  <textarea name="customer_notes" cols="70" rows="8" style="width: 520px; height: 150px;">{$order.customer_notes|escape}</textarea>
</form>

<script type="text/javascript">
//<![CDATA[
if (top == self)
  self.location = 'order.php?orderid={$order.orderid}';
//]]>
</script>

</body>
</html>
