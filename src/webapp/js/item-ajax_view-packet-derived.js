$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;
var dropdown = ""; // needed for dropdown menus for initialization

var idStandard = getUrlVars()["idStandard"];
var idParent = getUrlVars()["idParent"];

getDropdownDataEnumerationSetCreate();
/*getDropdownDataDiscriminantCreate();*/

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
		url: url+'api/getData_view-packet-derived.php?idParent='+idParent,
		data: {page:page}
	}).done(function(data){
		total_page = Math.ceil(data.total/5);
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
		url: url+'api/getData_view-packet-derived.php?idParent='+idParent+'&showAll=1',
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
		url: url+'api/getData_view-packet-derived.php?idParent='+idParent,
		data: {page:page}
	}).done(function(data){
		manageRow(data.data);
	});
}

/* Get Dropdown Data for Enaumeration Set */
function getDropdownDataEnumerationSetCreate() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-enumeration-set.php?idStandard='+idStandard+'&idParent='+idParent,
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionEnumerationSetCreate(data.data);
	});
}

/* Add new option to select */
function manageOptionEnumerationSetCreate(data) {
	$("#sel_enumeration-set_create").empty();
	$("#sel_enumeration-set_create").append('<option value="" selected>--- Please select ---</option>');
	$.each( data, function( key, value ) {
		$("#sel_enumeration-set_create").append('<option value="'+value.id+'">'+value.name+'</option>');
	});
}

/* Get Dropdown Data for Discriminant */
/*function getDropdownDataDiscriminantCreate() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-discriminant.php?idStandard='+idStandard+'&idParent='+idParent,
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionDiscriminantCreate(data.data);
	});
}*/

/* Add new option to select */
/*function manageOptionDiscriminantCreate(data) {
	$("#sel_discriminant_create").empty();
	$("#sel_discriminant_create").append('<option value="" selected>--- Please select ---</option>');
	$.each( data, function( key, value ) {
		$("#sel_discriminant_create").append('<option value="'+value.id+'">'+value.discriminant+'</option>');
	});
}*/

