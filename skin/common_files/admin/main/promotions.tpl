{*
$Id: promotions.tpl,v 1.3 2010/06/08 06:17:38 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_quick_start}

<br />

<!-- QUICK MENU -->

{include file="main/quick_menu.tpl"}

<!-- QUICK MENU -->

<a name="qs"></a>
{capture name=dialog}

<div align="justify">{$lng.txt_how_setup_store}</div>

{literal}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
var url =  document.URL;
var re = /^https/;
if ( !url.match(re) ) {
  document.write("<img src=\"http://www.x-cart.com/img/background.gif\" width=\"1\" height=\"1\" alt=\"\" />");
}
//]]>
</script>
{/literal}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_quick_start_text content=$smarty.capture.dialog extra='width="100%"'}
<br />

