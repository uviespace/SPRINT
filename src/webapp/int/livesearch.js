$(document).ready(function(){
  $("#liveSearch").on("search change keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#myTable tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });

    /* highlighting results */
/*
    $(".results").removeClass("results");
    $(".noresults").removeClass("noresults");
    $("#myTable tr").each(function () {
      if (value != "" && $(this).text().search(new RegExp(value,'gi')) != -1) {
        $(this).addClass("results");
      } else if (value != "" && $(this).text().search(value) != 1) {
        $(this).addClass("noresults");
      }
    });
*/
  });
});

/*
jQuery(document).ready(function($) {
$(".on-page-search").on("keyup", function () {
var v = $(this).val().toLowerCase();
$(".results").removeClass("results");
$(".noresults").removeClass("noresults");
$("#myTable td").each(function () { 
  if (v != "" && $(this).text().search(new RegExp(v,'gi')) != -1) {
    $(this).addClass("results"); 
  } else if (v != "" && $(this).text().search(v) != 1) {
    $(this).addClass("noresults");
  }
});

    $("#myTable tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(v) > -1)
    });

});
});
*/

/*
$("#search").on("search change keyup",function () {
  var text =this.value;
  $(".content").highlite({
      text: text
  });
});
*/

/* highlite text */
$(function () {
    $("#liveSearch").on("search change keyup", function () {
        var text = this.value;
        $("#myTable").highlite({
            text: text
        });
    });
});


$(document).ready(function(){
  $("#liveSearch").on("search change keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#myTableImport tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });

    /* highlighting results */
/*
    $(".results").removeClass("results");
    $(".noresults").removeClass("noresults");
    $("#myTableImport tr").each(function () {
      if (value != "" && $(this).text().search(new RegExp(value,'gi')) != -1) {
        $(this).addClass("results");
      } else if (value != "" && $(this).text().search(value) != 1) {
        $(this).addClass("noresults");
      }
    });
*/
  });
});

/* highlite text */
$(function () {
    $("#liveSearch").on("search change keyup", function () {
        var text = this.value;
        $("#myTableImport").highlite({
            text: text
        });
    });
});
