// <?php die(); ?>
/* vim: set ts=4 sw=4 sts=4 et: */
// Sales-n-Stats version 2.0 build 196 settings file.
// this file is automatically generated by the Sales-n-Stats export utility
// do not manually change this file unless you know what your are doing!

generation(2);
startSection('Actions');
  ActionDefinition ActionDefinition_61 = (ActionDefinition)createObject(1,'ActionDefinition',new Some(name = 'FillSurvey', className = 'FillSurveyAction', display = true, enabled = true, saved = true));
  ActionTag ActionTag_81 = (ActionTag)createObject(2,'ActionTag',new Some(tagName = 'survey_name', tagType = 'String', tagDisplay = 'survey_name', tagDescription = null, priority = 1, actionDefinition = :ActionDefinition_61));
endSection();
