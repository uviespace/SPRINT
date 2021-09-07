$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;
var dropdown = "";

var idDataPack = getUrlVars()["idDataPack"];

manageData();
getDropdownDataReferenceCreate();

/* get variables from URL */
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

/* Get Dropdown Data for Reference */
function getDropdownDataReference(reference) {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-reference.php',
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionReference(data.data, reference);
	});
}

/* Add new option to select */
function manageOptionReference(data, reference) {
	$("#sel_reference").empty();
	$.each( data, function( key, value ) {
        if (value.identifier==null) {
			if (reference==value.vid) {
				$("#sel_reference").append('<option value="'+value.vid+'" selected>'+value.shortName+'-'+value.number+' / '+value.name+' (v'+value.version+', '+value.date+')</option>');
			} else {
				$("#sel_reference").append('<option value="'+value.vid+'">'+value.shortName+'-'+value.number+' / '+value.name+' (v'+value.version+', '+value.date+')</option>');
			}
        } else {
			if (reference==value.vid) {
				$("#sel_reference").append('<option value="'+value.vid+'" selected>'+value.identifier+' / '+value.name+' (v'+value.version+', '+value.date+')</option>');
			} else {
				$("#sel_reference").append('<option value="'+value.vid+'">'+value.identifier+' / '+value.name+' (v'+value.version+', '+value.date+')</option>');
			}
        }
	});
}

/* Get Dropdown Data for Reference */
function getDropdownDataReferenceCreate() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-reference.php',
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionReferenceCreate(data.data);
	});
}

/* Add new option to select */
function manageOptionReferenceCreate(data) {
	$("#sel_reference_create").empty();
	$("#sel_reference_create").append('<option value="" selected>Please select ...</option>');
	$.each( data, function( key, value ) {
        if (value.identifier==null) {
            $("#sel_reference_create").append('<option value="'+value.vid+'">'+value.shortName+'-'+value.number+' / '+value.name+' (v'+value.version+', '+value.date+')</option>');
        } else {
            $("#sel_reference_create").append('<option value="'+value.vid+'">'+value.identifier+' / '+value.name+' (v'+value.version+', '+value.date+')</option>');
        }
	});
}

/* manage data list */
function manageData() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_view-dataPack-document.php?idDataPack='+idDataPack,
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
		url: url+'api/getData_view-dataPack-document.php?idDataPack='+idDataPack+'&showAll=1',
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
		url: url+'api/getData_view-dataPack-document.php?idDataPack='+idDataPack,
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
        rows = rows + '<td>'+value.idReference+'</td>';
        rows = rows + '<td>'+value.shortName+'</td>';
        rows = rows + '<td>'+value.number+'</td>';
	  	rows = rows + '<td>'+value.name+'</td>';
	  	rows = rows + '<td>'+value.version+'</td>';
	  	rows = rows + '<td>'+value.date+'</td>';
	  	rows = rows + '<td data-id="'+value.id+'">';
        rows = rows + '<button data-toggle="modal" data-target="#edit-item" class="btn btn-primary edit-item">Change</button> ';
        rows = rows + '<button class="btn btn-danger remove-item">Remove</button>';
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
    var idDataPack = $("#create-item").find("input[name='idDataPack']").val();
    var idReference = $("#create-item").find("select[name='idReference_create']").val();
    var note = "";

    console.log(">>> idDataPack = "+idDataPack);
    console.log(">>> idReference = "+idReference);
    console.log(">>> note = "+note);

    if(idDataPack != '' && idReference != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{idDataPack:idDataPack, idReference:idReference},
            success: function(results, textStatus) {
                toastr.success('Database Operation Successfully. ' + results, 'Success Alert', {timeOut: 5000});
            },
            error: function(xhr, status, error)
            {
                toastr.error('Database Operation Failed. ' + xhr.responseText, 'Failure Alert', {timeOut: 5000});
            }
        }).done(function(data){
            $("#create-item").find("input[name='idDataPack']").val('');
            $("#create-item").find("select[name='idReference_create']").val('');
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Item Created Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing document reference.')
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
            url: url + 'api/delete_view-dataPack-document.php',
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
    var idReference = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var shortName = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var number = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var name = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var version = $(this).parent("td").prev("td").prev("td").text();
    var date = $(this).parent("td").prev("td").text();

    getDropdownDataReference(idReference);

    $("#edit-item").find("input[name='idReference']").val(idReference);
    $("#edit-item").find("input[name='shortName']").val(shortName);
    $("#edit-item").find("input[name='name']").val(name);
    $("#edit-item").find("input[name='number']").val(number);
    $("#edit-item").find("input[name='version']").val(version);
    $("#edit-item").find("input[name='date']").val(date);
    $("#edit-item").find(".edit-id").val(id);

});

