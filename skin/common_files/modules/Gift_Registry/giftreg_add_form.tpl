{*
$Id: giftreg_add_form.tpl,v 1.1 2010/05/21 08:32:23 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="giftreg-add-form-container">
  <div class="giftreg-add-form-label">
    {$lng.lbl_giftreg_add_to}:
  </div>
  <div class="giftreg-add-form">
    <select id="eventid{$prefix}" class="giftreg-selector">
      {foreach from=$giftreg_events item=e}
        <option value="{$e.event_id}">{$e.title|escape}</option>
      {/foreach}
    </select>
    {include file="customer/buttons/button.tpl" button_title=$lng.lbl_add style="image" href="javascript: `$js_condition`$('#eventid`$prefix`').attr('name', 'eventid'); submitForm(document.`$form_name`, 'add2wl');"}
  </div>
</div>
