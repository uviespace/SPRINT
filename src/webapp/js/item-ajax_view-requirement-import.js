$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;
var dropdown = "";

var idProject = getUrlVars()["idProject"];
var dpDomain = getUrlVars()["dpDomain"];
var idReqList = getUrlVars()["idReqList"];

console.log("idReqList = "+idReqList);

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
    console.log('api/getData_view-requirement-import.php?idProject='+idProject+'&idReqList='+idReqList);
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_view-requirement-import.php?idProject='+idProject+'&idReqList='+idReqList,
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
		url: url+'api/getData_view-requirement-import.php?idProject='+idProject+'&idReqList='+idReqList+'&showAll=1',
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
		url: url+'api/getData_view-requirement-import.php?idProject='+idProject+'&idReqList='+idReqList,
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
        if (idReqList==10) {
            rows = rows + '<td>'+value.requirementId+'</td>';
            rows = rows + '<td></td>';
            rows = rows + '<td><p style="word-break:normal;">'+value.desc+'</p></td>';
            rows = rows + '<td><p style="word-break:normal;">'+value.notes+'</p></td>';
            rows = rows + '<td><p style="word-break:normal;">'+value.justification+'</p></td>';
            rows = rows + '<td>'+value.applicability+'</td>';
            rows = rows + '<td>'+value.applicableToPayloads+'</td>';
        } else if (idReqList==15) {
            rows = rows + '<td>'+value.requirementId+'</td>';
            rows = rows + '<td>'+value.shortDesc+'</td>';
            rows = rows + '<td><p style="word-break:normal;">'+value.desc+'</p></td>';
            rows = rows + '<td><p style="word-break:normal;">'+value.notes+'</p></td>';
        } else {
            rows = rows + '<td>'+value.clause+'</td>';
            rows = rows + '<td><p style="word-break:normal;">'+value.desc+'</p></td>';
            rows = rows + '<td>'+value.shortDesc+'</td>';
        }
	  	rows = rows + '<td data-id="'+value.id+'">';
        rows = rows + '<button data-toggle="modal" data-target="#edit-item" class="btn btn-primary edit-item">Edit</button> ';
        //rows = rows + '<button data-toggle="modal" data-target="#edit-item" class="btn btn-secondary show-item">Show</button> ';
        //rows = rows + '<button class="btn btn-danger remove-item">Unlink</button>';
        rows = rows + '<button class="btn btn-danger remove-item">Delete</button>';
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
		url: url + 'api/create_view-requirement-import.php?idProject='+idProject,
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

/* Add Item 4..9 */
$("body").on("click",".add-item4",function(){
	var id = $(this).parent("td").data('id');
    var col01 = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var col02 = $(this).parent("td").prev("td").prev("td").text();
    var col03 = $(this).parent("td").prev("td").text();
	//toastr.success('Item ['+col01+' / '+col02+' / '+col03+'] Linked Successfully.', 'Success Alert', {timeOut: 5000});
    
    console.log('Item ['+col01+' / '+col02+' / '+col03+'] Linked Successfully.');
    
	var c_obj = $(this).parents("tr");
	$.ajax({
		/*dataType: 'json',*/
		type:'POST',
		url: url + 'api/create_view-requirement-import.php?idReqList='+idReqList,
		data:{id:id, col01:col01, col02:col02, col03:col03},
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
	//toastr.success('B) Item Added Successfully.', 'Success Alert', {timeOut: 5000});

});

/* Add Item 10 */
$("body").on("click",".add-item10",function(){
	var id = $(this).parent("td").data('id');
    var reqId = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var ecssClause = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var reqText = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var notes = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var justification = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var applicability = $(this).parent("td").prev("td").prev("td").text();
    var applicableToPL = $(this).parent("td").prev("td").text();
	//toastr.success('Item ['+reqId+' / '+ecssClause+' / '+reqText+'] Linked Successfully.', 'Success Alert', {timeOut: 5000});
    
    console.log('Item ['+reqId+' / '+ecssClause+' / '+reqText+'] Linked Successfully.');
    
	var c_obj = $(this).parents("tr");
	$.ajax({
		/*dataType: 'json',*/
		type:'POST',
		url: url + 'api/create_view-requirement-import.php?idProject='+idProject+'&idReqList='+idReqList,
		data:{id:id, reqId:reqId, ecssClause:ecssClause, reqText:reqText, notes:notes, justification:justification,
              applicability:applicability, applicableToPL:applicableToPL},
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
	//toastr.success('B) Item Added Successfully.', 'Success Alert', {timeOut: 5000});

});

/* Add Item 15 */
$("body").on("click",".add-item15",function(){
	var id = $(this).parent("td").data('id');
    var reqId = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var reqShortText = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var reqText = $(this).parent("td").prev("td").prev("td").text();
    var comment = $(this).parent("td").prev("td").text();
	toastr.success('Item ['+reqId+' / '+reqShortText+' / '+reqText+'] Linked Successfully.', 'Success Alert', {timeOut: 5000});
    
    console.log('Item ['+reqId+' / '+reqShortText+' / '+reqText+'] Linked Successfully.');
    
	var c_obj = $(this).parents("tr");
	$.ajax({
		/*dataType: 'json',*/
		type:'POST',
		url: url + 'api/create_view-requirement-import.php?idProject='+idProject+'&idReqList='+idReqList,
		data:{id:id, reqId:reqId, reqShortText:reqShortText, reqText:reqText, comment:comment},
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
		url: url + 'api/create_view-requirement-import.php?action=link&idProject='+idProject,
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
		url: url + 'api/delete_view-requirement-import.php',
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
    var requirementId = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var shortDesc = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var desc = $(this).parent("td").prev("td").prev("td").text();
    var notes = $(this).parent("td").prev("td").text();

    //getDropdownDataKind(kind, "#sel_kind");
    //getDropdownDataParameterDatatype(idType, "#sel_datatype");

    $("#edit-item").find("input[name='requirementId']").val(requirementId);
    $("#edit-item").find("textarea[name='shortDesc']").val(shortDesc);
    $("#edit-item").find("textarea[name='desc']").val(desc);
    $("#edit-item").find("textarea[name='notes']").val(notes);
    $("#edit-item").find(".edit-id").val(id);

});

/* Show Item */
$("body").on("click",".show-item",function(){

    var id = $(this).parent("td").data('id');
    var requirementId = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var shortDesc = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var desc = $(this).parent("td").prev("td").prev("td").text();
    var notes = $(this).parent("td").prev("td").text();

    //getDropdownDataKind(kind, "#sel_kind_show");
    //getDropdownDataParameterDatatype(idType, "#sel_datatype_show");

    $("#show-item").find("input[name='requirementId']").val(requirementId);
    $("#show-item").find("textarea[name='shortDesc']").val(shortDesc);
    $("#show-item").find("textarea[name='desc']").val(desc);
    $("#show-item").find("textarea[name='notes']").val(notes);
    $("#show-item").find(".show-id").val(id);

});

/* Updated new Item */
$(".crud-submit-edit").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-item").find("form").attr("action");
    var requirementId = $("#edit-item").find("input[name='requirementId']").val();
    var shortDesc = $("#edit-item").find("textarea[name='shortDesc']").val();
    var desc = $("#edit-item").find("textarea[name='desc']").val();
    var notes = $("#edit-item").find("textarea[name='notes']").val();
    var id = $("#edit-item").find(".edit-id").val();

    if(id != '' && requirementId != '' && shortDesc != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, shortDesc:shortDesc, desc:desc, notes:notes}
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