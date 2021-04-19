$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;
var dropdown = "";

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
		url: url+'api/getData_view-tcheader.php?idStandard='+idStandard,
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
		url: url+'api/getData_view-tcheader.php?idStandard='+idStandard+'&showAll=1',
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
		url: url+'api/getData_view-tcheader.php?idStandard='+idStandard,
		data: {page:page}
	}).done(function(data){
		manageRow(data.data);
	});
}

/* Get Dropdown Data for Service Types */
function getDropdownData(type) {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-service.php?idStandard='+idStandard,
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOption(data.data, type);
	});
}

/* Get Dropdown Data for Packet Parameter */
function getDropdownDataPacketParameter(id) {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-packet-parameter.php?idStandard='+idStandard,
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionPacketParameter(data.data, id);
	});
}

/* Get Dropdown Data for Header Role */
function getDropdownDataHeaderRole(role) {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-header-role.php',
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionHeaderRole(data.data, role);
	});
}

/* Add new option to select */
function manageOption(data, type) {
	$("#sel_type").empty();
	$.each( data, function( key, value ) {
		if (type==value.type) {
			$("#sel_type").append('<option value="'+value.type+'" selected>'+value.type+' ('+value.name+')</option>');
		} else {
			$("#sel_type").append('<option value="'+value.type+'">'+value.type+' ('+value.name+')</option>');
		}
	});
}

/* Add new option to select */
function manageOptionPacketParameter(data, id) {
	$("#sel_parameter").empty();
	$.each( data, function( key, value ) {
		if (id==value.id) {
			$("#sel_parameter").append('<option value="'+value.id+'" selected>'+value.domain+' / '+value.name+' ('+value.id+')</option>');
		} else {
			$("#sel_parameter").append('<option value="'+value.id+'">'+value.domain+' / '+value.name+' ('+value.id+')</option>');
		}
	});
}

/* Add new option to select */
function manageOptionHeaderRole(data, role) {
	$("#sel_role").empty();
	$.each( data, function( key, value ) {
		if (role==value.id) {
			$("#sel_role").append('<option value="'+value.id+'" selected>'+value.id+' ('+value.name+')</option>');
		} else {
			$("#sel_role").append('<option value="'+value.id+'">'+value.id+' ('+value.name+')</option>');
		}
	});
}

/* Add new Item table row */
function manageRow(data) {
	var	rows = '';
	$.each( data, function( key, value ) {
	  	rows = rows + '<tr>';
	  	rows = rows + '<td>'+value.id+'</td>';
	  	rows = rows + '<td>'+value.parameter+'</td>';
	  	rows = rows + '<td>'+value.order+'</td>';
	  	rows = rows + '<td>'+value.role+'</td>';
	  	rows = rows + '<td>'+value.group+'</td>';
	  	rows = rows + '<td>'+value.repetition+'</td>';
	  	rows = rows + '<td>'+value.value+'</td>';
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
    var id = $("#create-item").find("input[name='id']").val();
    if (idStandard.length) {
    var parameter = $("#create-item").find("input[name='paramater']").val();
    } else {
    var parameter = $("#create-item").find("select[name='parameter']").val();
    }
    var order = $("#create-item").find("input[name='order']").val();
    if (idStandard.length) {
    var role = $("#create-item").find("input[name='role']").val();
    } else {
    var role = $("#create-item").find("select[name='role']").val();
    }
    var group = $("#create-item").find("input[name='group']").val();
    var repetition = $("#create-item").find("input[name='repetition']").val();
    var value = $("#create-item").find("input[name='value']").val();
    var desc = $("#create-item").find("textarea[name='desc']").val();

    if(id != '' && parameter != '' && order != '' && role != '' && group != '' && 
       repetition != '' && value != '' && desc != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, parameter:parameter, order:order, role:role, group:group,
            repetition:repetition, value:value, desc:desc}
        }).done(function(data){
            $("#create-item").find("input[name='id']").val('');
            if (idStandard.length) {
            $("#create-item").find("input[name='parameter']").val('');
            } else {
            $("#create-item").find("select[name='parameter']").val('');
            }
            $("#create-item").find("input[name='order']").val('');
            if (idStandard.length) {
            $("#create-item").find("input[name='role']").val('');
            } else {
            $("#create-item").find("select[name='role']").val('');
            }
            $("#create-item").find("input[name='group']").val('');
            $("#create-item").find("input[name='repetition']").val('');
            $("#create-item").find("input[name='value']").val('');
            $("#create-item").find("textarea[name='desc']").val('');
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
		url: url + 'api/delete_view-tcheader.php',
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
    var parameter = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var order = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var role = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var group = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var repetition = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var value = $(this).parent("td").prev("td").prev("td").text();
    var desc = $(this).parent("td").prev("td").text();

    /*getDropdownData(type);*/
    getDropdownDataPacketParameter(id);
    getDropdownDataHeaderRole(role);

    if (idStandard.length) {
    $("#edit-item").find("input[name='parameter']").val(parameter);
    } else {
    $("#edit-item").find("select[name='parameter']").val(parameter);
    }
    $("#edit-item").find("input[name='order']").val(order);
    if (idStandard.length) {
    $("#edit-item").find("input[name='role']").val(role);
    } else {
    $("#edit-item").find("select[name='role']").val(role);
    }
    $("#edit-item").find("input[name='group']").val(group);
    $("#edit-item").find("input[name='repetition']").val(repetition);
    $("#edit-item").find("input[name='value']").val(value);
    $("#edit-item").find("textarea[name='desc']").val(desc);
    $("#edit-item").find(".edit-id").val(id);

});

/* Updated new Item */
$(".crud-submit-edit").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-item").find("form").attr("action");
    if (idStandard.length) {
    var parameter = $("#edit-item").find("input[name='parameter']").val();
    } else {
    var parameter = $("#edit-item").find("select[name='parameter']").val();
    }
    var order = $("#edit-item").find("input[name='order']").val();
    if (idStandard.length) {
    var role = $("#edit-item").find("input[name='role']").val();
    } else {
    var role = $("#edit-item").find("select[name='role']").val();
    }
    var group = $("#edit-item").find("input[name='group']").val();
    var repetition = $("#edit-item").find("input[name='repetition']").val();
    var value = $("#edit-item").find("input[name='value']").val();
    var desc = $("#edit-item").find("textarea[name='desc']").val();
    var id = $("#edit-item").find(".edit-id").val();

    if(id != '' && parameter != '' && order != '' && role != '' && group != '' && 
       repetition != '' && value != '' && desc != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, parameter:parameter, order:order, role:role, group:group,
            repetition:repetition, value:value, desc:desc}
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