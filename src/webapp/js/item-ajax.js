$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;

manageData();

/* manage data list */
function manageData() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData.php',
		data: {page:page}
	}).done(function(data){
		total_page = Math.ceil(data.total/5);
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
		url: url+'api/getData.php',
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
	  	rows = rows + '<td>'+value.idParameter+'</td>';
	  	rows = rows + '<td>'+value.type+'</td>';
	  	rows = rows + '<td>'+value.lvalue+'</td>';
	  	rows = rows + '<td>'+value.hvalue+'</td>';
		rows = rows + '<td>'+value.setting+'</td>';
	  	rows = rows + '<td data-id="'+value.id+'">';
        rows = rows + '<button data-toggle="modal" data-target="#edit-item" class="btn btn-primary edit-item">Edit</button> ';
        rows = rows + '<button class="btn btn-danger remove-item">Delete</button>';
        rows = rows + '</td>';
	  	rows = rows + '</tr>';
	});

	$("tbody").html(rows);
}

/* Create new Item */
$(".crud-submit").click(function(e){
    //e.preventDefault();
    var form_action = $("#create-item").find("form").attr("action-data");
    var id = $("#create-item").find("input[name='id']").val();
	var idParameter = $("#create-item").find("input[name='idParameter']").val();
    var type = $("#create-item").find("input[name='type']").val();
    var lvalue = $("#create-item").find("input[name='lvalue']").val();
    var hvalue = $("#create-item").find("input[name='hvalue']").val();
	var setting = $("#create-item").find("textarea[name='setting']").val();

    if(id != '' && idParameter != '' && type != '' && lvalue != '' && hvalue != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, idParameter:idParameter, type:type, lvalue:lvalue, hvalue:hvalue, setting:setting}
        }).done(function(data){
            $("#create-item").find("input[name='id']").val('');
			$("#create-item").find("input[name='idParameter']").val('');
            $("#create-item").find("input[name='type']").val('');
			$("#create-item").find("input[name='lvalue']").val('');
            $("#create-item").find("input[name='hvalue']").val('');
            $("#create-item").find("textarea[name='setting']").val('');
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Item Created Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing title or description.')
    }

});

/* Remove Item */
$("body").on("click",".remove-item",function(){
	var id = $(this).parent("td").data('id');
	var c_obj = $(this).parents("tr");
	$.ajax({
		dataType: 'json',
		type:'POST',
		url: url + 'api/delete.php',
		data:{id:id}
	}).done(function(data){
		c_obj.remove();
		toastr.success('Item Deleted Successfully.', 'Success Alert', {timeOut: 5000});
		getPageData();
	});

});

/* Edit Item */
$("body").on("click",".edit-item",function(){

    var id = $(this).parent("td").data('id');
    var idParameter = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
	var type = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
	var lvalue = $(this).parent("td").prev("td").prev("td").prev("td").text();
	var hvalue = $(this).parent("td").prev("td").prev("td").text();
    var setting = $(this).parent("td").prev("td").text();

    $("#edit-item").find("input[name='idParameter']").val(idParameter);
    $("#edit-item").find("input[name='type']").val(type);
	$("#edit-item").find("input[name='lvalue']").val(lvalue);
	$("#edit-item").find("input[name='hvalue']").val(hvalue);
	$("#edit-item").find("textarea[name='setting']").val(setting);
    $("#edit-item").find(".edit-id").val(id);

});

/* Updated new Item */
$(".crud-submit-edit").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-item").find("form").attr("action");
    var idParameter = $("#edit-item").find("input[name='idParameter']").val();
	var type = $("#edit-item").find("input[name='type']").val();
	var lvalue = $("#edit-item").find("input[name='lvalue']").val();
	var hvalue = $("#edit-item").find("input[name='hvalue']").val();
    var setting = $("#edit-item").find("textarea[name='setting']").val();
    var id = $("#edit-item").find(".edit-id").val();

    if(id != '' && idParameter != '' && type != '' && lvalue != '' && hvalue != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, idParameter:idParameter, type:type, lvalue:lvalue, hvalue:hvalue, setting:setting}
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