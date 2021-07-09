$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;
var dropdown = "";

var idProject = getUrlVars()["idProject"];

getDropdownDataOwnerCreate();
getDropdownDataRoleCreate();

manageData();

/* get variables from URL */
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

/* Get Dropdown Data for Owner */
function getDropdownDataOwner(owner) {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-owner.php',
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionOwner(data.data, owner);
	});
}

/* Add new option to select */
function manageOptionOwner(data, owner) {
	$("#sel_owner").empty();
	$.each( data, function( key, value ) {
		if (owner==value.email) {
			$("#sel_owner").append('<option value="'+value.email+'" selected>'+value.email+' ('+value.name+')</option>');
		} else {
			$("#sel_owner").append('<option value="'+value.email+'">'+value.email+' ('+value.name+')</option>');
		}
	});
}

/* Get Dropdown Data for Owner */
function getDropdownDataOwnerCreate() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-owner.php',
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionOwnerCreate(data.data);
	});
}

/* Add new option to select */
function manageOptionOwnerCreate(data) {
	$("#sel_owner_create").empty();
	$("#sel_owner_create").append('<option value="" selected>Please select ...</option>');
	$.each( data, function( key, value ) {
		$("#sel_owner_create").append('<option value="'+value.email+'">'+value.email+' ('+value.name+')</option>');
	});
}

/* Get Dropdown Data for User Role */
function getDropdownDataRole(role) {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-user-role.php',
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionRole(data.data, role);
	});
}

/* Add new option to select */
function manageOptionRole(data, role) {
	$("#sel_role").empty();
	$.each( data, function( key, value ) {
		if (role==value.id) {
			$("#sel_role").append('<option value="'+value.id+'" selected>'+value.name+' ('+value.id+')</option>');
		} else {
			$("#sel_role").append('<option value="'+value.id+'">'+value.name+' ('+value.id+')</option>');
		}
	});
}

/* Get Dropdown Data for User Role */
function getDropdownDataRoleCreate() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-user-role.php',
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionRoleCreate(data.data);
	});
}

/* Add new option to select */
function manageOptionRoleCreate(data) {
	$("#sel_role_create").empty();
	$("#sel_role_create").append('<option value="" selected>Please select ...</option>');
	$.each( data, function( key, value ) {
		$("#sel_role_create").append('<option value="'+value.id+'">'+value.name+' ('+value.id+')</option>');
	});
}

/* manage data list */
function manageData() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_view-project-contributor.php?idProject='+idProject,
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

/* manage data list for all items */
function manageDataAll() {
	is_ajax_fire = 0;

	$.ajax({
		dataType: 'json',
		url: url+'api/getData_view-project-contributor.php?idProject='+idProject+'&showAll=1',
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
		url: url+'api/getData_view-project-contributor.php?idProject='+idProject,
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
        rows = rows + '<td>'+value.email+'</td>';
        rows = rows + '<td>'+value.idRole+'</td>';
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
    e.preventDefault();
    var form_action = $("#create-item").find("form").attr("action-data");
    var idProject = $("#create-item").find("input[name='idProject']").val();
    var idUser = $("#create-item").find("input[name='idUser']").val();
    var email = $("#create-item").find("select[name='email']").val();
    var idRole = $("#create-item").find("select[name='idRole']").val();

    if(idProject != '' && idUser != '' && email != '' && idRole != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{idProject:idProject, idUser:idUser, email:email, idRole:idRole},
            success: function(results, textStatus) {
                toastr.success('Database Operation Successfully. ' + results, 'Success Alert', {timeOut: 5000});
            },
            error: function(xhr, status, error)
            {
                toastr.error('Database Operation Failed. ' + xhr.responseText, 'Failure Alert', {timeOut: 5000});
            }
        }).done(function(data){
            $("#create-item").find("input[name='idProject']").val('');
            $("#create-item").find("input[name='idUser']").val('');
            $("#create-item").find("select[name='email']").val('');
            $("#create-item").find("select[name='idRole']").val('');
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Item Created Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing email or role.')
    }

});

/* Remove Item */
$("body").on("click",".remove-item",function(){
	
	var confirmation = confirm("Are you sure to remove the item?");
	
	if (confirmation){
	
	var id = $(this).parent("td").data('id');
	var c_obj = $(this).parents("tr");
	$.ajax({
		dataType: 'json',
		type:'POST',
		url: url + 'api/delete_view-project-contributor.php',
		data:{id:id}
	}).done(function(data){
        if (data['status'] == 1001) {
            toastr.warning('Item can not be deleted! '+data['statusText'], 'Failure Alert', {timeOut: 5000});
        } else {
		c_obj.remove();
		toastr.success('Item Deleted Successfully.', 'Success Alert', {timeOut: 5000});
		getPageData();
        }
	});
	
	}

});

/* Edit Item */
$("body").on("click",".edit-item",function(){

    var id = $(this).parent("td").data('id');
    var email = $(this).parent("td").prev("td").prev("td").text();
    var idRole = $(this).parent("td").prev("td").text();

    getDropdownDataOwner(email);
    getDropdownDataRole(idRole);

    $("#edit-item").find("select[name='email']").val(name);
    $("#edit-item").find("select[name='idRole']").val(email);
    $("#edit-item").find(".edit-id").val(id);

});

/* Updated new Item */
$(".crud-submit-edit").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-item").find("form").attr("action");
    var email = $("#edit-item").find("select[name='email']").val();
    var idRole = $("#edit-item").find("select[name='idRole']").val();
    var id = $("#edit-item").find(".edit-id").val();

    if(id != '' && email != '' && idRole != '' ){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, email:email, idRole:idRole}
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