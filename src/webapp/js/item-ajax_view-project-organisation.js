$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;
var dropdown = "";

var idProject = getUrlVars()["idProject"];

console.log("1# "+idProject);

manageData();
//getDropdownDataCountryCreate();
getDropdownDataOrganisationCreate();

/* get variables from URL */
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

/* Get Dropdown Data for Country */
/*function getDropdownDataCountry(country) {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-country.php',
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionCountry(data.data, country);
	});
}*/

/* Add new option to select */
/*function manageOptionCountry(data, country) {
	$("#sel_country").empty();
	$.each( data, function( key, value ) {
		if (country==value.id) {
			$("#sel_country").append('<option value="'+value.id+'" selected>'+value.name+'</option>');
		} else {
			$("#sel_country").append('<option value="'+value.id+'">'+value.name+'</option>');
		}
	});
}*/

/* Get Dropdown Data for Country */
/*function getDropdownDataCountryCreate() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-country.php',
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionCountryCreate(data.data);
	});
}*/

/* Add new option to select */
/*function manageOptionCountryCreate(data) {
	$("#sel_country_create").empty();
	$("#sel_country_create").append('<option value="" selected>Please select ...</option>');
	$.each( data, function( key, value ) {
		$("#sel_country_create").append('<option value="'+value.id+'">'+value.name+'</option>');
	});
}*/

/* Get Dropdown Data for Organisation */
function getDropdownDataOrganisation(organisation) {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-organisation.php',
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionOrganisation(data.data, organisation);
	});
}

/* Add new option to select */
function manageOptionOrganisation(data, organisation) {
	$("#sel_organisation").empty();
	$.each( data, function( key, value ) {
		if (organisation==value.id) {
			$("#sel_organisation").append('<option value="'+value.id+'" selected>'+value.name+' ('+value.shortDesc+')</option>');
		} else {
			$("#sel_organisation").append('<option value="'+value.id+'">'+value.name+' ('+value.shortDesc+')</option>');
		}
	});
}

/* Get Dropdown Data for Organisation */
function getDropdownDataOrganisationCreate() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-organisation.php',
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionOrganisationCreate(data.data);
	});
}

/* Add new option to select */
function manageOptionOrganisationCreate(data) {
	$("#sel_organisation_create").empty();
	$("#sel_organisation_create").append('<option value="" selected>Please select ...</option>');
	$.each( data, function( key, value ) {
		$("#sel_organisation_create").append('<option value="'+value.id+'">'+value.name+' ('+value.shortDesc+')</option>');
	});
}

/* manage data list */
function manageData() {
    console.log("2# "+url+'api/getData_view-project-organisation.php?idProject='+idProject);
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_view-project-organisation.php?idProject='+idProject,
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
		url: url+'api/getData_view-project-organisation.php?idProject='+idProject,
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
        rows = rows + '<td>'+value.idOrg+'</td>';
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
    e.preventDefault();
    var form_action = $("#create-item").find("form").attr("action-data");
    var idProject = $("#create-item").find("input[name='idProject']").val();
    var idOrg = $("#create-item").find("select[name='idOrg_create']").val();

    console.log("DEBUG CREATE: idProject = "+idProject+"; idOrg = "+idOrg);

    if(idProject != '' && idOrg != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{idProject:idProject, idOrg:idOrg}
        }).done(function(data){
            $("#create-item").find("input[name='idProject']").val('');
            $("#create-item").find("select[name='idOrg_create']").val('');
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
		url: url + 'api/delete_view-project-organisation.php',
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
    var idOrg = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var name = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var shortDesc = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var idCountry = $(this).parent("td").prev("td").prev("td").text();
    var desc = $(this).parent("td").prev("td").text();

    console.log("DEBUG: id = idOrg ("+idOrg+")");

    //getDropdownDataCountry(idCountry);
    getDropdownDataOrganisation(idOrg);

    $("#edit-item").find("select[name='idOrg']").val(name);
    $("#edit-item").find("input[name='name']").val(name);
    $("#edit-item").find("input[name='shortDesc']").val(shortDesc);
    $("#edit-item").find("input[name='idCountry']").val(idCountry);
    $("#edit-item").find("textarea[name='desc']").val(desc);
    $("#edit-item").find(".edit-id").val(id);

});

/* Updated new Item */
$(".crud-submit-edit").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-item").find("form").attr("action");
    var idOrg = $("#edit-item").find("select[name='idOrg']").val();
    var id = $("#edit-item").find(".edit-id").val();

    console.log(">>> id = "+id);
    console.log(">>> idOrg = "+idOrg);
    
    if(id != '' && idOrg != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, idOrg:idOrg}
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
		url: url+'api/getData_dd-organisation.php?id='+disc, // TODO: id=disc does not work
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
    $("#idCountry_create").empty();
    $("#desc_create").empty();
    $.each( data, function( key, value ) {
        console.log('B>> value.id '+value.id);
        console.log('B>> value.name '+value.name);
        if (disc==value.id) {
            console.log('B>> set fields ... ');
            $("#name_create").val(value.name);
            $("#shortDesc_create").val(value.shortDesc);
            $("#idCountry_create").val(value.idCountry);
            $("#desc_create").val(value.desc);
        }
	});
}

function updateDivOrganisationCreate() {
	var x = document.getElementById("sel_organisation_create");
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
		url: url+'api/getData_dd-organisation.php?id='+disc, // TODO: id=disc does not work
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionDiscriminantDescription(data.data, disc);
	});
}

/* Insert value to textarea */
function manageOptionDiscriminantDescription(data, disc) {
    $("#name").empty();
    $("#shortDesc").empty();
    $("#idCountry").empty();
    $("#desc").empty();
    $.each( data, function( key, value ) {
        if (disc==value.id) {
            $("#name").val(value.name);
            $("#shortDesc").val(value.shortDesc);
            $("#idCountry").val(value.idCountry);
            $("#desc").val(value.desc);
        }
	});
}

function updateDivOrganisation() {
	var x = document.getElementById("sel_organisation");
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