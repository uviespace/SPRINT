$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;
var dropdown = "";

var idProject = getUrlVars()["idProject"];

manageData();
getDropdownDataReqCatCreate();
getDropdownDataReqTypeCreate();
getDropdownDataReqVerifCreate();
getDropdownDataTopLevReqCreate();

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

/* Get Dropdown Data for Requirement Category */
function getDropdownDataReqCatCreate() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-requirement-category.php?idProject='+idProject,
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionReqCatCreate(data.data);
	});
}

/* Add new option to select */
function manageOptionReqCatCreate(data) {
	$("#sel_reqcat_create").empty();
	$("#sel_reqcat_create").append('<option value="" selected>Please select ...</option>');
	$.each( data, function( key, value ) {
		$("#sel_reqcat_create").append('<option value="'+value.id+'">'+value.category+' ('+value.shortDesc+')</option>');
	});
}

/* Get Dropdown Data for Requirement Type */
function getDropdownDataReqTypeCreate() {
    var data = { 
        "data": [ 
            { "type":"", "name":"--- Please select ---", "desc":"" }, 
            { "type":"A", "name":"A", "desc":"Application Requirements" }, 
            { "type":"P", "name":"P", "desc":"Platform Requirements" },
            { "type":"U", "name":"U", "desc":"Use Requirements" }, 
            { "type":"S", "name":"S", "desc":"Sub-System Requirements" }
        ]
    };
    manageOptionReqTypeCreate(data.data);
}

/* Add new option to select */
function manageOptionReqTypeCreate(data) {
	$("#sel_reqtype_create").empty();
	$.each( data, function( key, value ) {
		if (value.type=="") {
			$("#sel_reqtype_create").append('<option value="'+value.type+'" selected>'+value.name+'</option>');
		} else {
			$("#sel_reqtype_create").append('<option value="'+value.type+'">'+value.name+' ('+value.desc+')</option>');
		}
	});
}

/* Get Dropdown Data for Requirement Verification */
function getDropdownDataReqVerifCreate() {
    var data = { 
        "data": [ 
            { "verif":"", "name":"--- Please select ---", "desc":"" }, 
            { "verif":"-", "name":"-", "desc":"No verification needed" }, 
            { "verif":"R", "name":"R", "desc":"Verification by Review" }, 
            { "verif":"A", "name":"A", "desc":"Verification by Analysis" },
            { "verif":"T", "name":"T", "desc":"Verification by Testing" }
        ]
    };
    manageOptionReqVerifCreate(data.data);
}

/* Add new option to select */
function manageOptionReqVerifCreate(data) {
	$("#sel_reqverif_create").empty();
	$.each( data, function( key, value ) {
		if (value.verif=="") {
			$("#sel_reqverif_create").append('<option value="'+value.verif+'" selected>'+value.name+'</option>');
		} else {
			$("#sel_reqverif_create").append('<option value="'+value.verif+'">'+value.name+' ('+value.desc+')</option>');
		}
	});
}

/* Get Dropdown Data for Top-Level Requirement */
function getDropdownDataTopLevReqCreate() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-requirement-subsystem.php?idProject='+idProject,
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionTopLevReqCreate(data.data);
        manageOptionTopLevReq2Create(data.data);
        manageOptionTopLevReq3Create(data.data);
	});
}

/* Add new option to select */
function manageOptionTopLevReqCreate(data) {
	$("#sel_tlreqid_create").empty();
	$("#sel_tlreqid_create").append('<option value="" selected>Please select ...</option>');
	$.each( data, function( key, value ) {
		$("#sel_tlreqid_create").append('<option value="'+value.id+'">'+value.requirementId+' ('+value.desc.substr(0,60)+'...)</option>');
	});
}

/* Add new option to select */
function manageOptionTopLevReq2Create(data) {
	$("#sel_tlreqid2_create").empty();
	$("#sel_tlreqid2_create").append('<option value="" selected>Please select ...</option>');
	$.each( data, function( key, value ) {
		$("#sel_tlreqid2_create").append('<option value="'+value.id+'">'+value.requirementId+' ('+value.desc.substr(0,60)+'...)</option>');
	});
}

/* Add new option to select */
function manageOptionTopLevReq3Create(data) {
	$("#sel_tlreqid3_create").empty();
	$("#sel_tlreqid3_create").append('<option value="" selected>Please select ...</option>');
	$.each( data, function( key, value ) {
		$("#sel_tlreqid3_create").append('<option value="'+value.id+'">'+value.requirementId+' ('+value.desc.substr(0,60)+'...)</option>');
	});
}

