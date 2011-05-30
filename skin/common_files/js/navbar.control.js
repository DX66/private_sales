/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Navigation bar dynamic control
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: navbar.control.js,v 1.2 2010/05/27 13:43:07 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

var navBar = function(div, onclickHandler) {

  if (!window.jQuery)
    return false;

  var lastPage = $('.nav-page:last', div).get(0);
  if (!lastPage)
    return false;

  this.total = parseInt(lastPage.innerHTML);
  if (isNaN(this.total) || this.total < 3)
    return false;

  var links = $('a.nav-page', div).get();
  if (links.length == 0)
    return false;

  this.url = links[0].href.replace(/page=[0-9]+$/, 'page=');

  this.base = div;

  div.navbar = this;

  this.onclick = jQuery.isFunction(onclickHandler) ? onclickHandler : false;

  this.max_pages = -1;

  for (var i = 0; i < this.base.childNodes.length; i++) {
    if (this.base.childNodes[i].nodeType == 8) {
      var m = this.base.childNodes[i].data.match(/max_pages: ([0-9]+)/);
      if (m)
        this.max_pages = parseInt(m[1]);
    }
  }

  if (isNaN(this.max_pages) || this.max_pages == -1) {
    this.max_pages = links.length + 1 - $('.nav-dots', this.base).length;
  }

  this._active = true;

  this._bindOnClicks();
}

/*
  Public properties
*/
navBar.prototype.base = false;
navBar.prototype.total = false;
navBar.prototype.url = false;
navBar.prototype.max_pages = false;

/*
  Private properties
*/
navBar.prototype._active = false;

/*
  Public methods
*/

/*
  Change page
*/
navBar.prototype.changePage = function(page) {
  if (!this._active)
    return false;

  if (typeof(page) != 'number')
    page = parseInt(page);

  if (isNaN(page))
    return false;

  if (page < 1 || page > this.total)
    return false;

  var curPage = this._getCurrentPage();
  if (page == curPage || curPage === false)
    return false;

  // clear bar
  $('*:not(.nav-pages-title)', this.base).remove();

  // calculation
  var start_page = Math.max(Math.ceil(page - (this.max_pages / 2)), 1);
  var end_loop_pages = Math.min(start_page + Math.min(this.max_pages, this.total), this.total + 1);
  if (end_loop_pages - start_page < this.max_pages)
    start_page = this.max_pages > this.total ? 1 : end_loop_pages - this.max_pages;

  // add left arrow
  if (page > 1) {
    $(this.base)
      .append(
        $(document.createElement('A'))
          .addClass('nav-pages-larrow')
          .attr('href', this.url + (page-1)).
          append(
            $(document.createElement('IMG'))
              .attr('src', images_dir + '/spacer.gif')
              .attr('alt', lbl_prev_page ? lbl_prev_page : '')
          )
      )
      .append(
        $(document.createElement("SPAN"))
          .addClass('nav-delim')
      );
  }

  // add 1 page
  if (start_page > 1) {
    $(this.base)
      .append(
        $(document.createElement('A'))
          .addClass('nav-page')
          .attr('href', this.url + '1')
          .append('1')
      )
      .append(
        $(document.createElement("SPAN"))
          .addClass('nav-delim')
      );
  }

  // add left dot-dot-dot
  if (start_page > 2) {
    $(this.base)
      .append(
        $(document.createElement("SPAN"))
          .addClass('nav-dots')
          .append('...')
      )
      .append(
        $(document.createElement("SPAN"))
          .addClass('nav-delim')
      );
  }

  // add pages row
  for (var p = start_page; p < end_loop_pages; p++) {
    if (p == page) {
      $(this.base)
        .append(
          $(document.createElement('SPAN'))
            .addClass('current-page')
            .attr('title', lbl_current_page ? (lbl_current_page + ': #' + p) : '')
            .append(p)
        );

    } else {

      $(this.base)
        .append(
          $(document.createElement('A'))
            .addClass('nav-page')
            .attr('href', this.url + p)
            .attr('title', lbl_page ? (lbl_page + ' #' + p) : '')
            .append(p)
        );
    }

    if (p != end_loop_pages - 1)
      $(this.base)
        .append(
          $(document.createElement("SPAN"))
            .addClass('nav-delim')
        );
  }

  // add right dot-dot-dot
  if (end_loop_pages < this.total)
    $(this.base)
      .append(
        $(document.createElement("SPAN"))
          .addClass('nav-delim')
      )
      .append(
        $(document.createElement("SPAN"))
          .addClass('nav-dots')
          .append('...')
      );

  // add last page
  if (end_loop_pages <= this.total) {
    $(this.base)
      .append(
        $(document.createElement("SPAN"))
          .addClass('nav-delim')
      )
      .append(
        $(document.createElement('A'))
          .addClass('nav-page')
          .attr('href', this.url + this.total)
          .append(this.total)
      );
  }

  // add right arrow
  if (page < this.total) {
    $(this.base)
      .append(
        $(document.createElement("SPAN"))
          .addClass('nav-delim')
      )
      .append(
        $(document.createElement('A'))
          .addClass('nav-pages-rarrow')
          .attr('href', this.url + (page + 1))
          .append(
            $(document.createElement('IMG'))
              .attr('src', images_dir + '/spacer.gif')
              .attr('alt', lbl_next_page ? lbl_next_page : '')
          )
      );
  }

  this._bindOnClicks();
}

/*
  Private methods
*/

/*
  Get current page number
*/
navBar.prototype._getCurrentPage = function() {
  if (!this._active)
    return false;

  var span = $('.current-page', this.base).get(0);
  if (!span)
    return false;

  var idx = parseInt(span.innerHTML);
  return isNaN(idx) ? false : idx;
}

/*
  OnClick common handler
*/
navBar.prototype._onclickHandler = function() {
  return this.navbar._active && this.navbar.onclick ? this.navbar.onclick.call(this, this.navbar) : true;
}

/*
  Bind OnClick handler to all links
*/
navBar.prototype._bindOnClicks = function() {
  if (!this._active)
    return false;

  var links = $('a', this.base).get();
  for (var i = 0; i < links.length; i++) {
    links[i].navbar = this;
    links[i].onclick = this._onclickHandler;
  }

  return true;
}
