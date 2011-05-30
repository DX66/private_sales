{*
$Id: cmpi.tpl,v 1.3 2010/07/26 07:08:26 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<center>{$lng.lbl_cmpi_customer_note}</center><br />
<table width="100%">
<tr>
  <td width="50%" align="center"><img src="{$ImagesDir}/vbv_logo.gif" width="71" height="57" usemap="#vbv" alt="" /></td>
  <td align="center"><img src="{$ImagesDir}/mcsc_logo.gif" width="74" height="40" usemap="#mcsc" alt="" /></td>
</tr>
</table>
<map name="mcsc">
  <area alt="{$lng.lbl_cmpi_mcsc|escape}" coords="8,33,65,42" href="javascript:void(window.open('http://www.mastercardbusiness.com/mcbiz/index.jsp?template=/orphans&amp;content=securecodepopup','MCSC_POPUP','width=600,height=403,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no'));" />
</map>
<map name="vbv">
  <area alt="{$lng.lbl_cmpi_vbv|escape}" coords="2,37,69,53" href="javascript:void(window.open('cmpi_popup.php?type=vbv','VBV_POPUP','width=600,height=403,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no'));" />
</map>
<br />
<center>{$lng.txt_cmpi_customer_message}</center>
<br />
