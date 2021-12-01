$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;
var dropdown = "";

manageData();
getDropdownDataTypeCreate();
    
/* Get Dropdown Data for Doc. Type */
function getDropdownDataType(doctype) {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-doctype.php',
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionType(data.data, doctype);
	});
}

/* Add new option to select */
function manageOptionType(data, doctype) {
	$("#sel_type").empty();
	$.each( data, function( key, value ) {
		if (doctype==value.id) {
			$("#sel_type").append('<option value="'+value.id+'" selected>'+value.name+' ('+value.shortDesc+')</option>');
		} else {
			$("#sel_type").append('<option value="'+value.id+'">'+value.name+' ('+value.shortDesc+')</option>');
		}
	});
}

/* Get Dropdown Data for Doc. Type */
function getDropdownDataTypeCreate() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-doctype.php',
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionTypeCreate(data.data);
	});
}

/* Add new option to select */
function manageOptionTypeCreate(data) {
	$("#sel_type_create").empty();
	$("#sel_type_create").append('<option value="" selected>Please select ...</option>');
	$.each( data, function( key, value ) {
		$("#sel_type_create").append('<option value="'+value.id+'">'+value.name+' ('+value.shortDesc+')</option>');
	});
}

/* manage data list */
function manageData() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_mng-references.php',
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

/* Get Page Data*/
function getPageData() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_mng-references.php',
		data: {page:page}
	}).done(function(data){
		manageRow(data.data);
	});
}

/* Add new Item table row */
function manageRow(data) {
    var	rows = '';
    $.each( data, function( key, value ) {
        if (value.setting == null) {
            value.setting = "";
        }
        rows = rows + '<tr>';
        rows = rows + '<td>'+value.id+'</td>';
//        rows = rows + '<td>'+value.shortName+'</td>'; // table: document
//        rows = rows + '<td>'+value.number+'</td>';    // table: document
        rows = rows + '<td>'+value.identifier+'</td>';      // table: docVersion
        rows = rows + '<td>'+value.name+'</td>';      // table: document
        rows = rows + '<td>'+value.idDocType+'</td>'; // table: document -> docType
        rows = rows + '<td>'+value.version+'</td>';   // table: docVersion
        rows = rows + '<td>'+value.date+'</td>';      // table: docVersion
        rows = rows + '<td>'+value.oname+'</td>';      // table: document -> organisation
        rows = rows + '<td>'+value.filename+'</td>';      // table: docVersion
        rows = rows + '<td data-id="'+value.id+'">';
        rows = rows + '<button data-toggle="modal" data-target="#edit-item" class="btn btn-primary edit-item">Edit</button> ';
        rows = rows + '<button class="btn btn-success change-status">Chg</button> ';
        rows = rows + '<button class="btn btn-danger remove-item">Del</button>';
        rows = rows + '</td>';
        rows = rows + '</tr>';
    });

    $("tbody").html(rows);
}

/* Create new Item */
$(".crud-submit").click(function(e){
    e.preventDefault();
    var form_action = $("#create-item").find("form").attr("action-data");
    //var id = $("#create-item").find("input[name='id']").val();
    var identifier = $("#create-item").find("input[name='identifier']").val();
    var name = $("#create-item").find("input[name='name']").val();
    var type = $("#create-item").find("select[name='type']").val();
    var version = $("#create-item").find("input[name='version']").val();
    var date = $("#create-item").find("input[name='date']").val();
    var organisation = $("#create-item").find("input[name='organisation']").val();
    var filename = $("#create-item").find("input[name='filename']").val();
    var note = $("#create-item").find("textarea[name='note']").val();

    console.log(identifier);
    console.log(name);
    console.log(type);
    console.log(version);
    console.log(date);
    console.log(organisation);
    console.log(filename);
    console.log(note);

    if(identifier != '' && name != '' && type != '' && version != '' && date != '' && organisation != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{identifier:identifier, name:name, type:type, version:version, date:date, organisation:organisation, 
                filename:filename, note:note}
        }).done(function(data){
            //$("#create-item").find("input[name='id']").val('');
            $("#create-item").find("input[name='identifier']").val('');
            $("#create-item").find("input[name='name']").val('');
            $("#create-item").find("select[name='type']").val('');
            $("#create-item").find("input[name='version']").val('');
            $("#create-item").find("input[name='date']").val('');
            $("#create-item").find("input[name='organisation']").val('');
            $("#create-item").find("input[name='filename']").val('');
            $("#create-item").find("textarea[name='note']").val('');
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Item Created Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing some mandatory filed(s).')
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
		url: url + 'api/delete_mng-references.php',
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
    var identifier = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var name = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var type = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var version = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var date = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var organisation = $(this).parent("td").prev("td").prev("td").text();
    var filename = $(this).parent("td").prev("td").text();

    getDropdownDataType(type);

    $("#edit-item").find("input[name='identifier']").val(identifier);
    $("#edit-item").find("input[name='name']").val(name);
    $("#edit-item").find("select[name='type']").val(type);
    $("#edit-item").find("input[name='version']").val(version);
    $("#edit-item").find("input[name='date']").val(date);
    $("#edit-item").find("input[name='organisation']").val(organisation);
    $("#edit-item").find("input[name='filename']").val(filename);
    $("#edit-item").find(".edit-id").val(id);

});

/* Updated new Item */
$(".crud-submit-edit").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-item").find("form").attr("action");
    var name = $("#edit-item").find("input[name='name']").val();
    var shortDesc = $("#edit-item").find("input[name='shortDesc']").val();
    var desc = $("#edit-item").find("textarea[name='desc']").val();
    var id = $("#edit-item").find(".edit-id").val();

    if(id != '' && name != '' && shortDesc != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, name:name, shortDesc:shortDesc, desc:desc}
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