/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Survey statistics
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: survey_stats.js,v 1.2 2010/05/27 14:09:40 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/* Get loader object */
try {
  var loaderTexts = new ActiveXObject('Msxml2.XMLHTTP');
} catch (err) {}

if (!loaderTexts) {
  try {
    loaderTexts = new ActiveXObject('Microsoft.XMLHTTP');
  } catch (err) {}
}

try {
  if (!loaderTexts && XMLHttpRequest)
    loaderTexts = new XMLHttpRequest();
} catch (err) {}

function loadAnswersTexts(obj, qid, aid) {

  if (!loaderTexts)
    return window.open('survey.php?section=answer_texts&amp;answerid='+aid+'&amp;questionid='+qid, 'answer_texts', "width=600,height=550,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no");

  if (!obj.subTD) {
    var tr = obj;
    while (tr.tagName && tr.tagName.toUpperCase() != 'TR')
      tr = tr.parentNode;

    if (tr.tagName.toUpperCase() != 'TR')
      return false;

    var newTr = tr.parentNode.insertRow(tr.rowIndex+1);
//    newTr.className = tr.className;
    newTr.cssText = tr.cssText;
    obj.subTD = newTr.insertCell(-1);
    obj.subTD.colSpan = 3;
    obj.subTD.parentNode.style.display = 'none';
    obj.subTD.className = 'SurveyAnswerComment';

    var div = obj.subTD.appendChild(document.createElement("DIV"))
    div.className = 'SurveyAnswerComment';
    loadingTextsQuery[qid+'_'+aid] = div;

    obj.subTD.opened = false;

    checkLoadingTexts();

    obj.subTD.parentNode.style.display = '';
    obj.subTD.opened = true;
    return true;
  }

  obj.subTD.parentNode.style.display = obj.subTD.opened ? "none" : ""
  obj.subTD.opened = !obj.subTD.opened;
}

function checkLoadingTexts() {

  if (loadingTextsIdx) {
    if (loaderTexts.readyState == 4 && loaderTexts.status == 200) {
      loadingTextsQuery[loadingTextsIdx].innerHTML = loaderTexts.responseText;

    } else if (loaderTexts.readyState == 4) {
  
      if (!loadingTextsPopup || loadingTextsPopup.closed) {
        alert(txt_survey_loading_http_error_and_open_popup);
        loadingTextsPopup = window.open('survey.php?section=answer_texts&amp;questionid='+loadingTextsIdx.replace(/_/, '&answerid='), 'answer_texts', "width=600,height=550,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no");

      } else {
        alert(txt_survey_loading_http_error);
      }

      loadingTextsQuery[loadingTextsIdx].style.display = 'none;'
      loadingTextsQuery[loadingTextsIdx].opened = false;

    } else if (loadingTextsLimit == 0) {

      if (!loadingTextsPopup || loadingTextsPopup.closed) {
        alert(txt_survey_loading_timeout_and_open_popup);
        loadingTextsPopup = window.open('survey.php?section=answer_texts&amp;questionid='+loadingTextsIdx.replace(/_/, '&answerid='), 'answer_texts', "width=600,height=550,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no");

      } else {
        alert(txt_survey_loading_timeout);
      }

      loadingTextsQuery[loadingTextsIdx].style.display = 'none;'
      loadingTextsQuery[loadingTextsIdx].opened = false;

    } else {

      if (loadingTextsQuery[loadingTextsIdx].innerHTML.length > lbl_survey_loading_texts.length+10 || loadingTextsQuery[loadingTextsIdx].innerHTML.length == 0)
        loadingTextsQuery[loadingTextsIdx].innerHTML = lbl_survey_loading_texts;

      loadingTextsQuery[loadingTextsIdx].innerHTML = loadingTextsQuery[loadingTextsIdx].innerHTML + '.';

      loadingTextsLimit--;
      return setTimeout(checkLoadingTexts, 500);
    }

    loadingTextsQuery[loadingTextsIdx] = null;
    loadingTextsIdx = false;
  }

  if (!loadingTextsIdx) {
    for (var i in loadingTextsQuery) {
      if (hasOwnProperty(loadingTextsQuery, i) && loadingTextsQuery[i] != null) {
        loadingTextsIdx = i;
        break;
      }
    }

    if (!loadingTextsIdx || !loadingTextsQuery[loadingTextsIdx])
      return true;

    loadingTextsLimit = 30;

    loadingTextsQuery[loadingTextsIdx].innerHTML = lbl_survey_loading_texts;
        loaderTexts.open('GET', 'survey.php?section=answer_texts&questionid='+loadingTextsIdx.replace(/_/, '&answerid=')+'&as_text=Y'+new Date().getTime(), true);
        loaderTexts.send('');

    setTimeout(checkLoadingTexts, 500);
  }

  return false;
}
