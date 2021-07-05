$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;

var idStandard = getUrlVars()["idStandard"];

manageData();

/* get variables from URL */
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

/* manage data list */
function manageData() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_view-service.php?idStandard='+idStandard,
		data: {page:page}
	}).done(function(data){
		total_page = Math.ceil(data.total/5);
		current_page = page;
		if (data.total == 0) {
			total_page = 1;
			current_page = 1;
		}

		$('#pagination').twbsPagination({
			totalPages: total_page,
			visiblePages: current_page,
			onPageClick: function (event, pageL) {
				page = pageL;
				if(is_ajax_fire != 0){
					getPageData();
				}
			}
		});

		$("#result_nmb").val(data.total);

		manageRow(data.data);
		is_ajax_fire = 1;

	});

}

/* manage data list for all items */
function manageDataAll() {
	is_ajax_fire = 0;

	$.ajax({
		dataType: 'json',
		url: url+'api/getData_view-service.php?idStandard='+idStandard+'&showAll=1',
		data: {page:page}
	}).done(function(data){
		total_page = 1;
		current_page = page;

		$('#pagination').twbsPagination({
			totalPages: total_page,
			visiblePages: current_page,
			onPageClick: function (event, pageL) {
				page = pageL;
				if(is_ajax_fire != 0){
					getPageData();
				}
			}
		});

		manageRow(data.data);
		is_ajax_fire = 1;

	});

}


/* Get Page Data*/
function getPageData() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_view-service.php?idStandard='+idStandard,
		data: {page:page}
	}).done(function(data){
		manageRow(data.data);
	});
}

/* Add new Item table row */
function manageRow(data) {
	var	rows = '';
	$.each( data, function( key, value ) {
	  	rows = rows + '<tr>';
	  	rows = rows + '<td>'+value.id+'</td>';
	  	rows = rows + '<td>'+value.type+'</td>';
	  	rows = rows + '<td>'+value.name+'</td>';
	  	rows = rows + '<td>'+value.desc+'</td>';
	  	rows = rows + '<td data-id="'+value.id+'">';
        rows = rows + '<button data-toggle="modal" data-target="#edit-item" class="btn btn-primary edit-item">Edit</button> ';
        rows = rows + '<button class="btn btn-danger remove-item">Delete</button>';
        rows = rows + '</td>';
	  	rows = rows + '</tr>';
	});

	$("tbody").html(rows);
}

/* Show all Items */
$(".crud-submit-show").click(function(e){
    manageDataAll();
});

/* Create new Item */
$(".crud-submit").click(function(e){
    //e.preventDefault();
    var form_action = $("#create-item").find("form").attr("action-data");
    var idStandard = $("#create-item").find("input[name='idStandard']").val();
    var type = $("#create-item").find("input[name='type']").val();
    var name = $("#create-item").find("input[name='name']").val();
    var desc = $("#create-item").find("textarea[name='desc']").val();

    if(idStandard != '' && type != '' && name != '' && desc != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{idStandard:idStandard, type:type, name:name, desc:desc}
        }).done(function(data){
            $("#create-item").find("input[name='idStandard']").val('');
            $("#create-item").find("input[name='type']").val('');
            $("#create-item").find("input[name='name']").val('');
            $("#create-item").find("textarea[name='desc']").val('');
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Item Created Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing type, name or description.')
    }

});

/* Remove Item */
$("body").on("click",".remove-item",function(){
    var id = $(this).parent("td").data('id');
    var c_obj = $(this).parents("tr");

    var confirmation = confirm("Are you sure you want to remove this item?");
    if (confirmation) {
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + 'api/delete_view-service.php',
            data:{id:id}
        }).done(function(data){
            c_obj.remove();
            toastr.success('Item Deleted Successfully.', 'Success Alert', {timeOut: 5000});
            getPageData();
        });
    }

});

/* Edit Item */
$("body").on("click",".edit-item",function(){

    var id = $(this).parent("td").data('id');
    var type = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var name = $(this).parent("td").prev("td").prev("td").text();
    var desc = $(this).parent("td").prev("td").text();

    $("#edit-item").find("input[name='type']").val(type);
    $("#edit-item").find("input[name='name']").val(name);
    $("#edit-item").find("textarea[name='desc']").val(desc);
    $("#edit-item").find(".edit-id").val(id);

});

/* Updated new Item */
$(".crud-submit-edit").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-item").find("form").attr("action");
    var type = $("#edit-item").find("input[name='type']").val();
    var name = $("#edit-item").find("input[name='name']").val();
    var desc = $("#edit-item").find("textarea[name='desc']").val();
    var id = $("#edit-item").find(".edit-id").val();

    if(id != '' && type != '' && name != '' && desc != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, type:type, name:name, desc:desc}
        }).done(function(data){
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Item Updated Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing something.')
    }

});

});