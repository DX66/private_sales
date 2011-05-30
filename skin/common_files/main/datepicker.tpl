{*
$Id: datepicker.tpl,v 1.8 2010/07/27 12:50:51 aim Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}

<input id="{$id|default:$name|escape}" class="datepicker-formatted" name="{$name|escape}" type="text" value="{$date|default:$smarty.now|date_format:$config.Appearance.date_format}" />
<script type="text/javascript">
//<![CDATA[

$(document).ready(function () {ldelim}

// Original input object
var dp_in = $("#{$id|default:$name|escape}");

// Create a hidden field that will contain timestamp
// generated on-the-fly when setting date
var dp_ts = $(document.createElement('input'))
  .attr('type', 'hidden')
  .attr('name', '{$name|escape}')
  .attr('id', '{$id|default:$name|escape}')
  .val('{$date|default:$smarty.now}');

// Change attributes of an original object
$(dp_in)
  .attr('id',   'f_{$id|default:$name|escape}')
  .attr('name', 'f_{$name|escape}');

$(dp_ts).insertAfter(dp_in);

var opts = {ldelim}
  yearRange:   '{$start_year|default:$config.Company.start_year}:{$end_year|default:$config.Company.end_year}',
  dateFormat:  '{$config.Appearance.ui_date_format}',
  altFormat:   '@',
  altField:    '#{$id|default:$name|escape}',
  regional:    '{$shop_language}',
  changeMonth: {$changeMonth|default:'true'},
  changeYear:  {$changeYear|default:'true'},
  showOn:      'both',
  buttonImage: '{$ImagesDir}/calendar.png',
  buttonImageOnly: true

{rdelim};

$("#f_{$id|default:$name|escape}")
  .datepicker(opts)
  .bind('change', function() {ldelim}
    var re = new RegExp(/000$/);
    $('#{$id|default:$name|escape}').val($('#{$id|default:$name|escape}').val().replace(re, ''));
  {rdelim})

{rdelim}) // $(document).ready(function () {ldelim}

//]]>
</script>
