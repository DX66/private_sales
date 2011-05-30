{*
$Id: banner_html_code.tpl,v 1.1 2010/05/21 08:32:16 joy Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}

<table cellspacing="1" cellpadding="0" bgcolor="#000000">
  <tr bgcolor="#ffffff">
    <td>{include file="main/display_banner.tpl" assign="ban" type=$local_type partner='' test_area=true}{$ban|trim|amp}</td>
</tr>
</table>

<br />
<br />

<strong>{$lng.lbl_iframe_code}:</strong><br />
<textarea cols="100" rows="3" readonly="readonly">{include file="main/display_banner.tpl" assign="ban" type="iframe" partner=$partner }{$ban|trim|escape}</textarea><br />
<br />

<strong>{$lng.lbl_javascript_version}:</strong><br />
<textarea cols="100" rows="3" readonly="readonly">{include file="main/display_banner.tpl" assign="ban" type="js" partner=$partner }{$ban|trim|escape}</textarea><br />
<br />

<strong>{$lng.lbl_ssi_version}:</strong><br />
<textarea cols="100" rows="3" readonly="readonly">{include file="main/display_banner.tpl" assign="ban" type="ssi" partner=$partner }{$ban|trim|escape}</textarea><br />
<br />

<strong>{$lng.lbl_html_code}:</strong><br />
<textarea cols="100" rows="7" readonly="readonly">{include file="main/display_banner.tpl" assign="ban" type="html" partner=$partner }{$ban|strip|trim|escape}</textarea><br />

