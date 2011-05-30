{*
$Id: ga_code.tpl,v 1.3 2010/06/08 06:17:40 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $ga_track_commerce neq "Y" or $ga_init eq "Y"}
<script type="text/javascript">
//<![CDATA[[CDATA[
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
//]]>
</script>

<script type="text/javascript">
//<![CDATA[[CDATA[
try{ldelim}
var pageTracker = _gat._getTracker("{$config.Google_Analytics.ganalytics_code}");
pageTracker._trackPageview();
{rdelim} catch(err) {ldelim}{rdelim}
//]]>
</script>
{if $active_modules.Google_Checkout ne ""}
<script src="{if $current_location eq $http_location}http{else}https{/if}://checkout.google.com/files/digital/ga_post.js" type="text/javascript"></script>
{/if}
{/if}
