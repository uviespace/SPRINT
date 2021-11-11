$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;
var dropdown = "";

var idProject = getUrlVars()["idProject"];

manageData();
getDropdownDataAcronymCreate();

/* get variables from URL */
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

/* Get Dropdown Data for Acronym */
function getDropdownDataAcronym(acronym) {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-acronym.php',
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionAcronym(data.data, acronym);
	});
}

/* Add new option to select */
function manageOptionAcronym(data, acronym) {
	$("#sel_acronym").empty();
	$.each( data, function( key, value ) {
		if (acronym==value.id) {
			$("#sel_acronym").append('<option value="'+value.id+'" selected>'+value.name+' ('+value.shortDesc+')</option>');
		} else {
			$("#sel_acronym").append('<option value="'+value.id+'">'+value.name+' ('+value.shortDesc+')</option>');
		}
	});
}

/* Get Dropdown Data for Acronym */
function getDropdownDataAcronymCreate() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-acronym.php',
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionAcronymCreate(data.data);
	});
}

/* Add new option to select */
function manageOptionAcronymCreate(data) {
	$("#sel_acronym_create").empty();
	$("#sel_acronym_create").append('<option value="" selected>Please select ...</option>');
	$.each( data, function( key, value ) {
		$("#sel_acronym_create").append('<option value="'+value.id+'">'+value.name+' ('+value.shortDesc+')</option>');
	});
}

/* manage data list */
function manageData() {
    console.log("2# "+url+'api/getData_view-project-acronym.php?idProject='+idProject);
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_view-project-acronym.php?idProject='+idProject,
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

		$("#result_nmb").val(data.total);

		manageRow(data.data);
		is_ajax_fire = 1;

	});

}

/* Get Page Data*/
function getPageData() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_view-project-acronym.php?idProject='+idProject,
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
        rows = rows + '<td>'+value.idAcronym+'</td>';
        rows = rows + '<td>'+value.name+'</td>';
        rows = rows + '<td>'+value.shortDesc+'</td>';
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
    e.preventDefault();
    var form_action = $("#create-item").find("form").attr("action-data");
    var idProject = $("#create-item").find("input[name='idProject']").val();
    var idAcronym = $("#create-item").find("select[name='idAcronym_create']").val();

    if(idProject != '' && idAcronym != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{idProject:idProject, idAcronym:idAcronym}
        }).done(function(data){
            $("#create-item").find("input[name='idProject']").val('');
            $("#create-item").find("select[name='idAcronym_create']").val('');
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
		url: url + 'api/delete_view-project-acronym.php',
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
    var idAcronym = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var name = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var shortDesc = $(this).parent("td").prev("td").prev("td").text();
    var desc = $(this).parent("td").prev("td").text();

    getDropdownDataAcronym(idAcronym);
    
    $("#edit-item").find("select[name='idAcronym']").val(name);
    $("#edit-item").find("input[name='name']").val(name);
    $("#edit-item").find("input[name='shortDesc']").val(shortDesc);
    $("#edit-item").find("textarea[name='desc']").val(desc);
    $("#edit-item").find(".edit-id").val(id);

});

/* Updated new Item */
$(".crud-submit-edit").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-item").find("form").attr("action");
    var idAcronym = $("#edit-item").find("select[name='idAcronym']").val();
    var id = $("#edit-item").find(".edit-id").val();

    if(id != '' && idAcronym != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, idAcronym:idAcronym}
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
    console.log('A>> '+url+'api/getData_dd-organisation.php?id='+disc);
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-acronym.php?id='+disc, // TODO: id=disc does not work
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionDiscriminantDescriptionCreate(data.data, disc);
	});
}

/* Insert value to textarea */
function manageOptionDiscriminantDescriptionCreate(data, disc) {
    console.log('B>> '+disc);
    $("#name_create").empty();
    $("#shortDesc_create").empty();
    $("#desc_create").empty();
    $.each( data, function( key, value ) {
        console.log('B>> value.id '+value.id);
        console.log('B>> value.name '+value.name);
        if (disc==value.id) {
            console.log('B>> set fields ... ');
            $("#name_create").val(value.name);
            $("#shortDesc_create").val(value.shortDesc);
            $("#desc_create").val(value.desc);
        }
	});
}

function updateDivAcronymCreate() {
	var x = document.getElementById("sel_acronym_create");
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
		url: url+'api/getData_dd-acronym.php?id='+disc, // TODO: id=disc does not work
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionDiscriminantDescription(data.data, disc);
	});
}

/* Insert value to textarea */
function manageOptionDiscriminantDescription(data, disc) {
    $("#name").empty();
    $("#shortDesc").empty();
    $("#desc").empty();
    $.each( data, function( key, value ) {
        if (disc==value.id) {
            $("#name").val(value.name);
            $("#shortDesc").val(value.shortDesc);
            $("#desc").val(value.desc);
        }
	});
}

function updateDivAcronym() {
	var x = document.getElementById("sel_acronym");
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