/* Get Dropdown Data for Top-Level Requirement */
function getDropdownDataTopLevReqChange(idReq) {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-requirement-subsystem.php?idProject='+idProject,
		data: {dropdown:dropdown}
	}).done(function(data){
        $.ajax({
            dataType: 'json',
            url: url+'api/getData_dd-requirement-subsystem.php?idProject='+idProject+'&idReq='+idReq
        }).done(function(result){
            manageOptionTopLevReqChange(data.data, result.data, idReq, 0);
            manageOptionTopLevReq2Change(data.data, result.data, idReq, 1);
            manageOptionTopLevReq3Change(data.data, result.data, idReq, 2);
        });
	});
}

/* Add new option to select */
function manageOptionTopLevReqChange(data, result, idReq, pos) {
    if (result.length>pos) {
        console.log("### "+idReq+" | "+result.length+' ### '+result[pos].requirementId+' ###');
    } else {
        console.log("### "+idReq+" | "+result.length+' ### NO REQ FOUND! ###');
    }
    $("#sel_tlreqid_change").empty();
    $("#sel_tlreqid_change").append('<option value="" selected>Please select ...</option>');
    $.each( data, function( key, value ) {
        if (result.length>pos) {
        if (result[pos].requirementId==value.requirementId) {
            $("#sel_tlreqid_change").append('<option value="'+value.id+'" selected>'+value.requirementId+' ('+value.desc.substr(0,60)+'...)</option>');
        } else {
            $("#sel_tlreqid_change").append('<option value="'+value.id+'">'+value.requirementId+' ('+value.desc.substr(0,60)+'...)</option>');
        }
        } else {
            $("#sel_tlreqid_change").append('<option value="'+value.id+'">'+value.requirementId+' ('+value.desc.substr(0,60)+'...)</option>');
        }
    });
}

/* Add new option to select */
function manageOptionTopLevReq2Change(data, result, idReq, pos) {
    if (result.length>pos) {
        console.log("### "+idReq+" | "+result.length+' ### '+result[pos].requirementId+' ###');
    } else {
        console.log("### "+idReq+" | "+result.length+' ### - ###');
    }
    $("#sel_tlreqid2_change").empty();
    $("#sel_tlreqid2_change").append('<option value="" selected>Please select ...</option>');
    $.each( data, function( key, value ) {
        if (result.length>pos) {
        if (result[pos].requirementId==value.requirementId) {
            $("#sel_tlreqid2_change").append('<option value="'+value.id+'" selected>'+value.requirementId+' ('+value.desc.substr(0,60)+'...)</option>');
        } else {
            $("#sel_tlreqid2_change").append('<option value="'+value.id+'">'+value.requirementId+' ('+value.desc.substr(0,60)+'...)</option>');
        };
        } else {
            $("#sel_tlreqid2_change").append('<option value="'+value.id+'">'+value.requirementId+' ('+value.desc.substr(0,60)+'...)</option>');
        }
    });
}

