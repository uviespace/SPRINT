$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;

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
	  	rows = rows + '<td>'+value.value+'</td>';
	  	rows = rows + '<td>'+value.desc+'</td>';
	  	rows = rows + '<td>'+value.setting+'</td>';
	  	rows = rows + '<td data-id="'+value.id+'">';
        rows = rows + '<button data-toggle="modal" data-target="#edit-item" class="btn btn-primary edit-item">Edit</button> ';
        rows = rows + '<button class="btn btn-danger remove-item">Delete</button> ';
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
    var domain = $("#create-item").find("input[name='domain']").val();
    var name = $("#create-item").find("input[name='name']").val();
    var nativeType = $("#create-item").find("input[name='nativeType']").val();
    var size = $("#create-item").find("input[name='size']").val();
    var value = $("#create-item").find("input[name='value']").val();
    var desc = $("#create-item").find("textarea[name='desc']").val();
    var setting = $("#create-item").find("textarea[name='setting']").val();

    if(id != '' && domain != '' && name != '' && nativeType != '' && 
       size != '' && value != '' && desc != '' && setting != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, domain:domain, name:name, nativeType:nativeType, 
            size:size, value:value, desc:desc, setting:setting}
        }).done(function(data){
            $("#create-item").find("input[name='id']").val('');
            $("#create-item").find("input[name='domain']").val('');
            $("#create-item").find("input[name='name']").val('');
            $("#create-item").find("input[name='nativeType']").val('');
            $("#create-item").find("input[name='size']").val('');
            $("#create-item").find("input[name='value']").val('');
            $("#create-item").find("textarea[name='desc']").val('');
            $("#create-item").find("textarea[name='setting']").val('');
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
    var domain = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var name = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var nativeType = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var size = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var value = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var desc = $(this).parent("td").prev("td").prev("td").text();
    var setting = $(this).parent("td").prev("td").text();

    $("#edit-item").find("input[name='domain']").val(domain);
    $("#edit-item").find("input[name='name']").val(name);
    $("#edit-item").find("input[name='nativeType']").val(nativeType);
    $("#edit-item").find("input[name='size']").val(size);
    $("#edit-item").find("input[name='value']").val(value);
    $("#edit-item").find("textarea[name='desc']").val(desc);
    $("#edit-item").find("textarea[name='setting']").val(setting);
    $("#edit-item").find(".edit-id").val(id);

});

/* Updated new Item */
$(".crud-submit-edit").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-item").find("form").attr("action");
    var domain = $("#edit-item").find("input[name='domain']").val();
    var name = $("#edit-item").find("input[name='name']").val();
    var nativeType = $("#edit-item").find("input[name='nativeType']").val();
    var size = $("#edit-item").find("input[name='size']").val();
    var value = $("#edit-item").find("input[name='value']").val();
    var desc = $("#edit-item").find("textarea[name='desc']").val();
    var setting = $("#edit-item").find("textarea[name='setting']").val();
    var id = $("#edit-item").find(".edit-id").val();

    if(id != '' && domain != '' && name != '' && nativeType != '' && 
       size != '' && value != '' && desc != '' && setting != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, domain:domain, name:name, nativeType:nativeType, 
            size:size, value:value, desc:desc, setting:setting}
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