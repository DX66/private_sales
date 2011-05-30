{*
$Id: rectangle_bottom.tpl,v 1.3 2010/06/08 06:17:38 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
</td>
</tr>

<tr>
  <td class="BottomRow">
    {include file="bottom.tpl"}
  </td>
</tr>

</table>
{if $config.UA.browser eq "MSIE" and ($config.UA.version eq "6.0" or $config.UA.version eq "7.0")}
<script type="text/javascript">
//<![CDATA[
{literal}
$("#horizontal-menu li").hover(
  function () {
    $(this).find("div").toggleClass('horizontal-menu-li-hover-div');
  }
);
$("#horizontal-menu").css('top', '49px');
{/literal}
//]]>
</script>
{/if}
