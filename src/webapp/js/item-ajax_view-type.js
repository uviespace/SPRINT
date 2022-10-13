$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;

var userrole = document.getElementById("user_role");

var idProject = getUrlVars()["idProject"];
var idStandard = getUrlVars()["idStandard"];

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
		url: url+'api/getData_view-type.php?idStandard='+idStandard,
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
		url: url+'api/getData_view-type.php?idStandard='+idStandard+'&showAll=1',
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
		url: url+'api/getData_view-type.php?idStandard='+idStandard,
		data: {page:page}
	}).done(function(data){
		manageRow(data.data);
	});
}

/* Get Type Schema*/
function getTypeSchema(idType) {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_view-type-schema.php?idType='+idType,
		data: {page:page}
	}).done(function(data){
        //console.log(data);
        //console.log(data.data);
        //console.log(data.data[0]);
        //var jsonSchema = JSON.stringify(data.data[0].schema, null, 4);
        //console.log(jsonSchema);
        $("#edit-schema").find("textarea[name='schema']").val(data.data[0].schema);
	});
}

/* Add new Item table row */
function manageRow(data) {
	var	rows = '';
	$.each( data, function( key, value ) {
	  	rows = rows + '<tr>';
	  	rows = rows + '<td>'+value.id+'</td>';
	  	rows = rows + '<td>'+value.domain+'</td>';
	  	rows = rows + '<td>'+value.name+'</td>';
	  	rows = rows + '<td>'+value.nativeType+'</td>';
	  	rows = rows + '<td>'+value.size+'</td>';
	  	rows = rows + '<td>'+value.pusparamtype+'</td>';
	  	rows = rows + '<td>'+value.pusdatatype+'</td>';
	  	rows = rows + '<td>'+value.value+'</td>';
	  	rows = rows + '<td>'+value.desc+'</td>';
	  	rows = rows + '<td data-id="'+value.id+'">';
        if (userrole.value < 4) {
        rows = rows + '<button data-toggle="modal" data-target="#edit-item" class="btn btn-primary edit-item">Edit</button> ';
        rows = rows + '<button data-toggle="modal" data-target="#edit-setting" class="btn btn-primary edit-setting">Setting</button> ';
        rows = rows + '<button data-toggle="modal" data-target="#edit-schema" class="btn btn-primary edit-schema">Schema</button> ';
        if (userrole.value < 3) {
        rows = rows + '<button class="btn btn-danger remove-item">Delete</button>';
        }
        }
        rows = rows + '</td>';
	  	rows = rows + '</tr>';
	});

	$("tbody").html(rows);

	var v = 7; // column to be hidden
	$('#foo tr > *:nth-child('+v+')').hide();

}

/* Show all Items */
$(".crud-submit-show").click(function(e){
    manageDataAll();
});

/* Create new Item */
$(".crud-submit").click(function(e){
    //e.preventDefault();
    var form_action = $("#create-item").find("form").attr("action-data");
    //var id = $("#create-item").find("input[name='id']").val();
    var idStandrad = $("#create-item").find("input[name='idStandard']").val();
    var domain = $("#create-item").find("input[name='domain']").val();
    var name = $("#create-item").find("input[name='name']").val();
    var nativeType = $("#create-item").find("input[name='nativeType']").val();
    var size = $("#create-item").find("input[name='size']").val();
    var value = $("#create-item").find("input[name='value']").val();
    var desc = $("#create-item").find("textarea[name='desc']").val();

    if(domain != '' && name != '' && size != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{idStandard:idStandard, domain:domain, name:name, nativeType:nativeType,
            size:size, value:value, desc:desc}
        }).done(function(data){
            $("#create-item").find("input[name='id']").val('');
            $("#create-item").find("input[name='idStandard']").val('');
            $("#create-item").find("input[name='domain']").val('');
            $("#create-item").find("input[name='name']").val('');
            $("#create-item").find("input[name='nativeType']").val('');
            $("#create-item").find("input[name='size']").val('');
            $("#create-item").find("input[name='value']").val('');
            $("#create-item").find("textarea[name='desc']").val('');
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Item Created Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing title or description.')
    }

});

