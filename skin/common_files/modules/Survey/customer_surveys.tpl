{*
$Id: customer_surveys.tpl,v 1.1.2.1 2010/10/21 13:48:31 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.lbl_survey_surveys}</h1>

{capture name=dialog}

  {if $count_surveys}

    {if $count_unfilled gt 0}

      {include file="customer/subheader.tpl" title=$lng.lbl_survey_available_surveys}

      <table cellspacing="1" class="width-100" summary="{$lng.lbl_survey_available_surveys|escape}">
        {foreach from=$surveys item=s key=sid name=surveys}
          {if not $s.is_filled}
            <tr{interline name=surveys}>
              <td><a href="survey.php?surveyid={$sid}">{$s.survey|amp}</a></td>
            </tr>
          {/if}
        {/foreach}
      </table>

    {/if}

    {if $count_filled gt 0}

      {if $count_unfilled gt 0}
        <br />
      {/if}

      {include file="customer/subheader.tpl" title=$lng.lbl_survey_completed_surveys}

      <table cellspacing="1" class="width-100" summary="{$lng.lbl_survey_completed_surveys|escape}">
        {foreach from=$surveys item=s key=sid name=surveys}
          {if $s.is_filled}
            <tr{interline name=surveys}>
              <td>{$s.survey}</td>
              <td width="10%" nowrap="nowrap">
                {if $s.is_view_results}
                  <a href="survey.php?surveyid={$sid}&amp;mode=view">{$lng.lbl_survey_view_results}</a>
                {/if}
               </td>
            </tr>
          {/if}
        {/foreach}
      </table>

    {/if}

  {else}

    {$lng.txt_survey_list_is_empty}

  {/if}

{/capture}
{include file="customer/dialog.tpl" title=$lng.lbl_survey_surveys content=$smarty.capture.dialog noborder=true}