/* Add new Item table row */
function manageRow(data) {
	var	rows = '';
	$.each( data, function( key, value ) {
	  	rows = rows + '<tr>';
	  	rows = rows + '<td>'+value.id+'</td>';
	  	//rows = rows + '<td>'+value.idStandard+'</td>';
	  	//rows = rows + '<td>'+value.idParent+'</td>';
	  	//rows = rows + '<td>'+value.idProcess+'</td>';
	  	//rows = rows + '<td>'+value.kind+'</td>';
	  	//rows = rows + '<td>'+value.type+'</td>';
	  	//rows = rows + '<td>'+value.subtype+'</td>';
	  	rows = rows + '<td>'+value.discriminant+'</td>'; // IMPORTANT
	  	//rows = rows + '<td>'+value.domain+'</td>';
	  	rows = rows + '<td>'+value.name+'</td>';         // IMPORTANT
	  	rows = rows + '<td>'+value.shortDesc+'</td>';    // IMPORTANT
	  	rows = rows + '<td>'+value.desc+'</td>';         // IMPORTANT / DETAILS
	  	rows = rows + '<td>'+value.descParam+'</td>';    // DETAILS
	  	rows = rows + '<td>'+value.descDest+'</td>';     // DETAILS
	  	rows = rows + '<td>'+value.code+'</td>';         // DETAILS
	  	//rows = rows + '<td>'+value.setting+'</td>';
	  	rows = rows + '<td data-id="'+value.id+'">';
        rows = rows + '<button data-toggle="modal" data-target="#edit-item" class="btn btn-primary edit-item">Edit</button> ';
        rows = rows + '<button data-toggle="modal" data-target="#edit-detail" class="btn btn-primary edit-detail">Detail</button> ';
        rows = rows + '<button data-toggle="modal" data-target="#open-body" class="btn btn-primary open-body">Body</button> ';
        rows = rows + '<button class="btn btn-danger remove-item">Delete</button>';
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
    var idStandard = $("#create-item").find("input[name='idStandard']").val();
    var idParent = $("#create-item").find("input[name='idParent']").val();
    var kind = $("#create-item").find("input[name='kind']").val();
    var subtype = $("#create-item").find("input[name='subtype']").val();
    var discriminant = $("#create-item").find("select[name='discriminant']").val();
    var name = $("#create-item").find("input[name='name']").val();
    var shortDesc = $("#create-item").find("input[name='shortDesc']").val();
    var desc = $("#create-item").find("textarea[name='desc']").val();
    var descParam = $("#create-item").find("input[name='descParam']").val();
    var descDest = $("#create-item").find("input[name='descDest']").val();
    var code = $("#create-item").find("input[name='code']").val();

    if(idStandard != '' && idParent != '' && discriminant != '' && desc != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{idStandard:idStandard, idParent:idParent, kind:kind, subtype:subtype, 
                  discriminant:discriminant, name:name, shortDesc:shortDesc, desc:desc,
                  descParam:descParam, descDest:descDest, code:code}
        }).done(function(data){
            $("#create-item").find("input[name='idStandard']").val('');
            $("#create-item").find("input[name='idParent']").val('');
            $("#create-item").find("input[name='kind']").val('');
            $("#create-item").find("input[name='subtype']").val('');
            $("#create-item").find("select[name='discriminant']").val('');
            $("#create-item").find("input[name='name']").val('');
            $("#create-item").find("input[name='shortDesc']").val('');
            $("#create-item").find("textarea[name='desc']").val('');
            $("#create-item").find("input[name='descParam']").val('');
            $("#create-item").find("input[name='descDest']").val('');
            $("#create-item").find("input[name='code']").val('');
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
		url: url + 'api/delete_view-packet-derived.php',
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
    var discriminant = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var name = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var shortDesc = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var desc = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    /*var descParam = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var descDest = $(this).parent("td").prev("td").prev("td").text();
    var code = $(this).parent("td").prev("td").text();*/

    getDropdownDataDiscriminant(discriminant);

    $("#edit-item").find("select[name='discriminant']").val(discriminant);
    $("#edit-item").find("input[name='name']").val(name);
    $("#edit-item").find("input[name='shortDesc']").val(shortDesc);
    $("#edit-item").find("textarea[name='desc']").val(desc);
    /*$("#edit-item").find("input[name='descParam']").val(descParam);
    $("#edit-item").find("input[name='descDest']").val(descDest);
    $("#edit-item").find("input[name='code']").val(code);*/
    $("#edit-item").find(".edit-id").val(id);

});

/* Edit Body of Item */
$("body").on("click",".edit-detail",function(){

    var id = $(this).parent("td").data('id');
    var discriminant = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var name = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var shortDesc = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var desc = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var descParam = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var descDest = $(this).parent("td").prev("td").prev("td").text();
    var code = $(this).parent("td").prev("td").text();

    $("#edit-detail").find("input[name='discriminant']").val(discriminant);
    $("#edit-detail").find("input[name='name']").val(name);
    $("#edit-detail").find("input[name='shortDesc']").val(shortDesc);
    $("#edit-detail").find("input[name='desc']").val(desc);
    $("#edit-detail").find("input[name='descParam']").val(descParam);
    $("#edit-detail").find("input[name='descDest']").val(descDest);
    $("#edit-detail").find("input[name='code']").val(code);
    $("#edit-detail").find(".edit-id").val(id);

});


/* Open Body of Item */
$("body").on("click",".open-body",function(){

    toastr.success('Open body entered.', 'Success Alert', {timeOut: 5000});

    var id = $(this).parent("td").data('id');
    var c_obj = $(this).parents("tr");

    toastr.success('Open body: id = '+id, 'Success Alert', {timeOut: 5000});

    var url = 'view_packet-params-derived.php?idProject=1016&idStandard='+idStandard+'&idParent='+idParent+'&idPacket='+id;
    var w = window.open(url,"_self")

});

/* Updated new Item */
$(".crud-submit-edit").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-item").find("form").attr("action");
    var discriminant = $("#edit-item").find("select[name='discriminant']").val();
    var name = $("#edit-item").find("input[name='name']").val();
    var shortDesc = $("#edit-item").find("input[name='shortDesc']").val();
    var desc = $("#edit-item").find("textarea[name='desc']").val();
    /*var descParam = $("#edit-item").find("input[name='descParam']").val();
    var descDest = $("#edit-item").find("input[name='descDest']").val();
    var code = $("#edit-item").find("input[name='code']").val();*/
    var id = $("#edit-item").find(".edit-id").val();

    if(id != '' && discriminant != '' && desc != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, discriminant:discriminant, name:name, shortDesc:shortDesc, desc:desc},
            success: function(results, textStatus) {
                toastr.success('Database Operation Successfully. ' + results, 'Success Alert', {timeOut: 5000});
            },
            error: function(xhr, status, error)
            {
                toastr.error('Database Operation Failed. ' + xhr.responseText, 'Failure Alert', {timeOut: 5000});
            }
        }).fail(function(data) {
            if ( data.responseCode )
                console.log( data.responseCode );
        }).done(function(data){
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Item Updated Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing something.')
    }

});