/* Remove Item */
$("body").on("click",".remove-item",function(){
	var id = $(this).parent("td").data('id');
	var c_obj = $(this).parents("tr");
	$.ajax({
		dataType: 'json',
		type:'POST',
		url: url + 'api/delete_view-type.php',
		data:{id:id}
	}).done(function(data){
		c_obj.remove();
		toastr.success('Item Deleted Successfully.', 'Success Alert', {timeOut: 5000});
		getPageData();
	});

});

/* Edit Item */
$("body").on("click",".edit-item",function(){

    var id = $(this).parent("td").data('id');
    var domain = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var name = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var nativeType = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var size = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var pusparamtype = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var pusdatatype = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var value = $(this).parent("td").prev("td").prev("td").text();
    var desc = $(this).parent("td").prev("td").text();
    var type = 3;

    getDropdownDataPUS(pusdatatype, size);

    $("#edit-item").find("input[name='domain']").val(domain);
    $("#edit-item").find("input[name='name']").val(name);
    $("#edit-item").find("input[name='nativeType']").val(nativeType);
    $("#edit-item").find("input[name='size']").val(size);
    $("#edit-item").find("input[name='pusparamtype']").val(pusparamtype);
    $("#edit-item").find("select[name='pusdatatype']").val(pusdatatype);
    $("#edit-item").find("input[name='value']").val(value);
    $("#edit-item").find("textarea[name='desc']").val(desc);
    $("#edit-item").find(".edit-id").val(id);

});

/* Edit Setting */
$("body").on("click",".edit-setting",function(){

    var idType = $(this).parent("td").data('id');

    window.location = url+'open_datatype_setting_editor.php?idProject='+idProject+'&idStandard='+idStandard+'&id='+idType; 

});

/* Edit Schema */
$("body").on("click",".edit-schema",function(){

    var id = $(this).parent("td").data('id');
    
    console.log(id);
    getTypeSchema(id);

    $("#edit-schema").find(".edit-id").val(id);

});

/* Updated new Item */
$(".crud-submit-edit").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-item").find("form").attr("action");
    var domain = $("#edit-item").find("input[name='domain']").val();
    var name = $("#edit-item").find("input[name='name']").val();
    var nativeType = $("#edit-item").find("input[name='nativeType']").val();
    var size = $("#edit-item").find("input[name='size']").val();
    var pusdatatype = $("#edit-item").find("select[name='pusdatatype']").val();
    var value = $("#edit-item").find("input[name='value']").val();
    var desc = $("#edit-item").find("textarea[name='desc']").val();
    var id = $("#edit-item").find(".edit-id").val();

    if(id != '' && domain != '' && name != '' && 
       size != '' && pusdatatype != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, domain:domain, name:name, nativeType:nativeType,
            size:size, pusdatatype:pusdatatype, value:value, desc:desc}
        }).done(function(data){
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Item Updated Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing something.')
    }

});

