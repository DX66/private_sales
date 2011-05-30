/*
$Id: iefix.js,v 1.1 2010/05/21 08:31:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*/
if ($.browser.msie) {
  function IEFix() {
      $('#page-container2').height($('#page-container').height());
  };

  $.event.add(window, "load", IEFix);
  $.event.add(window, "resize", IEFix);
}
