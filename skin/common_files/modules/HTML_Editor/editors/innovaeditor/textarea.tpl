{*
$Id: textarea.tpl,v 1.1 2010/05/21 08:32:31 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
<!--
  if (isHTML_Editor) {ldelim}

    var {$id}Editor = new InnovaEditor('{$id}Editor');

    {$id}Editor.width = 576;
    if (navigator.appName.indexOf('Microsoft')!=-1)
      {$id}Editor.height = {$rows|default:30}*13;
    else if (navigator.appName.indexOf('Netscape')!=-1)
      {$id}Editor.height = {$rows|default:30}*14;
    else
      {$id}Editor.height = {$rows|default:30}*12;

    {$id}Editor.mode = '{$html_editor_mode|default:"XHTMLBody"}';
    
    if (popup_html_editor_text != undefined) {ldelim}
      $('#{$id}Box').show();
      $("#{$id}").val(popup_html_editor_text);
      {$id}Editor.REPLACE("{$id}");
    {rdelim} else {ldelim}
      {$id}Editor.REPLACE("{$id}Adv");
    {rdelim}
    
  {rdelim} else {ldelim}

    $('#{$id}Box').hide();

  {rdelim}
-->
</script>
