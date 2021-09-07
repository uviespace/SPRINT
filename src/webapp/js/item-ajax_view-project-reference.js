$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;
var dropdown = "";

var idProject = getUrlVars()["idProject"];

console.log("1# "+idProject);

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
    console.log("2# "+url+'api/getData_view-project-reference.php?idProject='+idProject);
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_view-project-reference.php?idProject='+idProject,
		data: {page:page}
	}).done(function(data){
		total_page = Math.ceil(data.total/5);
		current_page = page;
		if (data.total == 0) { // to avoid pagination-error: "Uncaught Error: Start page option is incorrect"
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

		manageRow(data.data);
		is_ajax_fire = 1;

	});

}

/* Get Page Data*/
function getPageData() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_view-project-reference.php?idProject='+idProject,
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
        rows = rows + '<td>'+value.idReference+'</td>';
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
    var idProject = $("#create-item").find("input[name='idProject']").val();
    var idReference = $("#create-item").find("select[name='idReference_create']").val();

    console.log("DEBUG CREATE: idProject = "+idProject+"; idReference = "+idReference);

    if(idProject != '' && idReference != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{idProject:idProject, idReference:idReference}
        }).done(function(data){
            $("#create-item").find("input[name='idProject']").val('');
            $("#create-item").find("select[name='idReference_create']").val('');
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
		url: url + 'api/delete_view-project-reference.php',
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
    var idReference = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var identifier = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var name = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var type = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var version = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var date = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var organisation = $(this).parent("td").prev("td").prev("td").text();
    var filename = $(this).parent("td").prev("td").text();

    console.log("DEBUG: id = idReference ("+idReference+")");

    getDropdownDataReference(idReference);

    $("#edit-item").find("select[name='idReference']").val(name);
    $("#edit-item").find("input[name='name']").val(name);
    $("#edit-item").find("input[name='identifier']").val(identifier);
    $("#edit-item").find("input[name='version']").val(version);
    $("#edit-item").find("input[name='date']").val(date);
    $("#edit-item").find("input[name='filename']").val(filename);
    $("#edit-item").find(".edit-id").val(id);

});

/* Updated new Item */
$(".crud-submit-edit").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-item").find("form").attr("action");
    var idReference = $("#edit-item").find("select[name='idReference']").val();
    var id = $("#edit-item").find(".edit-id").val();

    console.log(">>> procectdocument.id = "+id);
    console.log(">>> docversion.id = idReference = "+idReference+" -> document.id");
    
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