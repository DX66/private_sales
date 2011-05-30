/*
$Id: iefix.js,v 1.1 2010/05/21 08:33:03 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*/
if ($.browser.msie) {
  function IEFix() {
      $('#page-container2').height($('#page-container').height() - 1);
      $('#content-container').height($('#footer').position().top - $('#header').height() - 2);
      $('input[type=radio], input[type=checkbox]').addClass('no-background');
  };

  $.event.add(window, "load", IEFix);
  $.event.add(window, "resize", IEFix);
}
