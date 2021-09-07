$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;
var dropdown = "";

var idProject = getUrlVars()["idProject"];

    getDropdownDataPackageCreate();

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
		url: url+'api/getData_view-dataPack.php?idProject='+idProject,
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
		url: url+'api/getData_view-dataPack.php?idProject='+idProject+'&showAll=1',
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
		url: url+'api/getData_view-dataPack.php?idProject='+idProject,
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
	  	rows = rows + '<td class=\"hidden\">'+value.idPackage+'</td>';
	  	rows = rows + '<td>'+value.pname+' - '+value.pdesc+'</td>';
	  	rows = rows + '<td>'+value.note+'</td>';
	  	rows = rows + '<td data-id="'+value.id+'">';
        rows = rows + '<button data-toggle="modal" data-target="#edit-item" class="btn btn-primary edit-item">Edit</button> ';
        rows = rows + '<button class="btn btn-danger remove-item">Delete</button>';
        rows = rows + '</td>';
	  	rows = rows + '</tr>';
	});

	$("tbody").html(rows);
}

/* Get Dropdown Data for Process/APID */
function getDropdownDataPackageCreate() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-package.php',
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionPackageCreate(data.data);
	});
}

/* Add new option to select */
function manageOptionPackageCreate(data) {
	$("#sel_package_create").empty();
	$("#sel_package_create").append('<option value="" selected>--- Please select ---</option>');
	$.each( data, function( key, value ) {
		$("#sel_package_create").append('<option value="'+value.id+'">'+value.name+' - '+value.desc+' ('+value.id+')</option>');
	});
}

/* Get Dropdown Data for Package */
function getDropdownDataPackage(idpackage) {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-package.php',
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionPackage(data.data, idpackage);
	});
}

/* Add new option to select */
function manageOptionPackage(data, idpackage) {
	$("#sel_package").empty();
	$.each( data, function( key, value ) {
		if (idpackage==value.id) {
			$("#sel_package").append('<option value="'+value.id+'" selected>'+value.name+' - '+value.desc+' ('+value.id+')</option>');
		} else {
			$("#sel_package").append('<option value="'+value.id+'">'+value.name+' - '+value.desc+' ('+value.id+')</option>');
		}
	});
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
    var idPackage = $("#create-item").find("select[name='idPackage']").val();
    var name = $("#create-item").find("input[name='name']").val();
    var note = $("#create-item").find("textarea[name='note']").val();

    if(idProject != '' && idPackage != '' && name != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{idProject:idProject, idPackage:idPackage, name:name, note:note},
            success: function(results, textStatus) {
                toastr.success('Database Operation Successfully. ' + results, 'Success Alert', {timeOut: 5000});
            },
            error: function(xhr, status, error)
            {
                toastr.error('Database Operation Failed. ' + xhr.responseText, 'Failure Alert', {timeOut: 5000});
            }
        }).done(function(data){
            $("#create-item").find("input[name='idProject']").val('');
            $("#create-item").find("input[name='idPackage']").val('');
            $("#create-item").find("input[name='name']").val('');
            $("#create-item").find("textarea[name='note']").val('');
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Item Created Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing package or name.')
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
            url: url + 'api/delete_view-dataPack.php',
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
    var name = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var idPackage = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var basepackage = $(this).parent("td").prev("td").prev("td").text();
    var note = $(this).parent("td").prev("td").text();
    
    getDropdownDataPackage(idPackage)

    $("#edit-item").find("input[name='name']").val(name);
    $("#edit-item").find("select[name='idPackage']").val(idPackage);
    $("#edit-item").find("textarea[name='note']").val(note);
    $("#edit-item").find(".edit-id").val(id);

});

/* Updated new Item */
$(".crud-submit-edit").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-item").find("form").attr("action");
    var name = $("#edit-item").find("input[name='name']").val();
    var idPackage = $("#edit-item").find("select[name='idPackage']").val();
    var note = $("#edit-item").find("textarea[name='note']").val();
    var id = $("#edit-item").find(".edit-id").val();

    console.log(">>> name = "+name);
    console.log(">>> idPackage = "+idPackage);
    console.log(">>> note = "+note);
    console.log(">>> id = "+id);
    
    if(id != '' && name != '' && idPackage != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, name:name, idPackage:idPackage, note:note}
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