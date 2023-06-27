$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;
var dropdown = ""; // needed for dropdown menus for initialization

var userrole = document.getElementById("user_role");

var idStandard = getUrlVars()["idStandard"];
var idParameter = getUrlVars()["idParameter"];

    getDropdownDataCalCurveCreate()

manageData();

/* get variables from URL */
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

/* Get Dropdown Data for CalCurve */
function getDropdownDataCalCurveCreate() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-calibration.php?idStandard='+idStandard,
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionCalCurveCreate(data.data);
	});
}

/* Add new option to select */
function manageOptionCalCurveCreate(data) {
	$("#sel_calcurve_create").empty();
	$("#sel_calcurve_create").append('<option value="" selected>--- Please select ---</option>');
	$.each( data, function( key, value ) {
		$("#sel_calcurve_create").append('<option value="'+value.id+'">'+value.id+' - '+value.name+' ('+value.shortDesc+')</option>');
	});
	
}

/* Get Dropdown Data for CalCurve */
function getDropdownDataCalCurve(calcurve) {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-calibration.php?idStandard='+idStandard,
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionCalCurve(data.data, calcurve);
	});
}

/* Add new option to select */
function manageOptionCalCurve(data, calcurve) {
	$("#sel_calcurve").empty();
	$.each( data, function( key, value ) {
		if (calcurve==value.id) {
			$("#sel_calcurve").append('<option value="'+value.id+'" selected>'+value.id+' - '+value.name+' ('+value.shortDesc+')</option>');
		} else {
			$("#sel_calcurve").append('<option value="'+value.id+'">'+value.id+' - '+value.name+' ('+value.shortDesc+')</option>');
		}
		
	});
	
}

/* manage data list */
function manageData() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_view-parameter-calibration.php?idParameter='+idParameter,
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
		url: url+'api/getData_view-parameter-calibration.php?idParameter='+idParameter+'&showAll=1',
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
		url: url+'api/getData_view-parameter-calibration.php?idParameter='+idParameter,
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
	  	rows = rows + '<td>'+value.name+'</td>';
	  	rows = rows + '<td>'+value.shortDesc+'</td>';
	  	rows = rows + '<td>'+value.setting+'</td>';
	  	rows = rows + '<td data-id="'+value.id+'">';
        if (userrole.value < 4) {
        rows = rows + '<button data-toggle="modal" data-target="#edit-item" class="btn btn-primary edit-item">Edit</button> ';
        if (userrole.value < 3) {
        rows = rows + '<button class="btn btn-danger remove-item">Delete</button>';
        }
        }
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
    var id = $("#create-item").find("input[name='id']").val();
    var idParameter = $("#create-item").find("input[name='idParameter']").val();
    /*var type = $("#create-item").find("input[name='type']").val();
    var name = $("#create-item").find("input[name='name']").val();
    var shortDesc = $("#create-item").find("input[name='shortDesc']").val();
    var setting = $("#create-item").find("textarea[name='setting']").val();*/
    var calcurve = $("#create-item").find("select[name='calcurve']").val();

    if(idParameter != '' && calcurve != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, idParameter:idParameter, calcurve:calcurve}
        }).done(function(data){
            //$("#create-item").find("input[name='id']").val('');
            $("#create-item").find("input[name='idParameter']").val('');
            $("#create-item").find("select[name='calcurve']").val('');
            /*$("#create-item").find("input[name='name']").val('');
            $("#create-item").find("input[name='shortDesc']").val('');
            $("#create-item").find("textarea[name='setting']").val('');*/
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Item Added Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing title or description.')
    }

});

/* Remove Item */
$("body").on("click",".remove-item",function(){
	var id = $(this).parent("td").data('id');
    var idParameter = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
	var c_obj = $(this).parents("tr");
	
	var confirmation = confirm("Are you sure you want to remove this item?");
	if (confirmation) {
		$.ajax({
			dataType: 'json',
			type:'POST',
			url: url + 'api/delete_view-parameter-calibration.php',
			data:{id:id, idParameter:idParameter}
		}).done(function(data){
			c_obj.remove();
			toastr.success('Item Deleted Successfully.', 'Success Alert', {timeOut: 5000});
			getPageData();
		});
		/*alert("Deleted!")*/
	}
});

/* Edit Item */
$("body").on("click",".edit-item",function(){

    var id = $(this).parent("td").data('id');
    var idParameter = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var type = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var name = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var shortDesc = $(this).parent("td").prev("td").prev("td").text();
    var setting = $(this).parent("td").prev("td").text();

    getDropdownDataCalCurve(id);

    $("#edit-item").find("input[name='idParameter']").val(idParameter);
    $("#edit-item").find("input[name='type']").val(type);
    $("#edit-item").find("input[name='name']").val(name);
    $("#edit-item").find("input[name='shortDesc']").val(shortDesc);
    $("#edit-item").find("textarea[name='setting']").val(setting);
    $("#edit-item").find(".edit-id").val(id);

});

/* Updated new Item */
$(".crud-submit-edit").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-item").find("form").attr("action");
    var idParameter = $("#edit-item").find("input[name='idParameter']").val();
    var calcurve = $("#edit-item").find("select[name='calcurve']").val();
    var type = $("#edit-item").find("input[name='type']").val();
    var name = $("#edit-item").find("input[name='name']").val();
    var shortDesc = $("#edit-item").find("input[name='shortDesc']").val();
    var setting = $("#edit-item").find("textarea[name='setting']").val();
    var id = $("#edit-item").find(".edit-id").val();

    if(id != '' && idParameter != '' && calcurve != '' && type != '' && name != '' && shortDesc != '' && setting != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, idParameter:idParameter, calcurve:calcurve, type:type, name:name, shortDesc:shortDesc, setting:setting}
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