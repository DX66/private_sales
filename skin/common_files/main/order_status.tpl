{*
$Id: order_status.tpl,v 1.1 2010/05/21 08:32:17 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $extended eq "" and $status eq ""}

{$lng.lbl_wrong_status}

{elseif $mode eq "select"}

<select name="{$name}" {$extra}>
{if $extended ne ""}
  <option value="">&nbsp;</option>
{/if}
  <option value="I"{if $status eq "I"} selected="selected"{/if}>{$lng.lbl_not_finished}</option>
  <option value="Q"{if $status eq "Q"} selected="selected"{/if}>{$lng.lbl_queued}</option>
  {if $status eq "A" or $display_preauth}<option value="A"{if $status eq 'A'} selected="selected"{/if}>{$lng.lbl_pre_authorized}</option>{/if}
  <option value="P"{if $status eq "P"} selected="selected"{/if}>{$lng.lbl_processed}</option>
  <option value="B"{if $status eq "B"} selected="selected"{/if}>{$lng.lbl_backordered}</option>
  <option value="D"{if $status eq "D"} selected="selected"{/if}>{$lng.lbl_declined}</option>
  <option value="F"{if $status eq "F"} selected="selected"{/if}>{$lng.lbl_failed}</option>
  <option value="C"{if $status eq "C"} selected="selected"{/if}>{$lng.lbl_complete}</option>
</select>

{elseif $mode eq "static"}

{if $status eq "I"}
{$lng.lbl_not_finished}

{elseif $status eq "Q"}
{$lng.lbl_queued}

{elseif $status eq "A"}
{$lng.lbl_pre_authorized}

{elseif $status eq "P"}
{$lng.lbl_processed}

{elseif $status eq "D"}
{$lng.lbl_declined}

{elseif $status eq "B"}
{$lng.lbl_backordered}

{elseif $status eq "F"}
{$lng.lbl_failed}

{elseif $status eq "C"}
{$lng.lbl_complete}

{/if}

{/if}
