$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;
var dropdown = "";

var idProject = getUrlVars()["idProject"];
var idReqList = getUrlVars()["idReqList"];

manageData();

getDropdownDataReqStdCreate();

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

/* Get Dropdown Data for ReqStd */
function getDropdownDataReqStdCreate() {
    var data = { 
        "data": [ 
            { "id":"5", "name":"ECSS-E-ST-70-11C(31July2008)", "shortDesc":"[OPER]" }, 
            { "id":"6", "name":"ECSS-E-ST-40C(6March2009)", "shortDesc":"[E40 Std.]" }, 
            { "id":"7", "name":"ECSS-Q-ST-80C(6March2009)", "shortDesc":"[Q80 Std.]" }, 
            { "id":"8", "name":"ECSS-E-70-41A(30Jan2003)", "shortDesc":"[PUS-A]" }, 
            { "id":"9", "name":"ECSS-E-ST-70-41C(15April2016)", "shortDesc":"[PUS-C]" }
        ]
    };
    manageOptionReqStdCreate(data.data);
}

/* Add new option to select */
function manageOptionReqStdCreate(data) {
	$("#sel_reqstd_create").empty();
	$("#sel_reqstd_create").append('<option value="" selected>Please select ...</option>');
	$.each( data, function( key, value ) {
		$("#sel_reqstd_create").append('<option value="'+value.id+'">'+value.name+' '+value.shortDesc+'</option>');
	});
}

/* Get Dropdown Data for ReqStd */
function getDropdownDataApplicability(applicability) {
    var data = { 
        "data": [ 
            { "id":"", "name":"", "shortDesc":"-" }, 
            { "id":"A", "name":"A", "shortDesc":"Applicable" }, 
            { "id":"D", "name":"D", "shortDesc":"Deleted" }, 
            { "id":"M", "name":"M", "shortDesc":"Modified" }, 
            { "id":"N", "name":"N", "shortDesc":"New" }
        ]
    };
    manageOptionApplicabilityCreate(data.data, applicability);
}

/* Add new option to select */
function manageOptionApplicabilityCreate(data, applicability) {
	$("#sel_applicability").empty();
	//$("#sel_applicability").append('<option value="" selected>Please select ...</option>');
	$.each( data, function( key, value ) {
        if (applicability==value.name) {
			$("#sel_applicability").append('<option value="'+value.id+'" selected>'+value.name+' ('+value.shortDesc+')</option>');
		} else {
			$("#sel_applicability").append('<option value="'+value.id+'">'+value.name+' ('+value.shortDesc+')</option>');
		}
	});
}

/* Get Dropdown Data for ReqStd */
function getDropdownDataApplicableToPL(applicableToPL) {
    var data = { 
        "data": [ 
            { "id":"", "name":"", "shortDesc":"-" }, 
            { "id":"M", "name":"M", "shortDesc":"Mandatory" }, 
            { "id":"O", "name":"O", "shortDesc":"Optional" }
        ]
    };
    manageOptionApplicableToPL(data.data, applicableToPL);
}

/* Add new option to select */
function manageOptionApplicableToPL(data, applicableToPL) {
	$("#sel_applicableToPL").empty();
	//$("#sel_applicableToPL").append('<option value="" selected>Please select ...</option>');
	$.each( data, function( key, value ) {
        if (applicableToPL==value.name) {
			$("#sel_applicableToPL").append('<option value="'+value.id+'" selected>'+value.name+' ('+value.shortDesc+')</option>');
		} else {
			$("#sel_applicableToPL").append('<option value="'+value.id+'">'+value.name+' ('+value.shortDesc+')</option>');
		}
	});
}

