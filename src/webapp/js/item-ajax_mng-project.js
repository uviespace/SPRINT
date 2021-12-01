$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;
var dropdown = "";

var userid = document.getElementById("user_id");
toastr.success('User ID = '+userid.value, 'Success Alert', {timeOut: 5000});

manageData();
getDropdownDataPublicCreate();
getDropdownDataOwnerCreate();

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
		if (owner==value.name) {
			$("#sel_owner").append('<option value="'+value.name+'" selected>'+value.name+' ('+value.email+')</option>');
		} else {
			$("#sel_owner").append('<option value="'+value.name+'">'+value.name+' ('+value.email+')</option>');
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
		$("#sel_owner_create").append('<option value="'+value.name+'">'+value.name+' ('+value.email+')</option>');
	});
}

/* Get Dropdown Data for isPublic */
function getDropdownDataPublic(isPublic) {
    var data = { 
        "data": [ 
            { "isPublic":"0", "name":"no", "desc":"Project is not public." }, 
            { "isPublic":"1", "name":"yes", "desc":"Project is public." }
        ]
    };
    manageOptionPublic(data.data, isPublic);
}

/* Add new option to select */
function manageOptionPublic(data, isPublic) {
	$("#sel_public").empty();
	$.each( data, function( key, value ) {
		if (isPublic==value.isPublic) {
			$("#sel_public").append('<option value="'+value.isPublic+'" selected>'+value.name+'</option>');
		} else {
			$("#sel_public").append('<option value="'+value.isPublic+'">'+value.name+'</option>');
		}
	});
}

/* Get Dropdown Data for isPublic */
function getDropdownDataPublicCreate() {
    var data = { 
        "data": [ 
            { "isPublic":"0", "name":"no", "desc":"Project is not public." }, 
            { "isPublic":"1", "name":"yes", "desc":"Project is public." }
        ]
    };
    manageOptionPublicCreate(data.data);
}

/* Add new option to select */
function manageOptionPublicCreate(data) {
	$("#sel_public_create").empty();
	$("#sel_public_create").append('<option value="" selected>Please select ...</option>');
	$.each( data, function( key, value ) {
		$("#sel_public_create").append('<option value="'+value.isPublic+'">'+value.name+'</option>');
	});
}

/* manage data list */
function manageData() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_mng-project.php?userid='+userid.value,
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
		url: url+'api/getData_mng-project.php?userid='+userid.value,
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
	  	rows = rows + '<td>'+value.name+'</td>';
	  	rows = rows + '<td>'+value.desc+'</td>';
	  	rows = rows + '<td>'+value.owner+'</td>';
	  	rows = rows + '<td>'+value.isPublic+'</td>';
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
    var userid = $("#create-item").find("input[name='userid']").val();
    var name = $("#create-item").find("input[name='name']").val();
    var desc = $("#create-item").find("textarea[name='desc']").val();
    var owner = $("#create-item").find("select[name='owner']").val();
    var isPublic = $("#create-item").find("select[name='isPublic']").val();
    var setting = $("#create-item").find("textarea[name='setting']").val();

    if(userid != '' && name != '' && desc != '' && owner != '' && isPublic != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{userid:userid, name:name, desc:desc, owner:owner, isPublic:isPublic, setting:setting}
        }).done(function(data){
            $("#create-item").find("input[name='userid']").val('');
			$("#create-item").find("input[name='name']").val('');
            $("#create-item").find("textarea[name='desc']").val('');
			$("#create-item").find("select[name='owner']").val('');
            $("#create-item").find("select[name='isPublic']").val('');
            $("#create-item").find("textarea[name='setting']").val('');
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Item Created Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing name, description, owner or is Public.')
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
			url: url + 'api/delete_mng-project.php',
			data:{id:id}
		}).done(function(data){
            if (data['status'] == 2001 | data['status'] == 2002) {
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
    var name = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var desc = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var owner = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var isPublic = $(this).parent("td").prev("td").prev("td").text();
    var setting = $(this).parent("td").prev("td").text();

    getDropdownDataOwner(owner);
    getDropdownDataPublic(isPublic);

    $("#edit-item").find("input[name='name']").val(name);
    $("#edit-item").find("textarea[name='desc']").val(desc);
    $("#edit-item").find("select[name='owner']").val(owner);
    $("#edit-item").find("select[name='isPublic']").val(isPublic);
    $("#edit-item").find("textarea[name='setting']").val(setting);
    $("#edit-item").find(".edit-id").val(id);

});

/* Updated new Item */
$(".crud-submit-edit").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-item").find("form").attr("action");
    var name = $("#edit-item").find("input[name='name']").val();
    var desc = $("#edit-item").find("textarea[name='desc']").val();
    var owner = $("#edit-item").find("select[name='owner']").val();
    var isPublic = $("#edit-item").find("select[name='isPublic']").val();
    var setting = $("#edit-item").find("textarea[name='setting']").val();
    var id = $("#edit-item").find(".edit-id").val();

    if(id != '' && name != '' && desc != '' && owner != '' && isPublic != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, name:name, desc:desc, owner:owner, isPublic:isPublic, setting:setting}
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