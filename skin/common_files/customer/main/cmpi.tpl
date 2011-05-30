{*
$Id: cmpi.tpl,v 1.2 2010/07/26 07:08:26 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<p class="center text-block">{$lng.lbl_cmpi_customer_note}</p>

<div class="cmpi-cc-icons">
  <div class="cmpi-vbv-icon"><img src="{$ImagesDir}/spacer.gif" usemap="#vbv" alt="" /></div>
  <div class="cmpi-mcsc-icon"><img src="{$ImagesDir}/spacer.gif" usemap="#mcsc" alt="" /></div>
  <div class="clearing"></div>
</div>

<map name="mcsc">
  <area alt="{$lng.lbl_cmpi_mcsc|escape}" coords="8,33,65,42" href="javascript:void(window.open('http://www.mastercardbusiness.com/mcbiz/index.jsp?template=/orphans&amp;content=securecodepopup','MCSC_POPUP','width=600,height=403,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no'));" />
</map>
<map name="vbv">
  <area alt="{$lng.lbl_cmpi_vbv|escape}" coords="2,37,69,53" href="javascript:void(window.open('cmpi_popup.php?type=vbv','VBV_POPUP','width=600,height=403,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no'));" />
</map>

<p class="center text-block">{$lng.txt_cmpi_customer_message}</p>
