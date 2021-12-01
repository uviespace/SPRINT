$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;
var dropdown = "";

manageData();
getDropdownDataCountryCreate();

/* Get Dropdown Data for Country */
function getDropdownDataCountry(country) {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-country.php',
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionCountry(data.data, country);
	});
}

/* Add new option to select */
function manageOptionCountry(data, country) {
	$("#sel_country").empty();
	$.each( data, function( key, value ) {
		if (country==value.id) {
			$("#sel_country").append('<option value="'+value.id+'" selected>'+value.name+'</option>');
		} else {
			$("#sel_country").append('<option value="'+value.id+'">'+value.name+'</option>');
		}
	});
}

/* Get Dropdown Data for Country */
function getDropdownDataCountryCreate() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-country.php',
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionCountryCreate(data.data);
	});
}

/* Add new option to select */
function manageOptionCountryCreate(data) {
	$("#sel_country_create").empty();
	$("#sel_country_create").append('<option value="" selected>Please select ...</option>');
	$.each( data, function( key, value ) {
		$("#sel_country_create").append('<option value="'+value.id+'">'+value.name+'</option>');
	});
}

/* manage data list */
function manageData() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_mng-organisations.php',
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
		url: url+'api/getData_mng-organisations.php',
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
        rows = rows + '<td>'+value.name+'</td>';
        rows = rows + '<td>'+value.shortDesc+'</td>';
        rows = rows + '<td>'+value.idCountry+'</td>';
        rows = rows + '<td>'+value.desc+'</td>';
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
    //e.preventDefault();
    var form_action = $("#create-item").find("form").attr("action-data");
    var id = $("#create-item").find("input[name='id']").val();
    var name = $("#create-item").find("input[name='name']").val();
    var shortDesc = $("#create-item").find("input[name='shortDesc']").val();
    var idCountry = $("#create-item").find("input[name='idCountry']").val();
    var desc = $("#create-item").find("textarea[name='desc']").val();

    if(id != '' && name != '' && shortDesc != '' && idCountry != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, name:name, shortDesc:shortDesc, idCountry:idCountry, desc:desc}
        }).done(function(data){
            $("#create-item").find("input[name='id']").val('');
            $("#create-item").find("input[name='name']").val('');
            $("#create-item").find("input[name='shortDesc']").val('');
            $("#create-item").find("input[name='idCountry']").val('');
            $("#create-item").find("textarea[name='desc']").val('');
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Item Created Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing name, short description or country.')
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
		url: url + 'api/delete_mng-organisations.php',
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
    var name = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var shortDesc = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var idCountry = $(this).parent("td").prev("td").prev("td").text();
    var desc = $(this).parent("td").prev("td").text();

    getDropdownDataCountry(idCountry);

    $("#edit-item").find("input[name='name']").val(name);
    $("#edit-item").find("input[name='shortDesc']").val(shortDesc);
    $("#edit-item").find("input[select='idCountry']").val(idCountry);
    $("#edit-item").find("textarea[name='desc']").val(desc);
    $("#edit-item").find(".edit-id").val(id);

});

/* Updated new Item */
$(".crud-submit-edit").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-item").find("form").attr("action");
    var name = $("#edit-item").find("input[name='name']").val();
    var shortDesc = $("#edit-item").find("input[name='shortDesc']").val();
    var idCountry = $("#edit-item").find("select[name='idCountry']").val();
    var desc = $("#edit-item").find("textarea[name='desc']").val();
    var id = $("#edit-item").find(".edit-id").val();

    if(id != '' && name != '' && shortDesc != '' && idCountry != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, name:name, shortDesc:shortDesc, idCountry:idCountry, desc:desc}
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