/* Updated new Item */
$(".crud-submit-edit-detail").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-item").find("form").attr("action");
    var discriminant = $("#edit-item").find("input[name='discriminant']").val();
    var name = $("#edit-item").find("input[name='name']").val();
    var shortDesc = $("#edit-item").find("input[name='shortDesc']").val();
    var desc = $("#edit-item").find("input[name='desc']").val();
    var descParam = $("#edit-item").find("input[name='descParam']").val();
    var descDest = $("#edit-item").find("input[name='descDest']").val();
    var code = $("#edit-item").find("input[name='code']").val();
    var id = $("#edit-item").find(".edit-id").val();

    if(id != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, discriminant:discriminant, name:name, shortDesc:shortDesc, desc:desc},
            success: function(results, textStatus) {
                toastr.success('Database Operation Successfully. ' + results, 'Success Alert', {timeOut: 5000});
            },
            error: function(xhr, status, error)
            {
                toastr.error('Database Operation Failed. ' + xhr.responseText, 'Failure Alert', {timeOut: 5000});
            }
        }).fail(function(data) {
            if ( data.responseCode )
                console.log( data.responseCode );
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

/* Get Dropdown Data for Discriminant */
function getDropdownDataDiscriminant(discriminant) {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-discriminant.php',
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionDiscriminant(data.data, discriminant);
	});
}

/* Add new option to select */
function manageOptionDiscriminant(data, discriminant) {
	$("#sel_discriminant").empty();
	$.each( data, function( key, value ) {
        if (discriminant==value.name) {
            $("#sel_discriminant").append('<option value="'+value.name+'" selected>'+value.idType+' / '+value.name+'</option>');
        } else {
            $("#sel_discriminant").append('<option value="'+value.name+'">'+value.idType+' / '+value.name+'</option>');
        }
	});
}

/* Get Dropdown Data for Discriminant */
function getDropdownDataDiscriminantCreate(enumset) {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-discriminant.php?idType='+enumset,
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionDiscriminantCreate(data.data, enumset);
	});
}

/* Add new option to select */
function manageOptionDiscriminantCreate(data, enumset) {
	$("#sel_discriminant_create").empty();
	$("#sel_discriminant_create").append('<option value="" selected>--- Please select ---</option>');
	$.each( data, function( key, value ) {
		$("#sel_discriminant_create").append('<option value="'+value.name+'">'+value.name+'</option>');
	});
}

function updateDivDiscriminantCreate() {
	var x = document.getElementById("sel_enumeration-set_create");
	var ele = document.getElementById("disc");

	ele.style.display = "block";
	
    //x.value = x.value.toUpperCase();

    //updateDiv();

    getDropdownDataDiscriminantCreate(""+x.value);

    //var container = document.getElementById("sel_pusdatatype");
    //var content = container.innerHTML;
    //container.innerHTML= content; 

    //$('#pusdatatype').trigger('change');
}

/* Get Description Data for Discriminant */
function getDropdownDataDiscriminantDescriptionCreate(disc) {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-discriminant-description.php?id='+disc, // TODO: id=disc does not work
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionDiscriminantDescriptionCreate(data.data, disc);
	});
}

/* Insert value to textarea */
function manageOptionDiscriminantDescriptionCreate(data, disc) {
	$("#descr_textarea_create").empty();
	$.each( data, function( key, value ) {
        if (disc==value.name) {
            $("#descr_textarea_create").val(value.desc);
        }
	});
}

function updateDivDiscriminantDescriptionCreate() {
	var x = document.getElementById("sel_discriminant_create");
	var ele = document.getElementById("descr");

	//ele.style.display = "block";

    //x.value = x.value.toUpperCase();

    //updateDiv();

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
		url: url+'api/getData_dd-discriminant-description.php?id='+disc, // TODO: id=disc does not work
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionDiscriminantDescription(data.data, disc);
	});
}

/* Insert value to textarea */
function manageOptionDiscriminantDescription(data, disc) {
	$("#descr_textarea").empty();
	$.each( data, function( key, value ) {
        if (disc==value.name) {
            $("#descr_textarea").val(value.desc);
        }
	});
}

function updateDivDiscriminantDescription() {
	var x = document.getElementById("sel_discriminant");
	var ele = document.getElementById("descr");

	//ele.style.display = "block";

    //x.value = x.value.toUpperCase();

    //updateDiv();

    getDropdownDataDiscriminantDescription(""+x.value);

    //var container = document.getElementById("sel_pusdatatype");
    //var content = container.innerHTML;
    //container.innerHTML= content; 

    //$('#pusdatatype').trigger('change');
}