/* Add new option to select */
function manageOptionTopLevReq3Change(data, result, idReq, pos) {
    if (result.length>pos) {
        console.log("### "+idReq+" | "+result.length+' ### '+result[pos].requirementId+' ###');
    } else {
        console.log("### "+idReq+" | "+result.length+' ### - ###');
    }
    $("#sel_tlreqid3_change").empty();
    $("#sel_tlreqid3_change").append('<option value="" selected>Please select ...</option>');
    $.each( data, function( key, value ) {
        if (result.length>pos) {
        if (result[pos].requirementId==value.requirementId) {
            $("#sel_tlreqid3_change").append('<option value="'+value.id+'" selected>'+value.requirementId+' ('+value.desc.substr(0,60)+'...)</option>');
        } else {
            $("#sel_tlreqid3_change").append('<option value="'+value.id+'">'+value.requirementId+' ('+value.desc.substr(0,60)+'...)</option>');
        };
        } else {
            $("#sel_tlreqid3_change").append('<option value="'+value.id+'">'+value.requirementId+' ('+value.desc.substr(0,60)+'...)</option>');
        }
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
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_view-project-requirement-external-requ.php?idProject='+idProject,
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

/* manage data list for all items */
function manageDataAll() {
	is_ajax_fire = 0;

	$.ajax({
		dataType: 'json',
		url: url+'api/getData_view-project-requirement-external-requ.php?idProject='+idProject+'&showAll=1',
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
		url: url+'api/getData_view-project-requirement-external-requ.php?idProject='+idProject,
		data: {page:page}
	}).done(function(data){
		manageRow(data.data);
	});
}

/* Add new Item table row */
function manageRow(data) {
    var re = /,/gi;
    var val_clause = '';
    var	rows = '';
    $.each( data, function( key, value ) {
        if (value.setting == null) {
            value.setting = "";
        }
        if (value.clause != null) {
            val_clause = value.clause.replace(re, " <br/>");
        } else {
            val_clause = "---";
        }
        rows = rows + '<tr>';
        rows = rows + '<td>'+value.id+'</td>';
        rows = rows + '<td>'+value.requirementId+'</td>';
        rows = rows + '<td>'+val_clause+'</td>';
        rows = rows + '<td class=\'hidden\'>'+value.shortDesc+'</td>';
        rows = rows + '<td>'+value.desc+'</td>';
        rows = rows + '<td>'+value.notes+'</td>';
        rows = rows + '<td>'+value.justification+'</td>';
        rows = rows + '<td>'+value.applicability+'</td>';
        rows = rows + '<td>'+value.applicableToPayloads+'</td>';
        rows = rows + '<td data-id="'+value.id+'">';
        rows = rows + '<button data-toggle="modal" data-target="#edit-item" class="btn btn-primary edit-item">Edit</button> ';
        rows = rows + '<button data-toggle="modal" data-target="#change-status" class="btn btn-success change-status">Chg</button> ';
        rows = rows + '<button class="btn btn-danger remove-item">Del</button>';
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
    //e.preventDefault();
    var form_action = $("#create-item").find("form").attr("action-data");
    var idProject = $("#create-item").find("input[name='idProject']").val();
    
    /*var idReqCat = $("#create-item").find("select[name='idReqCat_create']").val();
    var newCat = $("#create-item").find("input[name='newCat']").val();
    var requirementNr = $("#create-item").find("input[name='requirementNr']").val();
    var idReqType = $("#create-item").find("select[name='idReqType_create']").val();
    var idReqVerif = $("#create-item").find("select[name='idReqVerif_create']").val();*/
    var reqId = $("#create-item").find("input[name='reqId']").val();
    
    var shortDesc = $("#create-item").find("input[name='shortDesc']").val();
    var desc = $("#create-item").find("textarea[name='desc']").val();
    var notes = $("#create-item").find("input[name='notes']").val();
    /*var closeOut = $("#create-item").find("input[name='closeOut']").val();
    var test = $("#create-item").find("input[name='test']").val();
    var codeTrace = $("#create-item").find("input[name='codeTrace']").val();*/
    
    var idTLReqId = $("#create-item").find("select[name='idTLReqId_create']").val();
    var idTLReqId2 = $("#create-item").find("select[name='idTLReqId2_create']").val();
    var idTLReqId3 = $("#create-item").find("select[name='idTLReqId3_create']").val();
    var newTLReqId = $("#create-item").find("input[name='newTLReqId']").val();
    var newTLReqDesc = $("#create-item").find("textarea[name='newTLReqDesc']").val();
    
    console.log("idProject "+idProject);
    
    /*console.log("idReqCat "+idReqCat);
    console.log("newCat "+newCat);
    console.log("requirementNr "+requirementNr);
    console.log("idReqType "+idReqType);
    console.log("idReqVerif "+idReqVerif);*/
    console.log("Requirement "+reqId);
    
    console.log("shortDesc "+shortDesc);
    console.log("desc "+desc);
    console.log("notes "+notes);
    /*console.log("closeOut "+closeOut);
    console.log("test "+test);
    console.log("codeTrace "+codeTrace);*/
    
    console.log("idTLReqId "+idTLReqId);
    console.log("idTLReqId2 "+idTLReqId2);
    console.log("idTLReqId3 "+idTLReqId3);
    console.log("newTLReqId "+newTLReqId);
    console.log("newTLReqDesc "+newTLReqDesc);

    if(idProject != '' && reqId != '' && desc != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{idProject:idProject, reqId:reqId, shortDesc:shortDesc, desc:desc, notes:notes, 
                    idTLReqId:idTLReqId, idTLReqId2:idTLReqId2, idTLReqId3:idTLReqId3, newTLReqId:newTLReqId, 
                    newTLReqDesc:newTLReqDesc}
        }).done(function(data){
            $("#create-item").find("input[name='idProject']").val('');
            $("#create-item").find("input[name='reqId']").val('');
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Item Created Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing something.')
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
		url: url + 'api/delete_view-project-requirement-external-requ.php',
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

/* Change Status */
$("body").on("click",".change-status",function(){

    var id = $(this).parent("td").data('id');
    var requirement_change = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();

    getDropdownDataTopLevReqChange(id);

    $("#change-status").find("input[name='requirement_change']").val(requirement_change);
    $("#change-status").find(".change-id").val(id);

});

/* Change Status */
$(".crud-submit-change").click(function(e){

    e.preventDefault();
    var form_action = $("#change-status").find("form").attr("action");
    var idTLReqId_change = $("#change-status").find("select[name='idTLReqId_change']").val();
    var idTLReqId2_change = $("#change-status").find("select[name='idTLReqId2_change']").val();
    var idTLReqId3_change = $("#change-status").find("select[name='idTLReqId3_change']").val();
    var id = $("#change-status").find(".change-id").val();

    if(id != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, idTLReqId_change:idTLReqId_change, idTLReqId2_change:idTLReqId2_change, 
            idTLReqId3_change:idTLReqId3_change}
        }).done(function(data){
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Status Changed Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing the description.')
    }

});

/* Edit Item */
$("body").on("click",".edit-item",function(){

    var id = $(this).parent("td").data('id');
    var requirement = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var requirementTL = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var shortDesc = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var desc = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var notes = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var justification = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var applicability = $(this).parent("td").prev("td").prev("td").text();
    var applicableToPL = $(this).parent("td").prev("td").text();

    getDropdownDataApplicability(applicability);
    getDropdownDataApplicableToPL(applicableToPL);
    
    console.log(requirementTL);
    requirementTL = requirementTL.replace(/ /gi, ", ");
    console.log(requirementTL);

    $("#edit-item").find("input[name='requirement']").val(requirement);
    $("#edit-item").find("input[name='requirementTL']").val(requirementTL);
    $("#edit-item").find("input[name='shortDesc']").val(shortDesc);
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
    var shortDesc = $("#edit-item").find("input[name='shortDesc']").val();
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
            data:{id:id, shortDesc:shortDesc, desc:desc, notes:notes, justification:justification, 
            applicability:applicability, applicableToPL:applicableToPL}
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
function getDropdownDataDiscriminantDescriptionCreate(disc, idProject) {
    console.log('A>> '+url+'api/getData_dd-requirement-category.php?idProject='+idProject+'&id='+disc);
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-requirement-category.php?idProject='+idProject+'&id='+disc, // TODO: id=disc does not work
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionDiscriminantDescriptionCreate(data.data, disc, idProject);
	});
}

/* Insert value to textarea */
function manageOptionDiscriminantDescriptionCreate(data, disc, idProject) {
    console.log('B>> '+disc);
    var y = document.getElementById("reqNr_create");
    if (disc != "") {
        // get number of requirements of this category
    
    $.ajax({
		dataType: 'json',
		url: url+'api/getData_view-project-requirement-external-requ.php?idProject='+idProject+'&reqCat='+disc,
		data: {dropdown:dropdown}
	}).done(function(data){
		//manageOptionDiscriminantDescriptionCreate(data.data, disc);
        y.value = Object.keys(data.data).length + 1;
        
    /*var size = 0, key;
    for (key in data.data) {
        if (data.data.hasOwnProperty(key)) size++;
    }
    y.value = size + 1;*/

	});
    
    
    
    /*
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
    */
    

        //y.value="x";
    } else {
        y.value="";
    }
    
    
}

function updateDivReqCatCreate() {
    var x = document.getElementById("sel_reqcat_create");
    var y = document.getElementById("project");

    console.log(x.value);

    getDropdownDataDiscriminantDescriptionCreate(""+x.value, ""+y.value);
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
    
    console.log(x.value);

    getDropdownDataDiscriminantDescription(""+x.value);
}

function updateDivReqNewCatCreate() {
    var x = document.getElementById("newCat_create");
    console.log(x.value);
    
    // check, if category already exists
    
    // insert new category in database
    
    if (x.value.length >= 3) {
        var y = document.getElementById("reqNr_create");
        y.value=1;
    }
}