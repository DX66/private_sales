{*
$Id: spambot_arrest.tpl,v 1.5.2.2 2010/11/15 11:46:25 ferz Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if not $id}
  {assign var="id" value="image"}
{/if}

{if $mode eq 'data-table'}
<tr class="hidden"><td>
{/if}
<label for="antibot_input_str" class="data-required hidden">{$lng.lbl_word_verification}</label>
{if $mode eq 'data-table'}
</td></tr>
{/if}

{if $mode eq 'advanced' or $mode eq 'simple'}

  <div class="iv-box">
    {if $mode eq 'advanced'}
      {$lng.lbl_word_verification}
      <hr />
    {/if}
    {$lng.lbl_type_the_characters}:
    <div class="iv-row">
      {include file="modules/Image_Verification/image_block.tpl" nobr=true}
      <div class="iv-input valign-middle-adv-lvl1">
        <div class="valign-middle-adv-lvl2">
          <div class="valign-middle-adv-lvl3">
            <span class="star">*</span>
            <input type="text" id="antibot_input_str" name="antibot_input_str"{if $antibot_err} class="err"{/if} />
            {if $button_code}
              <div>
                {$button_code}
              </div>
            {/if}
          </div>
        </div>
      </div>
    </div>
    <div class="clearing"></div>
  </div>

  {if $config.Image_Verification.spambot_arrest_case_sensitive eq 'Y' && $config.Image_Verification.spambot_arrest_str_generator ne 'numbers'}
    {$lng.lbl_case_sensitive_note}
  {/if}

{elseif $mode eq 'data-table'}

  <tr>
    <td colspan="3" class="iv-box-descr">{$lng.lbl_type_the_characters}:</td>
  </tr>
  <tr>
    <td class="iv-box">
      {include file="modules/Image_Verification/image_block.tpl"}
    </td>
    <td class="data-required">*</td>
    <td class="iv-box">
      <input type="text" id="antibot_input_str" name="antibot_input_str"{if $antibot_err} class="err"{/if} />
      {if $button_code}
        <div class="button-row">
          {$button_code}
        </div>
      {/if}
      {if $config.Image_Verification.spambot_arrest_case_sensitive eq 'Y' && $config.Image_Verification.spambot_arrest_str_generator ne 'numbers'}
        {$lng.lbl_case_sensitive_note}
      {/if}
    </td>
  </tr>

{elseif $mode eq 'simple_column'}

  <div class="iv-box">
    {$lng.lbl_type_the_characters}:
    {include file="modules/Image_Verification/image_block.tpl"}
    <div class="iv-input">
      <span class="star">*</span>
      <input type="text" id="antibot_input_str" name="antibot_input_str"{if $antibot_err} class="err"{/if} />
    </div>
    <div class="clearing"></div>
  </div>

  {if $config.Image_Verification.spambot_arrest_case_sensitive eq 'Y' && $config.Image_Verification.spambot_arrest_str_generator ne 'numbers'}
    {$lng.lbl_case_sensitive_note}
  {/if}

{/if}