/* Updated new Item */
$(".crud-submit-edit").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-item").find("form").attr("action");
    var idReference = $("#edit-item").find("select[name='idReference']").val();
    var id = $("#edit-item").find(".edit-id").val();

    if(id != '' && idReference != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, idReference:idReference}
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

/*### GLOBAL DEFINED FUNCTIONS */

var dropdown = ""; // needed for dropdown menus for initialization

/* Get Description Data for Discriminant */
function getDropdownDataDiscriminantDescriptionCreate(disc) {
    console.log('A>> '+url+'api/getData_dd-reference.php?id='+disc);
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-reference.php?id='+disc, // TODO: id=disc does not work
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionDiscriminantDescriptionCreate(data.data, disc);
	});
}

/* Insert value to textarea */
function manageOptionDiscriminantDescriptionCreate(data, disc) {
    console.log('B>> '+disc);
    $("#name_create").empty();
    $("#identifier_create").empty();
    $("#version_create").empty();
    $("#date_create").empty();
    $("#filename_create").empty();
    $.each( data, function( key, value ) {
        console.log('B>> value.id '+value.vid);
        console.log('B>> value.name '+value.name);
        if (disc==value.vid) {
            console.log('B>> set fields ... ');
            $("#name_create").val(value.name);
            $("#identifier_create").val(value.identifier);
            $("#version_create").val(value.version);
            $("#date_create").val(value.date);
            $("#filename_create").val(value.filename);
        }
	});
}

function updateDivReferenceCreate() {
	var x = document.getElementById("sel_reference_create");
	//var ele = document.getElementById("descr");

	//ele.style.display = "block";

    //x.value = x.value.toUpperCase();

    //updateDiv();

    console.log(x.value);

    getDropdownDataDiscriminantDescriptionCreate(""+x.value);

    //var container = document.getElementById("sel_pusdatatype");
    //var content = container.innerHTML;
    //container.innerHTML= content; 

    //$('#pusdatatype').trigger('change');
}

/* Get Description Data for Discriminant */
function getDropdownDataDiscriminantDescription(disc) {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-reference.php?id='+disc, // TODO: id=disc does not work
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionDiscriminantDescription(data.data, disc);
	});
}

/* Insert value to textarea */
function manageOptionDiscriminantDescription(data, disc) {
    $("#name").empty();
    $("#identifier").empty();
    $("#version").empty();
    $("#date").empty();
    $("#filename").empty();
    $.each( data, function( key, value ) {
        if (disc==value.vid) {
            $("#name").val(value.name);
            $("#identifier").val(value.identifier);
            $("#version").val(value.version);
            $("#date").val(value.date);
            $("#filename").val(value.filename);
        }
	});
}

function updateDivReference() {
	var x = document.getElementById("sel_reference");
	//var ele = document.getElementById("descr");

	//ele.style.display = "block";

    //x.value = x.value.toUpperCase();

    //updateDiv();
    
    console.log(x.value);

    getDropdownDataDiscriminantDescription(""+x.value);

    //var container = document.getElementById("sel_pusdatatype");
    //var content = container.innerHTML;
    //container.innerHTML= content; 

    //$('#pusdatatype').trigger('change');
}