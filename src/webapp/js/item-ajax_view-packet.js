$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;
var dropdown = "";

var userrole = document.getElementById("user_role");

var idProject = getUrlVars()["idProject"];
var idStandard = getUrlVars()["idStandard"];

    getDropdownDataProcessCreate();
    getDropdownDataTypeCreate();
    getDropdownDataKindCreate();

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
		url: url+'api/getData_view-packet.php?idStandard='+idStandard,
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
		url: url+'api/getData_view-packet.php?idStandard='+idStandard+'&showAll=1',
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
		url: url+'api/getData_view-packet.php?idStandard='+idStandard,
		data: {page:page}
	}).done(function(data){
		manageRow(data.data);
	});
}


/* Get Dropdown Data for Process/APID */
function getDropdownDataProcessCreate() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-apid.php?idProject='+idProject,
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionProcessCreate(data.data);
	});
}

/* Add new option to select */
function manageOptionProcessCreate(data) {
	$("#sel_process_create").empty();
	$("#sel_process_create").append('<option value="" selected>--- Please select ---</option>');
	$.each( data, function( key, value ) {
		$("#sel_process_create").append('<option value="'+value.id+'">'+value.address+' / '+value.name+' ('+value.id+')</option>');
	});
}

/* Get Dropdown Data for Service Types */
function getDropdownDataTypeCreate() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-service.php?idStandard='+idStandard,
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionTypeCreate(data.data);
	});
}

/* Get Dropdown Data for Kind */
function getDropdownDataKindCreate() {
    var data = { 
        "data": [ 
            { "kind":"0", "name":"TC", "desc":"Telecommand" }, 
            { "kind":"1", "name":"TM", "desc":"Telemetry" }
        ]
    };
    manageOptionKindCreate(data.data);
}

/* Add new option to select */
function manageOptionTypeCreate(data, type) {
	$("#sel_type_create").empty();
	$("#sel_type_create").append('<option value="" selected>--- Please select ---</option>');
	$.each( data, function( key, value ) {
		$("#sel_type_create").append('<option value="'+value.type+'">'+value.type+' ('+value.name+')</option>');
	});
}

/* Add new option to select */
function manageOptionKindCreate(data) {
	$("#sel_kind_create").empty();
	$("#sel_kind_create").append('<option value="" selected>--- Please select ---</option>');
	$.each( data, function( key, value ) {
		$("#sel_kind_create").append('<option value="'+value.kind+'">'+value.name+'</option>');
	});
}


/* Get Dropdown Data for Process/APID */
function getDropdownDataProcess(process) {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-apid.php?idProject='+idProject,
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionProcess(data.data, process);
	});
}

/* Add new option to select */
function manageOptionProcess(data, process) {
	$("#sel_process").empty();
	$.each( data, function( key, value ) {
		if (process==value.id) {
			$("#sel_process").append('<option value="'+value.id+'" selected>'+value.address+' / '+value.name+' ('+value.id+')</option>');
		} else {
			$("#sel_process").append('<option value="'+value.id+'">'+value.address+' / '+value.name+' ('+value.id+')</option>');
		}
	});
}

/* Get Dropdown Data for Service Types */
function getDropdownDataType(type) {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-service.php?idStandard='+idStandard,
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionType(data.data, type);
	});
}

/* Get Dropdown Data for Kind */
function getDropdownDataKind(kind) {
    var data = { 
        "data": [ 
            { "kind":"0", "name":"TC", "desc":"Telecommand" }, 
            { "kind":"1", "name":"TM", "desc":"Telemetry" }
        ]
    };
    manageOptionKind(data.data, kind);
}

/* Add new option to select */
function manageOptionType(data, type) {
	$("#sel_type").empty();
	var isDerivedPacket = false;
	$.each( data, function( key, value ) {
		if (type==value.type) {
			$("#sel_type").append('<option value="'+value.type+'" selected>'+value.type+' ('+value.name+')</option>');
		} else {
			if (type && !isDerivedPacket) {
				$("#sel_type").append('<option value="null" selected>derived packet</option>');
				isDerivedPacket = true;
			} else {
				$("#sel_type").append('<option value="'+value.type+'">'+value.type+' ('+value.name+')</option>');
			}
		}

			
	});
	
}

