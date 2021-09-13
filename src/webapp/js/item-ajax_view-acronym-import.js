$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;
var dropdown = "";

var idProject = getUrlVars()["idProject"];
var dpDomain = getUrlVars()["dpDomain"];

    getDropdownDataKindCreate();
    getDropdownDataParameterDatatypeCreate();

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
		url: url+'api/getData_view-acronym-import.php?idProject='+idProject,
		data: {page:page}
	}).done(function(data){
		total_page = Math.ceil(data.total/5);
		current_page = page;
		if (data.total == 0) {
			total_page = 1;
			current_page = 1;
		}
		//alert('A) Domain = '+dpDomain+' | Project = '+idProject)

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
	//alert('B) Domain = '+dpDomain+' | Project = '+idProject)
}

/* manage data list for all items */
function manageDataAll() {
	is_ajax_fire = 0;

	$.ajax({
		dataType: 'json',
		url: url+'api/getData_view-acronym.php?idProject='+idProject+'&showAll=1',
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
		url: url+'api/getData_view-acronym-import.php?idProject='+idProject,
		data: {page:page}
	}).done(function(data){
		manageRow(data.data);
	});
}

/* Get Dropdown Data for Kind */
function getDropdownDataKind(kind, id) {
    var data = { 
        "data": [ 
            { "kind":"3", "name":"Par", "desc":"Parameter" }, 
            { "kind":"4", "name":"Var", "desc":"Variable" },
            { "kind":"5", "name":"Par Imp", "desc":"Parameter Imported" }, 
            { "kind":"6", "name":"Var Imp", "desc":"Variable Imported" }
        ]
    };
    manageOptionKind(data.data, kind, id);
}

/* Get Dropdown Data for Packet Parameter */
function getDropdownDataParameterDatatype(idType, id) {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-parameter-datatype.php?idProject='+idProject,
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionParameterDatatype(data.data, idType, id);
	});
}

/* Add new option to select */
function manageOptionKind(data, kind, id) {
	$(id).empty();
	$.each( data, function( key, value ) {
		if (kind==value.kind) {
			$(id).append('<option value="'+value.kind+'" selected>'+value.name+'</option>');
		} else {
			$(id).append('<option value="'+value.kind+'">'+value.name+'</option>');
		}
	});
}

/* Add new option to select */
function manageOptionParameterDatatype(data, idType, id) {
	$(id).empty();
	$.each( data, function( key, value ) {
		if (idType==value.id) {
			$(id).append('<option value="'+value.id+'" selected>'+value.domain+' / '+value.name+' ('+value.id+')</option>');
		} else {
			$(id).append('<option value="'+value.id+'">'+value.domain+' / '+value.name+' ('+value.id+')</option>');
		}
	});
}

/* Get Dropdown Data for Kind */
function getDropdownDataKindCreate() {
    var data = { 
        "data": [ 
            { "kind":"", "name":"--- Please select ---", "desc":"" }, 
            { "kind":"3", "name":"Par", "desc":"Parameter" }, 
            { "kind":"4", "name":"Var", "desc":"Variable" },
            { "kind":"5", "name":"Par Imp", "desc":"Parameter Imported" }, 
            { "kind":"6", "name":"Var Imp", "desc":"Variable Imported" }
        ]
    };
    manageOptionKindCreate(data.data);
}

/* Get Dropdown Data for Packet Parameter */
function getDropdownDataParameterDatatypeCreate() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-parameter-datatype.php?idProject='+idProject,
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionParameterDatatypeCreate(data.data);
	});
}

/* Add new option to select */
function manageOptionKindCreate(data) {
	$("#sel_kind_create").empty();
	$.each( data, function( key, value ) {
		if (value.kind=="") {
			$("#sel_kind_create").append('<option value="'+value.kind+'" selected>'+value.name+'</option>');
		} else {
			$("#sel_kind_create").append('<option value="'+value.kind+'">'+value.name+'</option>');
		}
	});
}

