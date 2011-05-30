{*
$Id: change_states_js.tpl,v 1.3 2010/06/08 06:17:38 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
var txt_no_states = "{$lng.lbl_country_doesnt_require_state|wm_remove|escape:javascript}";
var txt_no_counties = "{$lng.lbl_country_doesnt_require_county|wm_remove|escape:javascript}";
var use_counties = {if $config.General.use_counties eq 'Y'}true{else}false{/if};
var states_sort_override = {if ($config.UA.browser eq 'Opera' and $config.UA.version lt 8) or $config.UA.browser eq 'Safari' or $config.UA.browser eq 'Chrome'}true{else}false{/if};

var config_default_country = "{$config.General.default_country|wm_remove|escape:javascript}";

var countries = {ldelim}{rdelim};
{assign var="cnt" value=0}
{if $countries}
{foreach from=$countries item=v}
countries.{$v.country_code} = {ldelim}states: {if $v.display_states eq 'Y'}[]{else}false{/if}, statesHash: {if $v.display_states eq 'Y'}[]{else}false{/if}{rdelim};

{/foreach}
{/if}

var i;
{if $states ne ''}
i = 0;
{foreach from=$states item=v key=k}
countries.{$v.country_code}.statesHash[i] = {$v.stateid};
countries.{$v.country_code}.states[{$v.stateid}] = {ldelim}code: "{$v.state_code|wm_remove|escape:javascript|replace:"\n":" "}", name: "{$v.state|wm_remove|escape:javascript|replace:"\n":" "}", counties: [], order: i++ {rdelim};

{/foreach}
{/if}

{if $config.General.use_counties eq 'Y' and $counties ne ''}
i = 0;
{foreach from=$counties item=v}
countries.{$v.country_code}.states[{$v.stateid}].counties[{$v.countyid}] = {ldelim}name: "{$v.county|wm_remove|escape:javascript|replace:"\n":" "}", order: i++ {rdelim};
{/foreach}
{/if}

var opera_ini_states_mem = {if $ship2diff}1{else}0{/if};

//]]>
</script>
<script type="text/javascript" src="{$SkinDir}/js/change_states.js"></script>