/* Add new option to select */
function manageOptionKind(data, kind) {
	$("#sel_kind").empty();
	$.each( data, function( key, value ) {
		if (kind==value.kind) {
			$("#sel_kind").append('<option value="'+value.kind+'" selected>'+value.name+'</option>');
		} else {
			$("#sel_kind").append('<option value="'+value.kind+'">'+value.name+'</option>');
		}
	});
}

/* Add new Item table row */
function manageRow(data) {
	var	rows = '';
	$.each( data, function( key, value ) {
	  	rows = rows + '<tr>';
	  	rows = rows + '<td>'+value.id+'</td>';
	  	rows = rows + '<td class="hide">'+value.idProcess+'</td>';
	  	rows = rows + '<td>'+value.kind+'</td>';
	  	rows = rows + '<td>'+value.type+'</td>';
	  	rows = rows + '<td>'+value.subtype+'</td>';
	  	rows = rows + '<td>'+value.discriminant+'</td>';
	  	rows = rows + '<td>'+value.domain+'</td>';
	  	rows = rows + '<td>'+value.name+'</td>';
	  	rows = rows + '<td>'+value.shortDesc+'</td>';
	  	rows = rows + '<td class="td-hover">'+value.desc+'</td>';      /* Detail */
	  	rows = rows + '<td class="td-hover hide">'+value.descParam+'</td>'; /* Detail */
	  	rows = rows + '<td class="td-hover hide">'+value.descDest+'</td>';  /* Detail */
	  	rows = rows + '<td class="hide">'+value.code+'</td>';      /* Detail */
		rows = rows + '<td class="hide">'+value.setting+'</td>';
	  	rows = rows + '<td class="td-fix" data-id="'+value.id+'">';
        if (userrole.value < 4) {
        rows = rows + '<button data-toggle="modal" data-target="#edit-item" class="btn btn-primary edit-item">Edit</button> ';
        rows = rows + '<button data-toggle="modal" data-target="#edit-detail" class="btn btn-primary edit-detail">Detail</button> ';
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
    var idStandard = $("#create-item").find("input[name='idStandard']").val();
    var idProcess = $("#create-item").find("select[name='idProcess']").val();
    var kind = $("#create-item").find("select[name='kind']").val();
    var type = $("#create-item").find("select[name='type']").val();
    var subtype = $("#create-item").find("input[name='subtype']").val();
    var domain = $("#create-item").find("input[name='domain']").val();
    var name = $("#create-item").find("input[name='name']").val();
    var shortDesc = $("#create-item").find("input[name='shortDesc']").val();
    var desc = $("#create-item").find("textarea[name='desc']").val();
    var descParam = $("#create-item").find("input[name='descParam']").val();
    var descDest = $("#create-item").find("input[name='descDest']").val();
    var code = $("#create-item").find("input[name='code']").val();
    var setting = $("#create-item").find("textarea[name='setting']").val();

    if(id != '' && idStandard != '' && idProcess != '' && kind != '' && 
       type != '' && subtype != '' && domain != '' && name != '' && 
       shortDesc != '' && desc != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, idStandard:idStandard, idProcess:idProcess, kind:kind,
            type:type, subtype:subtype, domain:domain, name:name, 
            shortDesc:shortDesc, desc:desc, descParam:descParam, descDest:descDest, code:code,
            setting:setting},
            success: function(results, textStatus) {
                toastr.success('Database Operation Successfully. ' + results, 'Success Alert', {timeOut: 5000});
            },
            error: function(xhr, status, error)
            {
                toastr.error('Database Operation Failed. ' + xhr.responseText, 'Failure Alert', {timeOut: 5000});
            }
        }).done(function(data){
            $("#create-item").find("input[name='id']").val('');
            $("#create-item").find("input[name='idStandard']").val('');
            $("#create-item").find("select[name='idProcess']").val('');
            $("#create-item").find("select[name='kind']").val('');
            $("#create-item").find("select[name='type']").val('');
            $("#create-item").find("input[name='subtype']").val('');
            $("#create-item").find("input[name='domain']").val('');
            $("#create-item").find("input[name='name']").val('');
            $("#create-item").find("input[name='shortDesc']").val('');
            $("#create-item").find("textarea[name='desc']").val('');
            $("#create-item").find("input[name='descParam']").val('');
            $("#create-item").find("input[name='descDest']").val('');
            $("#create-item").find("input[name='code']").val('');
            $("#create-item").find("textarea[name='setting']").val('');
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
    var id = $(this).parent("td").data('id');
    var c_obj = $(this).parents("tr");

    var confirmation = confirm("Are you sure you want to remove this item?");
    if (confirmation) {
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + 'api/delete_view-packet.php',
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
    var idProcess = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var kind = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var type = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var subtype = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var discriminant = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var domain = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var name = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var shortDesc = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();

    getDropdownDataProcess(idProcess)
    getDropdownDataKind(kind);
    getDropdownDataType(type);

    $("#edit-item").find("input[name='idStandard']").val(idStandard);
    $("#edit-item").find("select[name='idProcess']").val(idProcess);
    $("#edit-item").find("select[name='kind']").val(kind);
    $("#edit-item").find("select[name='type']").val(type);
    $("#edit-item").find("input[name='subtype']").val(subtype);
    $("#edit-item").find("input[name='discriminant']").val(discriminant);
    $("#edit-item").find("input[name='domain']").val(domain);
    $("#edit-item").find("input[name='name']").val(name);
    $("#edit-item").find("input[name='shortDesc']").val(shortDesc);
    $("#edit-item").find(".edit-id").val(id);

});

/* Updated new Item */
$(".crud-submit-edit").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-item").find("form").attr("action");
    var idStandard = $("#edit-item").find("input[name='idStandard']").val();
    var idProcess = $("#edit-item").find("select[name='idProcess']").val();
    var kind = $("#edit-item").find("select[name='kind']").val();
    var type = $("#edit-item").find("select[name='type']").val();
    var subtype = $("#edit-item").find("input[name='subtype']").val();
    var domain = $("#edit-item").find("input[name='domain']").val();
    var discriminant = $("#edit-item").find("input[name='discriminant']").val();
    var name = $("#edit-item").find("input[name='name']").val();
    var shortDesc = $("#edit-item").find("input[name='shortDesc']").val();
    var id = $("#edit-item").find(".edit-id").val();

    if(id != '' && idStandard != '' && idProcess != '' && kind != '' && 
       type != '' && subtype != '' && domain != '' && name != '' && 
       shortDesc != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, idStandard:idStandard, idProcess:idProcess, kind:kind,
            type:type, subtype:subtype, domain:domain, name:name, 
            shortDesc:shortDesc},
            success: function(results, textStatus) {
                toastr.success('Database Operation Successfully. ' + results, 'Success Alert', {timeOut: 5000});
            },
            error: function(xhr, status, error)
            {
                toastr.error('Database Operation Failed. ' + xhr.responseText, 'Failure Alert', {timeOut: 5000});
            }
        }).done(function(data){
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Item Updated Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing something.')
    }

});

