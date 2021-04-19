/**
 *
 */

(function(jQuery, $) {

  jQuery.log = function(msg) {
    console.log(msg);
  };

  jQuery.fn.highlite = function(options) {
    options = $.extend(true, {}, jQuery.fn.highlite.defaults, options.regexpMods);

    var _checkCookieFn = function(index) {
$.log('checkCookieFn()');
      var cookie = document.cookie,
          regexp = new RegExp(options.cname + index, options.r);
      if (!cookie.match(regexp) && navigator.cookieEnabled) {
      // cookie doesn't exists, so we set an empty one
        _writeCookieFn(index);
        return true;
      } else if (cookie.match(regexp) && navigator.cookieEnabled) {
      // cookie exists
        return true;
      } else {
      // cookie doesn't exists and browser disallows writing cookies
        return false;
      }
    };
    var _readCookieFn = function(index) {
$.log('readCookieFn()');
      var regexpStr = options.cname + index + '="(.+)"',
          regexp = new RegExp(regexpStr, options.regexpMods),
          cookies,
          cvalue,
          tmp;
      if ( _checkCookieFn(index) ) {
        cookies = document.cookie;
        cvalue = ((tmp = regexp.exec(cookies)) == null) ? '' : tmp[1];
$.log('cookies=[' + cookies + ']');
$.log('regExpResult=[' + cvalue +']');
        return cvalue;
      } else {
        return false;
      }
    };
    var _writeCookieFn = function(index, txt) {
$.log('writeCookieFn()');
      var expires = new Date(),
          txt = typeof txt !== 'undefined' ? txt : '';
    // set expiration time
      expires.setMinutes(expires.getMinutes() + options.expTime);
    // write cookie if it's possible
      if (navigator.cookieEnabled) {
        document.cookie = options.cname + index + '="' + txt + '"; expires=' + expires.toGMTString();
        return true;
      } else {
        return false;
      }
    };
    var _formSubmitFn = function(event) {
$.log('formSubmitFn()');
    // get text from input field ...
      var index = $(event.target).data('index'),
          txt = $(options.searchTxt[index]).val();
    // stop default action, if there is no flag set
      if (!$(event.target).data('send')) {
        event.preventDefault();
      // ... and write it to the cookie
        _writeCookieFn(index, txt);
      // set the send flag and ...
        $(event.target).data('send', true);
      // submit form
        $(options.searchForm[index]).submit();
      }
    };
    var _highliteFn = function(index) {
$.log('highliteFn()');
      var searchArr = _readCookieFn(index).split(' '),
          html = $(options.resultSel[index]).html(),
          idx = index;
      $.each(searchArr, function(key, value) {
        if (value != '') {
          var regex = new RegExp('(' + value + '+)', options.regexpMods),
              results = html.match(regex),
              replacement = '<span style="background-color:' + options.color + '">$1</span>';
          if (results != null && results[0] != '') {
$.log('results:' + results[0]);
            html = html.replace(regex, replacement);
          }
        }
      });
      $(options.resultSel[index])
        .html(html);
    };

    return this.each(function(index) {
      $(this)
        .find(options.searchForm[index])
          .data('index', index)
          .submit(function(event) {
            _formSubmitFn(event);
          });
      _highliteFn(index);
      return $(this);
    });
  };

  jQuery.fn.highlite.defaults = {
    color: '#ff0',
    cname: 'lsearch',
    searchTxt: ["#glossary form[name='glossary_search'] #search_text"],
    searchForm:["#glossary form[name='glossary_search']"],
    resultSel: ["#glossary dl"],
    expTime: 5,
    regexpMods: 'gim'
  };
})(jQuery, jQuery);

$(document).ready(function() {
  $('body').highlite();
});