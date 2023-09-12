$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;
var dropdown = "";
var database = "";

var userrole = document.getElementById("user_role");

var rel = getUrlVars()["rel"];
var idProject = getUrlVars()["idProject"];
var idApplication = getUrlVars()["idApplication"];
var idStandard = getUrlVars()["idStandard"];

    getDropdownDataPacketCreate();
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
		url: url+'api/getData_view-application-packet.php?idApplication='+idApplication+'&idStandard='+idStandard,
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
		url: url+'api/getData_view-application-packet.php?idApplication='+idApplication+'&idStandard='+idStandard+'&showAll=1',
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
		url: url+'api/getData_view-application-packet.php?idApplication='+idApplication+'&idStandard='+idStandard,
		data: {page:page}
	}).done(function(data){
		manageRow(data.data);
	});
}

/* Get Dropdown Data for Packet */
function getDropdownDataPacketCreate() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-application-packet.php?idProject='+idProject+'&idApplication='+idApplication+'&idStandard='+idStandard,
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionPacketCreate(data.data);
	});
}

/* Add new option to select */
function manageOptionPacketCreate(data) {
	$("#sel_packet_create").empty();
	$("#sel_packet_create").append('<option value="" selected>--- Please select ---</option>');
	$.each( data, function( key, value ) {
        if (value.kind==0) {
		    $("#sel_packet_create").append('<option value="'+value.id+'">TC('+value.type+'/'+value.subtype+') '+value.name+' ('+value.id+')</option>');
        } else if (value.kind==1) {
            $("#sel_packet_create").append('<option value="'+value.id+'">TM('+value.type+'/'+value.subtype+') '+value.name+' ('+value.id+')</option>');
        } else {
            $("#sel_packet_create").append('<option value="'+value.id+'">'+value.kind+'('+value.type+'/'+value.subtype+') '+value.name+' ('+value.id+')</option>');
        }
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

/* Get TM Data for Packet */
function getTmData(idPacket) {
	var ret = $.ajax({
		dataType: 'json',
		url: url+'api/getData_db-application-packet.php?kind=1&idPacket='+idPacket+'&idApplication='+idApplication+'&idStandard='+idStandard,
		data: {database:database},
        async: false
	}).done(function(data){
        /*console.log("data: "+JSON.stringify(data));
        console.log("data.data: "+JSON.stringify(data.data));
        var objectX = JSON.parse(JSON.stringify(data.data));
        console.log(JSON.stringify(objectX[0]) + "," + objectX[0]['repPrvCheckRepeat']);*/
	}).responseText;
    //console.log("ret: "+JSON.stringify(JSON.parse(ret).data[0]));
    return JSON.stringify(JSON.parse(ret).data[0]);
}

/* Get TC Data for Packet */
function getTcData(idPacket) {
	var ret = $.ajax({
		dataType: 'json',
		url: url+'api/getData_db-application-packet.php?kind=0&idPacket='+idPacket+'&idApplication='+idApplication+'&idStandard='+idStandard,
		data: {database:database},
        async: false
	}).done(function(data){

	}).responseText;
    return JSON.stringify(JSON.parse(ret).data[0]);
}


/* Add new Item table row */
function manageRow(data) {
	var	rows = '';
	$.each( data, function( key, value ) {
	  	rows = rows + '<tr>';
	  	rows = rows + '<td>'+value.id+'</td>';
	  	rows = rows + '<td class="hide">'+value.idProcess+'</td>';
        if (value.kind==0) {
            rows = rows + '<td>TC</td>';
        } else {
            rows = rows + '<td>TM</td>';
        }
	  	rows = rows + '<td>'+value.type+'</td>';
	  	rows = rows + '<td>'+value.subtype+'</td>';
	  	rows = rows + '<td>'+value.domain+'</td>';
	  	rows = rows + '<td>'+value.name+'</td>';
	  	rows = rows + '<td>'+value.shortDesc+'</td>';
	  	rows = rows + '<td class="td-hover hide">'+value.desc+'</td>';      /* Detail */
	  	rows = rows + '<td class="td-hover hide">'+value.descParam+'</td>'; /* Detail */
	  	rows = rows + '<td class="td-hover hide">'+value.descDest+'</td>';  /* Detail */
	  	rows = rows + '<td class="td-fix" data-id="'+value.id+'">';
        if (userrole.value < 4) {
        //rows = rows + '<button data-toggle="modal" data-target="#edit-item" class="btn btn-primary edit-item">Edit</button> ';
        if (rel == 1) {  // Service Provider
        if (value.kind == 1) {  // TM
        rows = rows + '<button data-toggle="modal" data-target="#edit-detail-tm-prv" class="btn btn-primary edit-detail-tm-prv">Detail</button> ';
        } else {  // TC
        rows = rows + '<button data-toggle="modal" data-target="#edit-detail-tc-prv" class="btn btn-primary edit-detail-tc-prv">Detail</button> ';
        }
        } else {  // Service User
        if (value.kind == 1) {  // TM
        rows = rows + '<button data-toggle="modal" data-target="#edit-detail-tm-usr" class="btn btn-primary edit-detail-tm-usr">Detail</button> ';
        } else {  // TC
        rows = rows + '<button data-toggle="modal" data-target="#edit-detail-tc-usr" class="btn btn-primary edit-detail-tc-usr">Detail</button> ';
        }
        }
        if (userrole.value < 3) {
        rows = rows + '<button class="btn btn-danger remove-item">Remove Item</button>';
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
    var id = $("#create-item").find("select[name='idPacket']").val();
    /*
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
    var setting = $("#create-item").find("textarea[name='setting']").val();*/

    if(id != '' && idApplication != '' && idStandard != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, idApplication:idApplication, idStandard:idStandard},
            success: function(results, textStatus) {
                toastr.success('Database Operation Successfully. ' + results, 'Success Alert', {timeOut: 5000});
            },
            error: function(xhr, status, error)
            {
                toastr.error('Database Operation Failed. ' + xhr.responseText, 'Failure Alert', {timeOut: 5000});
            }
        }).done(function(data){
            $("#create-item").find("input[name='id']").val('');
            /*$("#create-item").find("input[name='idStandard']").val('');
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
            $("#create-item").find("textarea[name='setting']").val('');*/
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
            url: url + 'api/delete_view-application-packet.php?idApplication='+idApplication+'&idStandard='+idStandard,
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
    
    /* NOT USED */

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

    /* NOT USED */

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
$("body").on("click",".edit-detail-tm-prv",function(){

    var id = $(this).parent("td").data('id');
    var type = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var subtype = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var domain = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var name = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var shortDesc = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var desc = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var descParam = $(this).parent("td").prev("td").prev("td").text();
    var descDest = $(this).parent("td").prev("td").text();
    
    console.log("id: "+id);
    var tmData = JSON.parse(getTmData(id));
    console.log(tmData);

    console.log("repPrvCheckEnable: "+tmData['repPrvCheckEnable']);
    console.log("repPrvCheckReady: "+tmData['repPrvCheckReady']);
    console.log("repPrvCheckRepeat: "+tmData['repPrvCheckRepeat']);
    console.log("repPrvActionUpdate: "+tmData['repPrvActionUpdate']);

    /*console.log("repUsrCheckAcceptance: "+tmData['repUsrCheckAcceptance']);
    console.log("repUsrActionUpdate: "+tmData['repUsrActionUpdate']);*/

    $("#edit-detail-tm-prv").find("input[name='type']").val(type);
    $("#edit-detail-tm-prv").find("input[name='subtype']").val(subtype);
    $("#edit-detail-tm-prv").find("input[name='domain']").val(domain);
    $("#edit-detail-tm-prv").find("input[name='name']").val(name);
    $("#edit-detail-tm-prv").find("input[name='shortDesc']").val(shortDesc);
    $("#edit-detail-tm-prv").find("textarea[name='desc']").val(desc);
    $("#edit-detail-tm-prv").find("input[name='descParam']").val(descParam);
    $("#edit-detail-tm-prv").find("input[name='descDest']").val(descDest);
    
    $("#edit-detail-tm-prv").find("textarea[name='enablecheck']").val(tmData['repPrvCheckEnable']);
    $("#edit-detail-tm-prv").find("textarea[name='readycheck']").val(tmData['repPrvCheckReady']);
    $("#edit-detail-tm-prv").find("textarea[name='repeatcheck']").val(tmData['repPrvCheckRepeat']);
    $("#edit-detail-tm-prv").find("textarea[name='updateaction']").val(tmData['repPrvActionUpdate']);
    $("#edit-detail-tm-prv").find(".edit-id").val(id);

});

/* Edit Details of Item */
$("body").on("click",".edit-detail-tc-prv",function(){

    var id = $(this).parent("td").data('id');
    var type = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var subtype = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var domain = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var name = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var shortDesc = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var desc = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var descParam = $(this).parent("td").prev("td").prev("td").text();
    var descDest = $(this).parent("td").prev("td").text();

    console.log("id: "+id);
    var tcData = JSON.parse(getTcData(id));
    console.log(tcData);

    console.log("cmdPrvActionAbort: "+tcData['cmdPrvActionAbort']);
    console.log("cmdPrvActionProgress: "+tcData['cmdPrvActionProgress']);
    console.log("cmdPrvActionStart: "+tcData['cmdPrvActionStart']);
    console.log("cmdPrvActionTermination: "+tcData['cmdPrvActionTermination']);
    console.log("cmdPrvCheckAcceptance: "+tcData['cmdPrvCheckAcceptance']);
    console.log("cmdPrvCheckReady: "+tcData['cmdPrvCheckReady']);

    /*console.log("cmdUsrActionUpdate: "+tcData['cmdUsrActionUpdate']);
    console.log("cmdUsrCheckEnable: "+tcData['cmdUsrCheckEnable']);
    console.log("cmdUsrCheckReady: "+tcData['cmdUsrCheckReady']);
    console.log("cmdUsrCheckRepeat: "+tcData['cmdUsrCheckRepeat']);*/

    $("#edit-detail-tc-prv").find("input[name='type']").val(type);
    $("#edit-detail-tc-prv").find("input[name='subtype']").val(subtype);
    $("#edit-detail-tc-prv").find("input[name='domain']").val(domain);
    $("#edit-detail-tc-prv").find("input[name='name']").val(name);
    $("#edit-detail-tc-prv").find("input[name='shortDesc']").val(shortDesc);
    $("#edit-detail-tc-prv").find("textarea[name='desc']").val(desc);
    $("#edit-detail-tc-prv").find("input[name='descParam']").val(descParam);
    $("#edit-detail-tc-prv").find("input[name='descDest']").val(descDest);
    
    $("#edit-detail-tc-prv").find("textarea[name='acceptancecheck']").val(tcData['cmdPrvCheckAcceptance']);
    $("#edit-detail-tc-prv").find("textarea[name='readycheck']").val(tcData['cmdPrvCheckReady']);
    $("#edit-detail-tc-prv").find("textarea[name='startaction']").val(tcData['cmdPrvActionStart']);
    $("#edit-detail-tc-prv").find("textarea[name='progressaction']").val(tcData['cmdPrvActionProgress']);
    $("#edit-detail-tc-prv").find("textarea[name='terminationaction']").val(tcData['cmdPrvActionTermination']);
    $("#edit-detail-tc-prv").find("textarea[name='abortaction']").val(tcData['cmdPrvActionAbort']);
    $("#edit-detail-tc-prv").find(".edit-id").val(id);

});

/* Edit Details of Item */
$("body").on("click",".edit-detail-tm-usr",function(){

    var id = $(this).parent("td").data('id');
    var type = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var subtype = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var domain = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var name = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var shortDesc = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var desc = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var descParam = $(this).parent("td").prev("td").prev("td").text();
    var descDest = $(this).parent("td").prev("td").text();
    
    console.log("id: "+id);
    var tmData = JSON.parse(getTmData(id));
    console.log(tmData);

    /*console.log("repPrvCheckEnable: "+tmData['repPrvCheckEnable']);
    console.log("repPrvCheckReady: "+tmData['repPrvCheckReady']);
    console.log("repPrvCheckRepeat: "+tmData['repPrvCheckRepeat']);
    console.log("repPrvActionUpdate: "+tmData['repPrvActionUpdate']);

    tmData['repPrvCheckEnable'] += " 1";
    tmData['repPrvCheckReady'] += " 2";
    tmData['repPrvCheckRepeat'] += " 3";
    tmData['repPrvActionUpdate'] += " 4";*/

    console.log("repUsrCheckAcceptance: "+tmData['repUsrCheckAcceptance']);
    console.log("repUsrActionUpdate: "+tmData['repUsrActionUpdate']);

    $("#edit-detail-tm-usr").find("input[name='type']").val(type);
    $("#edit-detail-tm-usr").find("input[name='subtype']").val(subtype);
    $("#edit-detail-tm-usr").find("input[name='domain']").val(domain);
    $("#edit-detail-tm-usr").find("input[name='name']").val(name);
    $("#edit-detail-tm-usr").find("input[name='shortDesc']").val(shortDesc);
    $("#edit-detail-tm-usr").find("textarea[name='desc']").val(desc);
    $("#edit-detail-tm-usr").find("input[name='descParam']").val(descParam);
    $("#edit-detail-tm-usr").find("input[name='descDest']").val(descDest);
    
    $("#edit-detail-tm-usr").find("textarea[name='acceptancecheck']").val(tmData['repUsrCheckAcceptance']);
    $("#edit-detail-tm-usr").find("textarea[name='updateaction']").val(tmData['repUsrActionUpdate']);
    $("#edit-detail-tm-usr").find(".edit-id").val(id);

});

/* Edit Details of Item */
$("body").on("click",".edit-detail-tc-usr",function(){

    var id = $(this).parent("td").data('id');
    var type = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var subtype = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var domain = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var name = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var shortDesc = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var desc = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var descParam = $(this).parent("td").prev("td").prev("td").text();
    var descDest = $(this).parent("td").prev("td").text();

    console.log("id: "+id);
    var tcData = JSON.parse(getTcData(id));
    console.log(tcData);

    /*console.log("cmdPrvActionAbort: "+tcData['cmdPrvActionAbort']);
    console.log("cmdPrvActionProgress: "+tcData['cmdPrvActionProgress']);
    console.log("cmdPrvActionStart: "+tcData['cmdPrvActionStart']);
    console.log("cmdPrvActionTermination: "+tcData['cmdPrvActionTermination']);
    console.log("cmdPrvCheckAcceptance: "+tcData['cmdPrvCheckAcceptance']);
    console.log("cmdPrvCheckReady: "+tcData['cmdPrvCheckReady']);

    tcData['cmdPrvActionAbort'] += " 1";
    tcData['cmdPrvActionProgress'] += " 2";
    tcData['cmdPrvActionStart'] += " 3";
    tcData['cmdPrvActionTermination'] += " 4";
    tcData['cmdPrvCheckAcceptance'] += " 5";
    tcData['cmdPrvCheckReady'] += " 6";*/

    console.log("cmdUsrActionUpdate: "+tcData['cmdUsrActionUpdate']);
    console.log("cmdUsrCheckEnable: "+tcData['cmdUsrCheckEnable']);
    console.log("cmdUsrCheckReady: "+tcData['cmdUsrCheckReady']);
    console.log("cmdUsrCheckRepeat: "+tcData['cmdUsrCheckRepeat']);

    $("#edit-detail-tc-usr").find("input[name='type']").val(type);
    $("#edit-detail-tc-usr").find("input[name='subtype']").val(subtype);
    $("#edit-detail-tc-usr").find("input[name='domain']").val(domain);
    $("#edit-detail-tc-usr").find("input[name='name']").val(name);
    $("#edit-detail-tc-usr").find("input[name='shortDesc']").val(shortDesc);
    $("#edit-detail-tc-usr").find("textarea[name='desc']").val(desc);
    $("#edit-detail-tc-usr").find("input[name='descParam']").val(descParam);
    $("#edit-detail-tc-usr").find("input[name='descDest']").val(descDest);
    
    $("#edit-detail-tc-usr").find("textarea[name='enablecheck']").val(tcData['cmdUsrCheckEnable']);
    $("#edit-detail-tc-usr").find("textarea[name='readycheck']").val(tcData['cmdUsrCheckReady']);
    $("#edit-detail-tc-usr").find("textarea[name='repeatcheck']").val(tcData['cmdUsrCheckRepeat']);
    $("#edit-detail-tc-usr").find("textarea[name='updateaction']").val(tcData['cmdUsrActionUpdate']);
    $("#edit-detail-tc-usr").find(".edit-id").val(id);

});

/* Updated Details of Item */
$(".crud-submit-detail-tm-prv").click(function(e){
	
    e.preventDefault();
    var form_action = $("#edit-detail-tm-prv").find("form").attr("action");
    /*var desc = $("#edit-detail").find("textarea[name='desc']").val();
    var descParam = $("#edit-detail").find("input[name='descParam']").val();
    var descDest = $("#edit-detail").find("input[name='descDest']").val();
    var code = $("#edit-detail").find("input[name='code']").val();
    var setting = $("#edit-detail").find("textarea[name='setting']").val();*/
    
    var enablecheck = $("#edit-detail-tm-prv").find("textarea[name='enablecheck']").val();
    var readycheck = $("#edit-detail-tm-prv").find("textarea[name='readycheck']").val();
    var repeatcheck = $("#edit-detail-tm-prv").find("textarea[name='repeatcheck']").val();
    var updateaction = $("#edit-detail-tm-prv").find("textarea[name='updateaction']").val();
    var id = $("#edit-detail-tm-prv").find(".edit-id").val();

    if(id != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, idApplication:idApplication, idStandard:idStandard, enablecheck:enablecheck, 
                  readycheck:readycheck, repeatcheck:repeatcheck, updateaction:updateaction}
        }).done(function(data){
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Details of Item Updated Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing something.')
    }

});

/* Updated Details of Item */
$(".crud-submit-detail-tc-prv").click(function(e){
	
    e.preventDefault();
    var form_action = $("#edit-detail-tc-prv").find("form").attr("action");
    /*var desc = $("#edit-detail").find("textarea[name='desc']").val();
    var descParam = $("#edit-detail").find("input[name='descParam']").val();
    var descDest = $("#edit-detail").find("input[name='descDest']").val();
    var code = $("#edit-detail").find("input[name='code']").val();
    var setting = $("#edit-detail").find("textarea[name='setting']").val();*/
    
    var acceptancecheck = $("#edit-detail-tc-prv").find("textarea[name='acceptancecheck']").val();
    var readycheck = $("#edit-detail-tc-prv").find("textarea[name='readycheck']").val();
    var startaction = $("#edit-detail-tc-prv").find("textarea[name='startaction']").val();
    var progressaction = $("#edit-detail-tc-prv").find("textarea[name='progressaction']").val();
    var terminationaction = $("#edit-detail-tc-prv").find("textarea[name='terminationaction']").val();
    var abortaction = $("#edit-detail-tc-prv").find("textarea[name='abortaction']").val();
    var id = $("#edit-detail-tc-prv").find(".edit-id").val();

    if(id != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, idApplication:idApplication, idStandard:idStandard, acceptancecheck:acceptancecheck, 
                  readycheck:readycheck, startaction:startaction, progressaction:progressaction,
                  terminationaction:terminationaction, abortaction:abortaction}
        }).done(function(data){
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Details of Item Updated Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing something.')
    }

});

/* Updated Details of Item */
$(".crud-submit-detail-tm-usr").click(function(e){
	
    e.preventDefault();
    var form_action = $("#edit-detail-tm-usr").find("form").attr("action");
    /*var desc = $("#edit-detail").find("textarea[name='desc']").val();
    var descParam = $("#edit-detail").find("input[name='descParam']").val();
    var descDest = $("#edit-detail").find("input[name='descDest']").val();
    var code = $("#edit-detail").find("input[name='code']").val();
    var setting = $("#edit-detail").find("textarea[name='setting']").val();*/
    
    var acceptancecheck = $("#edit-detail-tm-usr").find("textarea[name='acceptancecheck']").val();
    var updateaction = $("#edit-detail-tm-usr").find("textarea[name='updateaction']").val();
    var id = $("#edit-detail-tm-usr").find(".edit-id").val();

    if(id != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, idApplication:idApplication, idStandard:idStandard, acceptancecheck:acceptancecheck, 
                  updateaction:updateaction}
        }).done(function(data){
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Details of Item Updated Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing something.')
    }

});

/* Updated Details of Item */
$(".crud-submit-detail-tc-usr").click(function(e){
	
    e.preventDefault();
    var form_action = $("#edit-detail-tc-usr").find("form").attr("action");
    /*var desc = $("#edit-detail").find("textarea[name='desc']").val();
    var descParam = $("#edit-detail").find("input[name='descParam']").val();
    var descDest = $("#edit-detail").find("input[name='descDest']").val();
    var code = $("#edit-detail").find("input[name='code']").val();
    var setting = $("#edit-detail").find("textarea[name='setting']").val();*/
    
    var enablecheck = $("#edit-detail-tc-usr").find("textarea[name='enablecheck']").val();
    var readycheck = $("#edit-detail-tc-usr").find("textarea[name='readycheck']").val();
    var repeatcheck = $("#edit-detail-tc-usr").find("textarea[name='repeatcheck']").val();
    var updateaction = $("#edit-detail-tc-usr").find("textarea[name='updateaction']").val();
    var id = $("#edit-detail-tc-usr").find(".edit-id").val();

    if(id != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, idApplication:idApplication, idStandard:idStandard, enablecheck:enablecheck, 
                  readycheck:readycheck, repeatcheck:repeatcheck, updateaction:updateaction}
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