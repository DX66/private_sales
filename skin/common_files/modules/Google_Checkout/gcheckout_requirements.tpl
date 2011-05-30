{*
$Id: gcheckout_requirements.tpl,v 1.1 2010/05/21 08:32:23 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $gcheckout_requirements ne ''}

<br />

{include file="main/subheader.tpl" title=$lng.lbl_gcheckout_issues_found class="grey"}

{$lng.txt_gcheckout_requirements_failed_note}

<ul>
{foreach key=req_code item=req_data from=$gcheckout_requirements}

{if $req_data.result ne 'Y'}

<li style="padding-bottom:5px">
{if $req_data.result eq 'N'}
<span style="color: #ff0000">[{$lng.lbl_error}]</span>

{elseif $req_data.result eq 'W'}
<span style="color: #0000ff">[{$lng.lbl_warning}]</span>
{/if}

{$req_data.langvar|default:$lng.lbl_unknown}

</li>
{/if}

{/foreach}

</ul>

{/if}

<br />

<input type="button" name="test_callback_url" value="{$lng.lbl_gcheckout_test_callback_url|escape}" onclick="javascript: self.location='test_gcheckout.php?mode=test_callback';" />
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" name="test_gc" value="{$lng.lbl_gcheckout_test_gc|escape}" onclick="javascript: self.location='test_gcheckout.php?mode=test_gc';" />
