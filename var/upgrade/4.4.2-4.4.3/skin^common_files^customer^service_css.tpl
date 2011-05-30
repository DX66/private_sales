{*
$Id: service_css.tpl,v 1.2.2.6 2010/10/14 07:29:34 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{load_defer file="css/`$smarty.config.CSSFilePrefix`.css" type="css"}
{if $config.UA.browser eq "MSIE"}
  {assign var=ie_ver value=$config.UA.version|string_format:'%d'}
  {load_defer file="css/`$smarty.config.CSSFilePrefix`.IE`$ie_ver`.css" type="css"}
{/if}

{if $config.UA.browser eq 'Firefox' or $config.UA.browser eq 'Mozilla'}
  {load_defer file="css/`$smarty.config.CSSFilePrefix`.FF.css" type="css"}
{/if}

{if $config.UA.browser eq 'Opera'}
  {load_defer file="css/`$smarty.config.CSSFilePrefix`.Opera.css" type="css"}
{/if}

{if $config.UA.browser eq 'Chrome'}
  {load_defer file="css/`$smarty.config.CSSFilePrefix`.GC.css" type="css"}
{/if}

{load_defer file="lib/cluetip/jquery.cluetip.css" type="css"}

{if $ie_ver ne ''}
<style type="text/css">
<!--
{/if}

{foreach from=$css_files item=files key=mname}
  {foreach from=$files item=f}
    {if ($f.browser eq $config.UA.browser and $f.version eq $config.UA.version) or ($f.browser eq $config.UA.browser and not $f.version) or (not $f.browser and not $f.version) or (not $f.browser)}
      {if $f.suffix}
        {load_defer file="modules/$mname/`$f.subpath``$smarty.config.CSSFilePrefix`.`$f.suffix`.css" type="css" css_inc_mode=$ie_ver}
      {else}
        {load_defer file="modules/$mname/`$f.subpath``$smarty.config.CSSFilePrefix`.css" type="css" css_inc_mode=$ie_ver}
      {/if}
    {/if}
  {/foreach}
{/foreach}

{if $ie_ver ne ''}
-->
</style>
{/if}

{if $AltSkinDir}
  {load_defer file="css/altskin.css" type="css"}
  {if $config.UA.browser eq "MSIE"}
    {load_defer file="css/altskin.IE`$ie_ver`.css" type="css"}
  {/if}
{/if}

{if $custom_styles}
{load_defer file="css/custom_styles" direct_info=$custom_styles type="css"}
{/if}