/* Add new option to select */
function manageOptionParameterDatatypeCreate(data) {
	$("#sel_datatype_create").empty();
	$("#sel_datatype_create").append('<option value="" selected>--- Please select ---</option>');
	$.each( data, function( key, value ) {
		$("#sel_datatype_create").append('<option value="'+value.id+'">'+value.domain+' / '+value.name+' ('+value.id+')</option>');
	});
}

/* Add new Item table row */
function manageRow(data) {
	var	rows = '';
	$.each( data, function( key, value ) {
	  	rows = rows + '<tr>';
	  	rows = rows + '<td>'+value.id+'</td>';
	  	rows = rows + '<td>'+value.name+'</td>';
	  	rows = rows + '<td>'+value.shortDesc+'</td>';
	  	rows = rows + '<td><p style="word-break:normal;">'+value.desc+'</p></td>';
	  	rows = rows + '<td data-id="'+value.id+'">';
        //rows = rows + '<button data-toggle="modal" data-target="#edit-item" class="btn btn-primary edit-item">Edit</button> ';
        rows = rows + '<button data-toggle="modal" data-target="#edit-item" class="btn btn-secondary edit-item">Show</button> ';
        rows = rows + '<button class="btn btn-danger remove-item">Unlink</button>';
        rows = rows + '</td>';
	  	rows = rows + '</tr>';
	});

	//$("tbody").html(rows);
	$("#myTable").html(rows);
}

/* Show all Items */
$(".crud-submit-show").click(function(e){
    manageDataAll();
});

/* Create new Item */
$(".crud-submit").click(function(e){

    //e.preventDefault();
    var form_action = $("#create-item").find("form").attr("action-data");
    var id = $("#create-item").find("input[name='id']").val();
    var domain = $("#create-item").find("input[name='domain']").val();
    var name = $("#create-item").find("input[name='name']").val();
    if (!idProject.length) {
      var kind = $("#create-item").find("input[name='kind']").val();
    } else {
      var kind = $("#create-item").find("select[name='kind']").val();
    }
    var shortDesc = $("#create-item").find("textarea[name='shortDesc']").val();
    if (!idProject.length) {
      var idType = $("#create-item").find("input[name='idType']").val();
    } else {
      var idType = $("#create-item").find("select[name='idType']").val();
    }
    var multiplicity = $("#create-item").find("input[name='multiplicity']").val();
    var value = $("#create-item").find("input[name='value']").val();
    var unit = $("#create-item").find("input[name='unit']").val();

    if(id != '' && idProject != '' && domain != '' && name != '' && kind != '' && 
       idType != '' && value != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, idProject:idProject, domain:domain, name:name, kind:kind, 
            shortDesc:shortDesc,idType:idType, multiplicity:multiplicity, value:value,
            unit:unit}
        }).done(function(data){
            $("#create-item").find("input[name='id']").val('');
            $("#create-item").find("input[name='domain']").val('');
            $("#create-item").find("input[name='name']").val('');
            if (!idProject.length) {
              $("#create-item").find("input[name='kind']").val('');
            } else {
              $("#create-item").find("select[name='kind']").val('');
            }
            $("#create-item").find("textarea[name='shortDesc']").val('');
            if (!idProject.length) {
              $("#create-item").find("input[name='idType']").val('');
            } else {
              $("#create-item").find("select[name='idType']").val('');
            }

            $("#create-item").find("input[name='multiplicity']").val('');
            $("#create-item").find("input[name='value']").val('');
            $("#create-item").find("input[name='unit']").val('');
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Item Created Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing title or description or other mandatory item.')
    }

});

/* Add Item */
$("body").on("click",".add-item",function(){
	var id = $(this).parent("td").data('id');
    var name = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var shortDesc = $(this).parent("td").prev("td").prev("td").text();
    var desc = $(this).parent("td").prev("td").text();
	toastr.success('Item ['+name+' / '+shortDesc+'] for '+idProject+' Linked Successfully.', 'Success Alert', {timeOut: 5000});
	
	var c_obj = $(this).parents("tr");
	$.ajax({
		/*dataType: 'json',*/
		type:'POST',
		url: url + 'api/create_view-acronym-import.php?idProject='+idProject,
		data:{id:id, name:name, shortDesc:shortDesc, desc:desc},
		success: function(result) { // we got the response
			//alert('Successfully called');
		},
		error: function(jqxhr, status, exception) {
			alert(status + ' | '+jqxhr+' | Exception:', exception);
		}
	}).done(function(data){
		c_obj.remove();
		toastr.success('Item Added Successfully.', 'Success Alert', {timeOut: 5000});
		getPageData();
	});
	toastr.success('B) Item Added Successfully.', 'Success Alert', {timeOut: 5000});

});