/* manage data list */
function manageData() {
    console.log("2# "+url+'api/getData_view-project-requirement-internal.php?idProject='+idProject+'&idReqList='+idReqList);
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_view-project-requirement-internal.php?idProject='+idProject+'&idReqList='+idReqList,
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
		url: url+'api/getData_view-project-requirement-internal.php?idProject='+idProject+'&idReqList='+idReqList,
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
        //rows = rows + '<td>'+value.idAcronym+'</td>';
        rows = rows + '<td>'+value.requirementId+'</td>';
        rows = rows + '<td>'+value.clause+' ('+value.idDocVersion+')</td>';
        //rows = rows + '<td>'+value.shortDesc+'</td>';
        rows = rows + '<td>'+value.desc+'</td>';
        rows = rows + '<td>'+value.notes+'</td>';
        rows = rows + '<td>'+value.justification+'</td>';
        rows = rows + '<td>'+value.applicability+'</td>';
        rows = rows + '<td>'+value.applicableToPayloads+'</td>';
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
    var requirementId = $("#create-item").find("input[name='requirementId']").val();
    var idReqStd_create = $("#create-item").find("select[name='idReqStd_create']").val();
    var idRequirement_create = $("#create-item").find("select[name='idRequirement_create']").val();

    console.log('idProject = '+idProject);
    console.log('requirementId = '+requirementId);
    console.log('idReqStd_create = '+idReqStd_create);
    console.log('idRequirement_create = '+idRequirement_create);

    if(idProject != '' && requirementId != '' && idReqStd_create != '' && idRequirement_create != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{idProject:idProject, requirementId:requirementId, idReqStd_create:idReqStd_create, idRequirement_create:idRequirement_create}
        }).done(function(data){
            $("#create-item").find("input[name='idProject']").val('');
            $("#create-item").find("input[name='requirementId']").val('');
            $("#create-item").find("select[name='idReqStd_create']").val('');
            $("#create-item").find("select[name='idRequirement_create']").val('');
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Item Created Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing requirement id, requirement standard or requirement (clause).')
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
		url: url + 'api/delete_view-project-requirement-internal.php',
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
    var desc = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var notes = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var justification = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var applicability = $(this).parent("td").prev("td").prev("td").text();
    var applicableToPL = $(this).parent("td").prev("td").text();

    getDropdownDataApplicability(applicability);
    getDropdownDataApplicableToPL(applicableToPL);

    $("#edit-item").find("textarea[name='desc']").val(desc);
    $("#edit-item").find("textarea[name='notes']").val(notes);
    $("#edit-item").find("textarea[name='justification']").val(justification);
    $("#edit-item").find("select[name='applicability']").val(applicability);
    $("#edit-item").find("select[name='applicableToPL']").val(applicableToPL);

    $("#edit-item").find(".edit-id").val(id);

});

/* Updated new Item */
$(".crud-submit-edit").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-item").find("form").attr("action");
    var desc = $("#edit-item").find("textarea[name='desc']").val();
    var notes = $("#edit-item").find("textarea[name='notes']").val();
    var justification = $("#edit-item").find("textarea[name='justification']").val();
    var applicability = $("#edit-item").find("select[name='applicability']").val();
    var applicableToPL = $("#edit-item").find("select[name='applicableToPL']").val();
    var id = $("#edit-item").find(".edit-id").val();

    if(id != '' && desc != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, desc:desc, notes:notes, justification:justification, applicability:applicability, applicableToPL:applicableToPL}
        }).done(function(data){
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Item Updated Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing the description.')
    }

});

});

/*### GLOBAL DEFINED FUNCTIONS */

var dropdown = ""; // needed for dropdown menus for initialization

/* Get Description Data for Discriminant */
function getDropdownDataRequirementCreate(disc) {
    console.log('A>> '+url+'api/getData_dd-requirement.php?idReqList='+disc);
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-requirement.php?idReqList='+disc,
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionRequirementCreate(data.data, disc);
	});
}

/* Insert value to textarea */
function manageOptionRequirementCreate(data, disc) {
    console.log('B>> '+disc);
    $("#sel_requirement_create").empty();
    $("#sel_requirement_create").append('<option value="" selected>Please select ...</option>');
    $.each( data, function( key, value ) {
        /*if (disc==value.id) {
            $("#sel_requirement_create").append('<option value="'+value.id+'" selected>'+value.clause+' ('+value.shortDesc+')</option>');
        } else {*/
            $("#sel_requirement_create").append('<option value="'+value.id+'">'+value.clause+' ('+value.shortDesc+') '+value.desc.substr(0,50)+' ...</option>');
        //}
    });
}

function updateDivReqStdCreate() {
	var x = document.getElementById("sel_reqstd_create");
	//var ele = document.getElementById("descr");

	//ele.style.display = "block";

    //x.value = x.value.toUpperCase();

    //updateDiv();

    console.log(x.value);

    getDropdownDataRequirementCreate(""+x.value);

    //var container = document.getElementById("sel_pusdatatype");
    //var content = container.innerHTML;
    //container.innerHTML= content; 

    //$('#pusdatatype').trigger('change');
}

/* Get Description Data for Discriminant */
function getDropdownDataRequirementDescriptionCreate(disc) {
    console.log('A>> '+url+'api/getData_dd-requirement.php?id='+disc);
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-requirement.php?id='+disc, // TODO: id=disc does not work
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionRequirementDescriptionCreate(data.data, disc);
	});
}

/* Insert value to textarea */
function manageOptionRequirementDescriptionCreate(data, disc) {
    console.log('B>> '+disc);
    $("#clause_create").empty();
    $("#shortDesc_create").empty();
    $("#desc_create").empty();
    $.each( data, function( key, value ) {
        console.log('B>> value.id '+value.id);
        console.log('B>> value.clause '+value.clause);
        if (disc==value.id) {
            console.log('B>> set fields ... ');
            $("#clause_create").val(value.clause);
            $("#shortDesc_create").val(value.shortDesc);
            $("#desc_create").val(value.desc);
        }
	});
}

function updateDivRequirementCreate() {
	var x = document.getElementById("sel_requirement_create");
	//var ele = document.getElementById("descr");

	//ele.style.display = "block";

    //x.value = x.value.toUpperCase();

    //updateDiv();

    console.log(x.value);

    getDropdownDataRequirementDescriptionCreate(""+x.value);

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