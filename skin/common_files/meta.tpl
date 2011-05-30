{*
$Id: meta.tpl,v 1.14.2.2 2011/03/03 10:53:08 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<meta http-equiv="Content-Type" content="text/html; charset={$default_charset|default:"iso-8859-1"}" />
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Language" content="{if ($usertype eq "P" or $usertype eq "A") and $current_language ne ""}{$current_language|escape}{else}{$store_language|escape}{/if}" />
<meta name="ROBOTS" content="NOINDEX,NOFOLLOW" />
{if $__frame_not_allowed}
<script type="text/javascript">
//<![CDATA[
if (top != self)
  top.location = self.location;
//]]>
</script>
{/if}
{include file="presets_js.tpl"}
<script type="text/javascript" src="{$SkinDir}/js/common.js"></script>
{if $config.Adaptives.is_first_start eq 'Y'}
<script type="text/javascript" src="{$SkinDir}/js/browser_identificator.js"></script>
{/if}
{if $webmaster_mode eq "editor"}
<script type="text/javascript">
//<![CDATA[
var store_language = "{if ($usertype eq "P" or $usertype eq "A") and $current_language ne ""}{$current_language|escape:javascript}{else}{$store_language|escape:javascript}{/if}";
var catalogs = new Object();
catalogs.admin = "{$catalogs.admin}";
catalogs.provider = "{$catalogs.provider}";
catalogs.customer = "{$catalogs.customer}";
catalogs.partner = "{$catalogs.partner}";
catalogs.images = "{$ImagesDir}";
catalogs.skin = "{$SkinDir}";
var lng_labels = [];
{foreach key=lbl_name item=lbl_val from=$webmaster_lng}
lng_labels['{$lbl_name}'] = '{$lbl_val}';
{/foreach}
var page_charset = "{$default_charset|default:"iso-8859-1"}";
//]]>
</script>
<script type="text/javascript" language="JavaScript 1.2" src="{$SkinDir}/js/editor_common.js"></script>
{if $user_agent eq "ns"}
<script type="text/javascript" language="JavaScript 1.2" src="{$SkinDir}/js/editorns.js"></script>
{else}
<script type="text/javascript" language="JavaScript 1.2" src="{$SkinDir}/js/editor.js"></script>
{/if}
{/if}
{if $active_modules.Magnifier ne ""}
<script type="text/javascript" src="{$SkinDir}/lib/swfobject-min.js"></script>
{/if}

<script type="text/javascript" src="{$SkinDir}/lib/jquery-min.js"></script>
<script type="text/javascript" src="{$SkinDir}/lib/cluetip/jquery.cluetip.js"></script>
<link rel="stylesheet" type="text/css" href="{$SkinDir}/lib/cluetip/jquery.cluetip.css" />

<!--[if lt IE 7]>
<script type="text/javascript" src="{$SkinDir}/js/iepngfix.js"></script>
<![endif]-->
{if $gmap_enabled}
<script type="text/javascript">
//<![CDATA[
var gmapGeocodeError="{$lng.lbl_gmap_geocode_error}";
var lbl_close="{$lng.lbl_close}";
//]]>
</script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="{$SkinDir}/js/gmap.js"></script>
<script type="text/javascript" src="{$SkinDir}/js/modal.js"></script>
{/if}

{include file="jquery_ui.tpl"}

<script type="text/javascript">
//<![CDATA[
var md = {$config.Appearance.delay_value|default:10}*1000;
{literal}
$(document).ready( function() {
  $('form').not('.skip-auto-validation').each( function() {
    applyCheckOnSubmit(this);
  });

  $("input:submit, input:button, button, a.simple-button").button();

  $(".top-message-info").fadeIn('slow').delay(md).fadeOut('slow');
});

{/literal}
//]]>
</script>

{load_defer file="js/ajax.js" type="js"}
{load_defer file="js/popup_open.js" type="js"}
{load_defer file="lib/jquery.blockUI.js" type="js"}
{load_defer file="lib/jquery.blockUI.defaults.js" type="js"}

{load_defer file="js/sticky.js" type="js"}

{load_defer_code type="css"}
{load_defer_code type="js"}
