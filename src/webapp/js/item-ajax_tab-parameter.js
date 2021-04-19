$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;

manageData();

/* manage data list */
function manageData() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_tab-parameter.php',
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

		manageRow(data.data);
		is_ajax_fire = 1;

	});

}

/* Get Page Data*/
function getPageData() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_tab-parameter.php',
		data: {page:page}
	}).done(function(data){
		manageRow(data.data);
	});
}

/* Add new Item table row */
function manageRow(data) {
	var	rows = '';
	$.each( data, function( key, value ) {
	  	rows = rows + '<tr>';
	  	rows = rows + '<td>'+value.id+'</td>';
	  	rows = rows + '<td>'+value.idStandard+'</td>';
	  	rows = rows + '<td>'+value.idType+'</td>';
	  	rows = rows + '<td>'+value.kind+'</td>';
	  	rows = rows + '<td>'+value.domain+'</td>';
	  	rows = rows + '<td>'+value.name+'</td>';
	  	rows = rows + '<td>'+value.shortDesc+'</td>';
	  	rows = rows + '<td>'+value.desc+'</td>';
	  	rows = rows + '<td>'+value.value+'</td>';
	  	rows = rows + '<td>'+value.size+'</td>';
	  	rows = rows + '<td>'+value.unit+'</td>';
	  	rows = rows + '<td>'+value.multiplicity+'</td>';
	  	rows = rows + '<td>'+value.setting+'</td>';
	  	rows = rows + '<td>'+value.role+'</td>';
	  	rows = rows + '<td data-id="'+value.id+'">';
        rows = rows + '<button data-toggle="modal" data-target="#edit-item" class="btn btn-primary edit-item">Edit</button> ';
        rows = rows + '<button class="btn btn-danger remove-item">Delete</button>';
        rows = rows + '</td>';
	  	rows = rows + '</tr>';
	});

	$("tbody").html(rows);
}

/* Create new Item */
$(".crud-submit").click(function(e){
    //e.preventDefault();
    var form_action = $("#create-item").find("form").attr("action-data");
    var id = $("#create-item").find("input[name='id']").val();
    var idStandard = $("#create-item").find("input[name='idStandard']").val();
    var idType = $("#create-item").find("input[name='idType']").val();
    var kind = $("#create-item").find("input[name='kind']").val();
    var domain = $("#create-item").find("input[name='domain']").val();
    var name = $("#create-item").find("input[name='name']").val();
    var shortDesc = $("#create-item").find("input[name='shortDesc']").val();
    var desc = $("#create-item").find("textarea[name='desc']").val();
    var value = $("#create-item").find("input[name='value']").val();
    var size = $("#create-item").find("input[name='size']").val();
    var unit = $("#create-item").find("input[name='unit']").val();
    var multiplicity = $("#create-item").find("input[name='multiplicity']").val();
    var setting = $("#create-item").find("textarea[name='setting']").val();
    var role = $("#create-item").find("input[name='role']").val();

    if(id != '' && idStandard != '' && idType != '' && kind != '' && domain != '' &&
       name != '' && shortDesc != '' && desc != '' && value != '' && size != '' && 
       unit != '' &&  multiplicity != '' && setting != '' && role != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, idStandard:idStandard, idType:idType, kind:kind, domain:domain,
            name:name, shortDesc:shortDesc, desc:desc, value:value, size:size, unit:unit, 
            multiplicity:multiplicity, setting:setting, role:role}
        }).done(function(data){
            $("#create-item").find("input[name='id']").val('');
            $("#create-item").find("input[name='idStandard']").val('');
            $("#create-item").find("input[name='idType']").val('');
            $("#create-item").find("input[name='kind']").val('');
            $("#create-item").find("input[name='domain']").val('');
            $("#create-item").find("input[name='name']").val('');
            $("#create-item").find("input[name='shortDesc']").val('');
            $("#create-item").find("textarea[name='desc']").val('');
            $("#create-item").find("input[name='value']").val('');
            $("#create-item").find("input[name='size']").val('');
            $("#create-item").find("input[name='unit']").val('');
            $("#create-item").find("input[name='multiplicity']").val('');
            $("#create-item").find("textarea[name='setting']").val('');
            $("#create-item").find("input[name='role']").val('');
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
		url: url + 'api/delete_tab-parameter.php',
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
    var idStandard = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var idType = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var kind = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var domain = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var name = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var shortDesc = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var desc = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var value = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var size = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var unit = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var multiplicity = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var setting = $(this).parent("td").prev("td").prev("td").text();
    var role = $(this).parent("td").prev("td").text();

    $("#edit-item").find("input[name='idStandard']").val(idStandard);
    $("#edit-item").find("input[name='idType']").val(idType);
    $("#edit-item").find("input[name='kind']").val(kind);
    $("#edit-item").find("input[name='domain']").val(domain);
    $("#edit-item").find("input[name='name']").val(name);
    $("#edit-item").find("input[name='shortDesc']").val(shortDesc);
    $("#edit-item").find("textarea[name='desc']").val(desc);
    $("#edit-item").find("input[name='value']").val(value);
    $("#edit-item").find("input[name='size']").val(size);
    $("#edit-item").find("input[name='unit']").val(unit);
    $("#edit-item").find("input[name='multiplicity']").val(multiplicity);
    $("#edit-item").find("textarea[name='setting']").val(setting);
    $("#edit-item").find("input[name='role']").val(role);
    $("#edit-item").find(".edit-id").val(id);

});

/* Updated new Item */
$(".crud-submit-edit").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-item").find("form").attr("action");
    var idStandard = $("#edit-item").find("input[name='idStandard']").val();
    var idType = $("#edit-item").find("input[name='idType']").val();
    var kind = $("#edit-item").find("input[name='kind']").val();
    var domain = $("#edit-item").find("input[name='domain']").val();
    var name = $("#edit-item").find("input[name='name']").val();
    var shortDesc = $("#edit-item").find("input[name='shortDesc']").val();
    var desc = $("#edit-item").find("textarea[name='desc']").val();
    var value = $("#edit-item").find("input[name='value']").val();
    var size = $("#edit-item").find("input[name='size']").val();
    var unit = $("#edit-item").find("input[name='unit']").val();
    var multiplicity = $("#edit-item").find("input[name='multiplicity']").val();
    var setting = $("#edit-item").find("textarea[name='setting']").val();
    var role = $("#edit-item").find("input[name='role']").val();
    var id = $("#edit-item").find(".edit-id").val();

    if(id != '' && idStandard != '' && idType != '' && kind != '' && domain != '' &&
       name != '' && shortDesc != '' && desc != '' && value != '' && size != '' && 
       unit != '' &&  multiplicity != '' && setting != '' && role != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, idStandard:idStandard, idType:idType, kind:kind, domain:domain,
            name:name, shortDesc:shortDesc, desc:desc, value:value, size:size, unit:unit, 
            multiplicity:multiplicity, setting:setting, role:role}
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