/* Link Item */
$("body").on("click",".link-item",function(){
	var id = $(this).parent("td").data('id');
    var name = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var shortDesc = $(this).parent("td").prev("td").prev("td").text();
    var desc = $(this).parent("td").prev("td").text();
	//toastr.success('Item ['+name+' / '+shortDesc+'] for '+idProject+' Linked Successfully.', 'Success Alert', {timeOut: 5000});
	
	var c_obj = $(this).parents("tr");
	$.ajax({
		/*dataType: 'json',*/
		type:'POST',
		url: url + 'api/create_view-acronym-import.php?action=link&idProject='+idProject,
		data:{id:id, name:name, shortDesc:shortDesc, desc:desc},
		success: function(result) { // we got the response
			//alert('Successfully called');
		},
		error: function(jqxhr, status, exception) {
			alert(status + ' | '+jqxhr+' | Exception:', exception);
		}
	}).done(function(data){
		c_obj.remove();
		toastr.success('Item Linked Successfully.', 'Success Alert', {timeOut: 5000});
		getPageData();
	});
	//toastr.success('B) Item Linked Successfully.', 'Success Alert', {timeOut: 5000});

});

/* Remove Item */
$("body").on("click",".remove-item",function(){
	var id = $(this).parent("td").data('id');
	var c_obj = $(this).parents("tr");
	$.ajax({
		dataType: 'json',
		type:'POST',
		url: url + 'api/delete_view-acronym-import.php',
		data:{id:id}
	}).done(function(data){
		c_obj.remove();
		toastr.success('Item Unlinked Successfully.', 'Success Alert', {timeOut: 5000});
		getPageData();
	});

});

/* Edit Item */
$("body").on("click",".edit-item",function(){

    var id = $(this).parent("td").data('id');
    var name = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var shortDesc = $(this).parent("td").prev("td").prev("td").text();
    var desc = $(this).parent("td").prev("td").text();

    //getDropdownDataKind(kind, "#sel_kind");
    //getDropdownDataParameterDatatype(idType, "#sel_datatype");

    $("#edit-item").find("input[name='name']").val(name);
    $("#edit-item").find("input[name='shortDesc']").val(shortDesc);
    $("#edit-item").find("textarea[name='desc']").val(desc);
    $("#edit-item").find(".edit-id").val(id);

});

/* Show Item */
$("body").on("click",".show-item",function(){

    var id = $(this).parent("td").data('id');
    var name = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var shortDesc = $(this).parent("td").prev("td").prev("td").text();
    var desc = $(this).parent("td").prev("td").text();

    //getDropdownDataKind(kind, "#sel_kind_show");
    //getDropdownDataParameterDatatype(idType, "#sel_datatype_show");

    $("#show-item").find("input[name='name']").val(name);
    $("#show-item").find("input[name='shortDesc']").val(shortDesc);
    $("#show-item").find("textarea[name='desc']").val(desc);
    $("#show-item").find(".show-id").val(id);

});

/* Updated new Item */
$(".crud-submit-edit").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-item").find("form").attr("action");
    var name = $("#edit-item").find("input[name='name']").val();
    var shortDesc = $("#edit-item").find("input[name='shortDesc']").val();
    var desc = $("#edit-item").find("textarea[name='desc']").val();
    var id = $("#edit-item").find(".edit-id").val();

    if(id != '' && name != '' && shortDesc != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, name:name, shortDesc:shortDesc, desc:desc}
        }).done(function(data){
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Item Updated Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing name or short description.')
    }

});

});