/* Updated Schema */
$(".crud-submit-edit-schema").click(function(e){
    
    e.preventDefault();
    var form_action = $("#edit-schema").find("form").attr("action");
    var schema = $("#edit-schema").find("textarea[name='schema']").val();
    var id = $("#edit-schema").find(".edit-id").val();
    
    //console.log(id);
    //console.log(schema);
    //console.log(JSON.stringify(schema, null, 4));
    //console.log(form_action);
    
    if(id != '' && schema != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, schema:schema}
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

/* Get Dropdown Data for PUS data type */
function getDropdownDataPUS(type, size) {
    var data = { 
        "data": [
            { "type":"-", "name":"", "size":"0", "desc":"------- Please choose -------" },
            { "type":"0", "name":"PTC 1 / PFC 0", "size":"1", "desc":"Unsigned Integer; 1 bit; boolean parameter" },
            { "type":"1", "name":"PTC 2 / 0 < PFC < 33", "size":"1:32", "desc":"Unsigned Integer; PFC bits; enumerated parameter" },
            { "type":"2", "name":"PTC 3 / PFC 0", "size":"4", "desc":"Unsigned Integer; 4 bits; unsigned integer parameter" },
            { "type":"3", "name":"PTC 3 / PFC 1", "size":"5", "desc":"Unsigned Integer; 5 bits; unsigned integer parameter" },
            { "type":"4", "name":"PTC 3 / PFC 2", "size":"6", "desc":"Unsigned Integer; 6 bits; unsigned integer parameter" },
            { "type":"5", "name":"PTC 3 / PFC 3", "size":"7", "desc":"Unsigned Integer; 7 bits; unsigned integer parameter" },
            { "type":"6", "name":"PTC 3 / PFC 4", "size":"8", "desc":"Unsigned Integer; 8 bits; unsigned integer parameter" },
            { "type":"7", "name":"PTC 3 / PFC 5", "size":"9", "desc":"Unsigned Integer; 9 bits; unsigned integer parameter" },
            { "type":"8", "name":"PTC 3 / PFC 6", "size":"10", "desc":"Unsigned Integer; 10 bits; unsigned integer parameter" },
            { "type":"9", "name":"PTC 3 / PFC 7", "size":"11", "desc":"Unsigned Integer; 11 bits; unsigned integer parameter" },
            { "type":"10", "name":"PTC 3 / PFC 8", "size":"12", "desc":"Unsigned Integer; 12 bits; unsigned integer parameter" },
            { "type":"11", "name":"PTC 3 / PFC 9", "size":"13", "desc":"Unsigned Integer; 13 bits; unsigned integer parameter" },
            { "type":"12", "name":"PTC 3 / PFC 10", "size":"14", "desc":"Unsigned Integer; 14 bits; unsigned integer parameter" },
            { "type":"13", "name":"PTC 3 / PFC 11", "size":"15", "desc":"Unsigned Integer; 15 bits; unsigned integer parameter" },
            { "type":"14", "name":"PTC 3 / PFC 12", "size":"16", "desc":"Unsigned Integer; 16 bits; unsigned integer parameter" },
            { "type":"15", "name":"PTC 3 / PFC 13", "size":"24", "desc":"Unsigned Integer; 24 bits; unsigned integer parameter" },
            { "type":"16", "name":"PTC 3 / PFC 14", "size":"32", "desc":"Unsigned Integer; 32 bits; unsigned integer parameter" },
            { "type":"17", "name":"PTC 3 / PFC 15", "size":"48", "desc":"Unsigned Integer; 48 bits; unsigned integer parameter (not supported by SCOS2000)" },
            { "type":"18", "name":"PTC 3 / PFC 16", "size":"64", "desc":"Unsigned Integer; 64 bits; unsigned integer parameter (not supported by SCOS2000)" },
            { "type":"19", "name":"PTC 4 / PFC 0", "size":"4", "desc":"Signed Integer; 4 bits; signed integer parameter" },
            { "type":"20", "name":"PTC 4 / PFC 1", "size":"5", "desc":"Signed Integer; 5 bits; signed integer parameter" },
            { "type":"21", "name":"PTC 4 / PFC 2", "size":"6", "desc":"Signed Integer; 6 bits; signed integer parameter" },
            { "type":"22", "name":"PTC 4 / PFC 3", "size":"7", "desc":"Signed Integer; 7 bits; signed integer parameter" },
            { "type":"23", "name":"PTC 4 / PFC 4", "size":"8", "desc":"Signed Integer; 8 bits; signed integer parameter" },
            { "type":"24", "name":"PTC 4 / PFC 5", "size":"9", "desc":"Signed Integer; 9 bits; signed integer parameter" },
            { "type":"25", "name":"PTC 4 / PFC 6", "size":"10", "desc":"Signed Integer; 10 bits; signed integer parameter" },
            { "type":"26", "name":"PTC 4 / PFC 7", "size":"11", "desc":"Signed Integer; 11 bits; signed integer parameter" },
            { "type":"27", "name":"PTC 4 / PFC 8", "size":"12", "desc":"Signed Integer; 12 bits; signed integer parameter" },
            { "type":"28", "name":"PTC 4 / PFC 9", "size":"13", "desc":"Signed Integer; 13 bits; signed integer parameter" },
            { "type":"29", "name":"PTC 4 / PFC 10", "size":"14", "desc":"Signed Integer; 14 bits; signed integer parameter" },
            { "type":"30", "name":"PTC 4 / PFC 11", "size":"15", "desc":"Signed Integer; 15 bits; signed integer parameter" },
            { "type":"31", "name":"PTC 4 / PFC 12", "size":"16", "desc":"Signed Integer; 16 bits; signed integer parameter" },
            { "type":"32", "name":"PTC 4 / PFC 13", "size":"24", "desc":"Signed Integer; 24 bits; signed integer parameter" },
            { "type":"33", "name":"PTC 4 / PFC 14", "size":"32", "desc":"Signed Integer; 32 bits; signed integer parameter" },
            { "type":"34", "name":"PTC 4 / PFC 15", "size":"48", "desc":"Signed Integer; 48 bits; signed integer parameter (not supported by SCOS2000)" },
            { "type":"35", "name":"PTC 4 / PFC 16", "size":"64", "desc":"Signed Integer; 64 bits; signed integer parameter (not supported by SCOS2000)" },
            { "type":"36", "name":"PTC 5 / PFC 1", "size":"32", "desc":"Simple precision real; 32 bits; simple precision real parameter" },
            { "type":"37", "name":"PTC 5 / PFC 2", "size":"64", "desc":"Double precision real; 64 bits; double precision real parameter" },
            { "type":"38", "name":"PTC 5 / PFC 3", "size":"32", "desc":"Simple precision real (MIL std); 32 bits; Referred to as PTC=5, PFC=2 in ESA missions Parameter Types definitions" },
            { "type":"39", "name":"PTC 6 / PFC 0", "size":"0", "desc":"Bit string; variable; variable-length bit-string (not supported by SCOS2000)" },
            { "type":"40", "name":"PTC 6 / 0 < PFC < 33", "size":"1:32", "desc":"Unsigned integer; PFC bits; PUS bit-string parameter" },
            { "type":"41", "name":"PTC 7 / PFC 0", "size":"0", "desc":"Octet string; variable; variable-length octet string" },
            { "type":"42", "name":"PTC 7 / PFC > 0", "size":"0", "desc":"Octet string; PFC octets; fixed-length octet string" },
            { "type":"43", "name":"PTC 8 / PFC 0", "size":"0", "desc":"ASCII string; variable; variable-length character string" },
            { "type":"44", "name":"PTC 8 / PFC > 0", "size":"0", "desc":"ASCII string; PFC octets; fixed-length character string" },
            { "type":"45", "name":"PTC 9 / PFC 0", "size":"0", "desc":"Absolute time; variable; absolute time based on its p-field (not supported by SCOS2000)" },
            { "type":"46", "name":"PTC 9 / PFC 1", "size":"48", "desc":"Absolute time; 6 octets; absolute time CDS format without microseconds" },
            { "type":"47", "name":"PTC 9 / PFC 2", "size":"64", "desc":"Absolute time; 8 octets; absolute time CDS format with microseconds" },
            { "type":"48", "name":"PTC 9 / PFC 3", "size":"8", "desc":"Absolute time; 1 octet; absolute time CUC format (1 Byte coarse time)" },
            { "type":"49", "name":"PTC 9 / PFC 4", "size":"16", "desc":"Absolute time; 2 octets; absolute time CUC format (1 Byte coarse time)" },
            { "type":"50", "name":"PTC 9 / PFC 5", "size":"24", "desc":"Absolute time; 3 octets; absolute time CUC format (1 Byte coarse time)" },
            { "type":"51", "name":"PTC 9 / PFC 6", "size":"32", "desc":"Absolute time; 4 octet; absolute time CUC format (1 Byte coarse time)" },
            { "type":"52", "name":"PTC 9 / PFC 7", "size":"16", "desc":"Absolute time; 2 octets; absolute time CUC format (2 Bytes coarse time)" },
            { "type":"53", "name":"PTC 9 / PFC 8", "size":"24", "desc":"Absolute time; 3 octets; absolute time CUC format (2 Bytes coarse time)" },
            { "type":"54", "name":"PTC 9 / PFC 9", "size":"32", "desc":"Absolute time; 4 octet; absolute time CUC format (2 Bytes coarse time)" },
            { "type":"55", "name":"PTC 9 / PFC 10", "size":"40", "desc":"Absolute time; 5 octets; absolute time CUC format (2 Bytes coarse time)" },
            { "type":"56", "name":"PTC 9 / PFC 11", "size":"24", "desc":"Absolute time; 3 octets; absolute time CUC format (3 Bytes coarse time)" },
            { "type":"57", "name":"PTC 9 / PFC 12", "size":"32", "desc":"Absolute time; 4 octet; absolute time CUC format (3 Bytes coarse time)" },
            { "type":"58", "name":"PTC 9 / PFC 13", "size":"40", "desc":"Absolute time; 5 octets; absolute time CUC format (3 Bytes coarse time)" },
            { "type":"59", "name":"PTC 9 / PFC 14", "size":"48", "desc":"Absolute time; 6 octets; absolute time CUC format (3 Bytes coarse time)" },
            { "type":"60", "name":"PTC 9 / PFC 15", "size":"32", "desc":"Absolute time; 4 octet; absolute time CUC format (4 Bytes coarse time)" },
            { "type":"61", "name":"PTC 9 / PFC 16", "size":"40", "desc":"Absolute time; 5 octets; absolute time CUC format (4 Bytes coarse time)" },
            { "type":"62", "name":"PTC 9 / PFC 17", "size":"48", "desc":"Absolute time; 6 octets; absolute time CUC format (4 Bytes coarse time)" },
            { "type":"63", "name":"PTC 9 / PFC 18", "size":"56", "desc":"Absolute time; 7 octets; absolute time CUC format (4 Bytes coarse time)" },
            { "type":"64", "name":"PTC 10 / PFC 3", "size":"8", "desc":"Relative time; 1 octet; relative time CUC format (1 Byte coarse time)" },
            { "type":"65", "name":"PTC 10 / PFC 4", "size":"16", "desc":"Relative time; 2 octets; relative time CUC format (1 Byte coarse time)" },
            { "type":"66", "name":"PTC 10 / PFC 5", "size":"24", "desc":"Relative time; 3 octets; relative time CUC format (1 Byte coarse time)" },
            { "type":"67", "name":"PTC 10 / PFC 6", "size":"32", "desc":"Relative time; 4 octet; relative time CUC format (1 Byte coarse time)" },
            { "type":"68", "name":"PTC 10 / PFC 7", "size":"16", "desc":"Relative time; 2 octets; relative time CUC format (2 Bytes coarse time)" },
            { "type":"69", "name":"PTC 10 / PFC 8", "size":"24", "desc":"Relative time; 3 octets; relative time CUC format (2 Bytes coarse time)" },
            { "type":"70", "name":"PTC 10 / PFC 9", "size":"32", "desc":"Relative time; 4 octet; relative time CUC format (2 Bytes coarse time)" },
            { "type":"71", "name":"PTC 10 / PFC 10", "size":"40", "desc":"Relative time; 5 octets; relative time CUC format (2 Bytes coarse time)" },
            { "type":"72", "name":"PTC 10 / PFC 11", "size":"24", "desc":"Relative time; 3 octets; relative time CUC format (3 Bytes coarse time)" },
            { "type":"73", "name":"PTC 10 / PFC 12", "size":"32", "desc":"Relative time; 4 octet; relative time CUC format (3 Bytes coarse time)" },
            { "type":"74", "name":"PTC 10 / PFC 13", "size":"40", "desc":"Relative time; 5 octets; relative time CUC format (3 Bytes coarse time)" },
            { "type":"75", "name":"PTC 10 / PFC 14", "size":"48", "desc":"Relative time; 6 octets; relative time CUC format (3 Bytes coarse time)" },
            { "type":"76", "name":"PTC 10 / PFC 15", "size":"32", "desc":"Relative time; 4 octet; relative time CUC format (4 Bytes coarse time)" },
            { "type":"77", "name":"PTC 10 / PFC 16", "size":"40", "desc":"Relative time; 5 octets; relative time CUC format (4 Bytes coarse time)" },
            { "type":"78", "name":"PTC 10 / PFC 17", "size":"48", "desc":"Relative time; 6 octets; relative time CUC format (4 Bytes coarse time)" },
            { "type":"79", "name":"PTC 10 / PFC 18", "size":"56", "desc":"Relative time; 7 octets; relative time CUC format (4 Bytes coarse time)" }
//            { "type":"80", "name":"PTC 11 / PFC 0", "size":"0", "desc":"Deduced parameter" },
//            { "type":"81", "name":"PTC 13 / PFC 0", "size":"0", "desc":"Saved synthetic parameter" }
        ]
    };
    manageOptionPUS(data.data, type, size);
}

/* Add new option to select */
function manageOptionPUS(data, type, size) {
	$("#sel_pusdatatype").empty();
	var octets = false;
	$.each( data, function( key, value ) {
		// get resulting size
		var res_size = value.size.split(":");
		// octets?
		if (size % 8 == 0) octets = true;
		
		if (res_size.length==1) {
			if ((size==parseInt(value.size)) || (parseInt(value.size)==0)) {
				
				// get PTC 
				var ptc = getPTC(value.name);
				// get PFC
				var pfc = getPFC(value.name, size);
				
				if (!((value.type==41 || value.type==42 || value.type==43 || value.type==44 || value.type==45) && !octets)) {
					if (type==value.type) {
						$("#sel_pusdatatype").append('<option value="'+value.type+'_'+ptc+'_'+pfc+'" selected>'+value.name+' ('+value.desc+')</option>');
					} else {
						$("#sel_pusdatatype").append('<option value="'+value.type+'_'+ptc+'_'+pfc+'">'+value.name+' ('+value.desc+')</option>');
					}
				}
			}
		} else {
			var lo_size = res_size[0];
			var hi_size = res_size[1];
			if ((size>=parseInt(lo_size)) && (size<=parseInt(hi_size))) {
				
				// get PTC 
				var ptc = getPTC(value.name);
				// get PFC
				var pfc = getPFC(value.name, size);
				
				if (!((value.type==41 || value.type==42 || value.type==43 || value.type==44 || value.type==45) && !octets)) {
					if (type==value.type) {
						$("#sel_pusdatatype").append('<option value="'+value.type+'_'+ptc+'_'+pfc+'" selected>'+value.name+' ('+value.desc+')</option>');
					} else {
						$("#sel_pusdatatype").append('<option value="'+value.type+'_'+ptc+'_'+pfc+'">'+value.name+' ('+value.desc+')</option>');
					}
				}
			}
		}
	
	});
}

/**
* Get PTC value
*/
function getPTC(vname) {
    var ptf_pfc = vname.split(' / ');
    var ptc = ptf_pfc[0].substring(4);
    return ptc;
}

/**
* Get PFC value
*/
function getPFC(vname, size) {
    var ptf_pfc = vname.split(' / ');
    var pfc = ""
    if (ptf_pfc.length>1) {
        if ((ptf_pfc[1].substring(5,4)=='>') && (size % 8 == 0)) {
            pfc = size/8;
        } else if (ptf_pfc[1].substring(5,4)=='P') {
            pfc = size;
        } else {
            pfc = ptf_pfc[1].substring(4);
        }
    }
    return pfc;
}

function updateDivPusdatatype() {
	var x = document.getElementById("fsize");
	var ele = document.getElementById("ptf");
	
	ele.style.display = "block";
	
    //x.value = x.value.toUpperCase();

    //updateDiv();

    getDropdownDataPUS("", ""+x.value);

    //var container = document.getElementById("sel_pusdatatype");
    //var content = container.innerHTML;
    //container.innerHTML= content; 

    //$('#pusdatatype').trigger('change');
}