/* Edit Details of Item */
$("body").on("click",".edit-detail",function(){

    var id = $(this).parent("td").data('id');
    var type = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var subtype = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var discriminant = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var domain = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var name = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var shortDesc = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var desc = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var descParam = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var descDest = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var code = $(this).parent("td").prev("td").prev("td").text();
    var setting = $(this).parent("td").prev("td").text();

    $("#edit-detail").find("input[name='type']").val(type);
    $("#edit-detail").find("input[name='subtype']").val(subtype);
    $("#edit-detail").find("input[name='discriminant']").val(discriminant);
    $("#edit-detail").find("input[name='domain']").val(domain);
    $("#edit-detail").find("input[name='name']").val(name);
    $("#edit-detail").find("input[name='shortDesc']").val(shortDesc);
    $("#edit-detail").find("textarea[name='desc']").val(desc);
    $("#edit-detail").find("input[name='descParam']").val(descParam);
    $("#edit-detail").find("input[name='descDest']").val(descDest);
    $("#edit-detail").find("input[name='code']").val(code);
    $("#edit-detail").find("textarea[name='setting']").val(setting);
    $("#edit-detail").find(".edit-id").val(id);

});

/* Updated Details of Item */
$(".crud-submit-detail").click(function(e){
	
    e.preventDefault();
    var form_action = $("#edit-detail").find("form").attr("action");
    var desc = $("#edit-detail").find("textarea[name='desc']").val();
    var descParam = $("#edit-detail").find("input[name='descParam']").val();
    var descDest = $("#edit-detail").find("input[name='descDest']").val();
    var code = $("#edit-detail").find("input[name='code']").val();
    var setting = $("#edit-detail").find("textarea[name='setting']").val();
    var id = $("#edit-detail").find(".edit-id").val();

    if(id != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, desc:desc, descParam:descParam, descDest:descDest, code:code,
            setting:setting}
        }).done(function(data){
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Details of Item Updated Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing something.')
    }

});

});