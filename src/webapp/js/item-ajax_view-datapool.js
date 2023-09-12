$( document ).ready(function() {

var page = 1;
var current_page = 1;
var total_page = 0;
var is_ajax_fire = 0;
var dropdown = "";

var userrole = document.getElementById("user_role");

var idProject = getUrlVars()["idProject"];
var idStandard = getUrlVars()["idStandard"];
var idParameter = getUrlVars()["idParameter"];

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
		url: url+'api/getData_view-datapool.php?idStandard='+idStandard+'&idParameter='+idParameter,
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
		url: url+'api/getData_view-datapool.php?idStandard='+idStandard+'&idParameter='+idParameter+'&showAll=1',
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
		url: url+'api/getData_view-datapool.php?idStandard='+idStandard+'&idParameter='+idParameter,
		data: {page:page}
	}).done(function(data){
		manageRow(data.data);
        $("#result_nmb").val(data.total);
	});
}

/* Get Dropdown Data for Kind */
function getDropdownDataKind(kind) {
    var data = { 
        "data": [ 
            { "kind":"3", "name":"DpPar", "desc":"Datapool Parameter" }, 
            { "kind":"4", "name":"DpVar", "desc":"Datapool Variable" },
            { "kind":"5", "name":"DpPar Imp", "desc":"Datapool Parameter Imported" }, 
            { "kind":"6", "name":"DpVar Imp", "desc":"Datapool Variable Imported" }
        ]
    };
    manageOptionKind(data.data, kind);
}

/* Get Dropdown Data for Packet Parameter */
function getDropdownDataParameterDatatype(idType) {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-parameter-datatype.php?idStandard='+idStandard,
		data: {dropdown:dropdown}
	}).done(function(data){
		manageOptionParameterDatatype(data.data, idType);
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

/* Add new option to select */
function manageOptionParameterDatatype(data, idType) {
	$("#sel_datatype").empty();
    if (idType=='null') {
        $("#sel_datatype").append('<option value="" selected>None / None</option>');
    }
	$.each( data, function( key, value ) {
		if (idType==value.id) {
			$("#sel_datatype").append('<option value="'+value.id+'" selected>'+value.domain+' / '+value.name+' ('+value.id+')</option>');
		} else {
			$("#sel_datatype").append('<option value="'+value.id+'">'+value.domain+' / '+value.name+' ('+value.id+')</option>');
		}
	});
}

/* Get Dropdown Data for Kind */
function getDropdownDataKindCreate() {
    var data = { 
        "data": [ 
            { "kind":"", "name":"--- Please select ---", "desc":"" }, 
            { "kind":"3", "name":"DpPar", "desc":"Datapool Parameter" }, 
            { "kind":"4", "name":"DpVar", "desc":"Datapool Variable" },
            { "kind":"5", "name":"DpPar Imp", "desc":"Datapool Parameter Imported" }, 
            { "kind":"6", "name":"DpVar Imp", "desc":"Datapool Variable Imported" }
        ]
    };
    manageOptionKindCreate(data.data);
}

/* Get Dropdown Data for Packet Parameter */
function getDropdownDataParameterDatatypeCreate() {
	$.ajax({
		dataType: 'json',
		url: url+'api/getData_dd-parameter-datatype.php?idStandard='+idStandard,
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
	  	rows = rows + '<td>'+value.domain+'</td>';
	  	rows = rows + '<td>'+value.name+'</td>';
	  	rows = rows + '<td>'+value.shortDesc+'</td>';
	  	rows = rows + '<td>'+value.kind+'</td>';
	  	rows = rows + '<td>'+value.idType+'</td>';
	  	rows = rows + '<td>'+value.multiplicity+'</td>';
	  	rows = rows + '<td class="td-hover-break">'+value.value+'</td>';
	  	rows = rows + '<td>'+value.unit+'</td>';
	  	rows = rows + '<td data-id="'+value.id+'">';
        if (userrole.value < 4) {
        rows = rows + '<button data-toggle="modal" data-target="#edit-item" class="btn btn-primary edit-item">Edit</button> ';
        if (value.multiplicity > 1) {
            rows = rows + '<button data-toggle="modal" data-target="#edit-values" class="btn btn-primary edit-values">Values</button> ';
        }
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
    //var id = $("#create-item").find("input[name='id']").val();
    var domain = $("#create-item").find("input[name='domain']").val();
    var name = $("#create-item").find("input[name='name']").val();
    if (!idStandard.length) {
      var kind = $("#create-item").find("input[name='kind']").val();
    } else {
      var kind = $("#create-item").find("select[name='kind']").val();
    }
    var shortDesc = $("#create-item").find("input[name='shortDesc']").val();
    if (!idStandard.length) {
      var idType = $("#create-item").find("input[name='idType']").val();
    } else {
      var idType = $("#create-item").find("select[name='idType']").val();
    }
    var multiplicity = $("#create-item").find("input[name='multiplicity']").val();
    var value = $("#create-item").find("input[name='value']").val();
    var unit = $("#create-item").find("input[name='unit']").val();

    //toastr.success('Create Entered. Stanbdard = ' + idStandard, 'Success Alert', {timeOut: 5000});

    if(multiplicity=='0') {
		toastr.error('Multiplicity should not be zero!', 'Failure Alert', {timeOut: 5000});
	} else {

    if(idStandard != '' && domain != '' && name != '' && kind != '' && idType != '' && value != ''){
        toastr.success('Checked necessary items.', 'Success Alert', {timeOut: 5000});
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{idStandard:idStandard, domain:domain, name:name, kind:kind, 
            shortDesc:shortDesc,idType:idType, multiplicity:multiplicity, value:value,
            unit:unit},
            success: function(results, textStatus) {
                toastr.success('Database Operation Successfully. ' + results, 'Success Alert', {timeOut: 5000});
            },
            error: function(xhr, status, error)
            {
                toastr.error('Database Operation Failed. ' + xhr.responseText, 'Failure Alert', {timeOut: 5000});
            }
        }).done(function(data){
            //$("#create-item").find("input[name='id']").val('');
            $("#create-item").find("input[name='domain']").val('');
            $("#create-item").find("input[name='name']").val('');
            if (!idStandard.length) {
              $("#create-item").find("input[name='kind']").val('');
            } else {
              $("#create-item").find("select[name='kind']").val('');
            }
            $("#create-item").find("input[name='shortDesc']").val('');
            if (!idStandard.length) {
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
        }).fail(function(xhr, err) { 
            toastr.error('Failure. ' + xhr.responseText, 'Failure Alert', {timeOut: 5000});
        }).always(function() { 
            alert("complete"); 
        });
    }else{
        alert('You are missing name or another mandatory item.')
    }
	
	}

});

/* Remove Item */
$("body").on("click",".remove-item",function(){
	var id = $(this).parent("td").data('id');
	var c_obj = $(this).parents("tr");
	$.ajax({
		dataType: 'json',
		type:'POST',
		url: url + 'api/delete_view-datapool.php',
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
    var shortDesc = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var kind = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var idType = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var multiplicity = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var value = $(this).parent("td").prev("td").prev("td").text();
    var unit = $(this).parent("td").prev("td").text();

    getDropdownDataKind(kind);
    getDropdownDataParameterDatatype(idType);

    $("#edit-item").find("input[name='domain']").val(domain);
    $("#edit-item").find("input[name='name']").val(name);
    if (!idStandard.length) {
      $("#edit-item").find("input[name='kind']").val(kind);
    } else {
      $("#edit-item").find("select[name='kind']").val(kind);
    }
    $("#edit-item").find("input[name='shortDesc']").val(shortDesc);
    if (!idStandard.length) {
      $("#edit-item").find("input[name='idType']").val(idType);
    } else {
      $("#edit-item").find("select[name='idType']").val(idType);
    }
    $("#edit-item").find("input[name='multiplicity']").val(multiplicity);
    $("#edit-item").find("input[name='value']").val(value);
    $("#edit-item").find("input[name='unit']").val(unit);
    $("#edit-item").find(".edit-id").val(id);

});

/* Updated new Item */
$(".crud-submit-edit").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-item").find("form").attr("action");
    var domain = $("#edit-item").find("input[name='domain']").val();
    var name = $("#edit-item").find("input[name='name']").val();
    if (!idStandard.length) {
      var kind = $("#edit-item").find("input[name='kind']").val();
    } else {
      var kind = $("#edit-item").find("select[name='kind']").val();
    }
    var shortDesc = $("#edit-item").find("input[name='shortDesc']").val();
    if (!idStandard.length) {
      var idType = $("#edit-item").find("input[name='idType']").val();
    } else {
      var idType = $("#edit-item").find("select[name='idType']").val();
    }
    var multiplicity = $("#edit-item").find("input[name='multiplicity']").val();
    var value = $("#edit-item").find("input[name='value']").val();
    var unit = $("#edit-item").find("input[name='unit']").val();
    var id = $("#edit-item").find(".edit-id").val();

    if(multiplicity=='0') {
		toastr.error('Multiplicity should not be zero!', 'Failure Alert', {timeOut: 5000});
	} else {

    if(id != '' && domain != '' && name != '' && kind != '' && idType != '' && value != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, domain:domain, name:name, kind:kind, shortDesc:shortDesc,
            idType:idType, multiplicity:multiplicity, value:value, unit:unit}
        }).done(function(data){
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Item Updated Successfully.', 'Success Alert', {timeOut: 5000});
        });
    }else{
        alert('You are missing something.')
    }
	
	}

});

/* Edit Values */
$("body").on("click",".edit-values",function(){

    var id = $(this).parent("td").data('id');
    var domain = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var name = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var shortDesc = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var kind = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").prev("td").text();
    var idType = $(this).parent("td").prev("td").prev("td").prev("td").prev("td").text();
    var multiplicity = $(this).parent("td").prev("td").prev("td").prev("td").text();
    var value = $(this).parent("td").prev("td").prev("td").text();
    var unit = $(this).parent("td").prev("td").text();
        
    //var dat = "mult = "+multiplicity+" / value = "+value+"";
    //$('#dat').text('dat: ' + dat );
        
    $.ajax({
        type: 'POST',
        url: url + 'api/getData_parameter-mult-values.php?idStandard='+idStandard,
        data: {mult:multiplicity ,value:value},
        success: function(response){
            //$('#dat').append(response);
            //$('#response').append(response);
            //document.getElementById('response').insertAdjacentHTML('afterend', response);
            document.getElementById('response').innerHTML = response;
            //toastr.success('Data Transfered Successfully.', 'Success Alert', {timeOut: 5000});
        }
    }).done(function(data){
        console.log("DONE");
        $("#edit-values").find("input[name='domain-val']").val(domain);
        $("#edit-values").find("input[name='domain-val']").width( ($('#domain-val_id').val().length) + "ch" ); 
        $("#edit-values").find("input[name='name-val']").val(name);
        $("#edit-values").find("input[name='name-val']").width( ($('#name-val_id').val().length) + "ch" ); 
        $("#edit-values").find("input[name='multiplicity-val']").val(multiplicity);
        $("#edit-values").find("input[name='multiplicity-val']").width( ($('#multiplicity-val_id').val().length) + "ch" );
        $("#edit-values").find("input[name='value-val']").val(value);
        $("#edit-values").find(".edit-id").val(id);
    });
    
    
    //window.location = '?idProject='+idProject+'&idStandard='+idStandard+'&mult=' + multiplicity; //redirect page with para ,here do redirect current page so not added file name .    
 
 /*
     $.ajax({
        type: 'post',
        data: {ajax: 1,name: name},
        success: function(response){
           $('#response').text('name: ' + response );
        }
     });
     */
    
    /*
    $.ajax({
      type: "POST",
      data: { mult: multiplicity },
      success: function(data) {
        $("#test").html(data);
        toastr.success('Data Transfered Successfully.', 'Success Alert', {timeOut: 5000});
      }
    });
    */
    
    /*
    var userdata = {'mult':multiplicity,'value':value};
    console.log(url + "view_datapool.php?idProject="+idProject+"&idStandard="+idStandard);
    $.ajax({
        type: "POST",
        //url: url + "view_datapool.php?idProject="+idProject+"&idStandard="+idStandard,
        data:userdata,
        success: function(data){
            //console.log(data);
            toastr.success('Data Transfered Successfully.', 'Success Alert', {timeOut: 5000});
        }
    }).done(function(data){
        console.log("DONE");
        $("#edit-values").find("input[name='multiplicity-val']").val(multiplicity);
        $("#edit-values").find("input[name='value-val']").val(value);
        $("#edit-values").find(".edit-id").val(id);
    });
    */
    
    /* Cookies only work for one shot */
    /*
    var date = new Date();
    var seconds = 15;
    date.setTime(date.getTime()+(seconds*1000));
    var expires = "; expires="+date.toGMTString();
    
    document.cookie = "name="+name;
    document.cookie = "mult"+name+"="+multiplicity;
    document.cookie = "value"+name+"="+value;
    
    //document.cookie = "name="+name+expires;
    //document.cookie = "mult_"+name+"="+multiplicity+expires;
    //document.cookie = "value_"+name+"="+value+expires;
    
    console.log("name="+name+expires);
    console.log("mult_"+name+"="+multiplicity+expires);
    console.log("value_"+name+"="+value+expires);
    
    const values = value.split(",");
    if (values.length > 1) {
        values[0] = values[0].substring(1);
        values[values.length-1] = values[values.length-1].substring(0,values[values.length-1].length-1);
    }
    console.log("values: "+values);
    
    $("#edit-values").find("input[name='domain-val']").val(domain);
    $("#edit-values").find("input[name='name-val']").val(name);
    $("#edit-values").find("input[name='multiplicity-val']").val(multiplicity);
    $("#edit-values").find("input[name='value-val']").val(value);
    
    for (let i = 0; i < values.length; i++) {
        $("#edit-values").find("input[name='values["+i+"]']").val(values[i]);
    }

    
    //$("#edit-values").find("input[name='values[]']").val(values);
    $("#edit-values").find(".edit-id").val(id);
    */

});

/* Updated new Item */
$(".crud-submit-edit-values").click(function(e){

    e.preventDefault();
    var form_action = $("#edit-values").find("form").attr("action");
    var valueVal = $("#edit-values").find("input[name='value-val']").val();
    var values = $("#edit-values").find("input[name='values[]']").map(function(){return $(this).val();}).get();
    var id = $("#edit-values").find(".edit-id").val();
    
    console.log("valueVal: "+valueVal);
    values = "{"+values+"}";
    console.log("values: "+values);

    if(id != '' && values != ''){
        $.ajax({
            dataType: 'json',
            type:'POST',
            url: url + form_action,
            data:{id:id, values:values}
        }).done(function(data){
            getPageData();
            $(".modal").modal('hide');
            toastr.success('Item Updated Successfully.', 'Success Alert', {timeOut: 5000});
            //location.reload();
            // Refresh the page after a delay of 3 seconds
            /*setTimeout(function(){
                location.reload();
            }, 5000);*/ // 5000 milliseconds = 5 seconds
            /*toastr.success(
              'Have fun!',
              'Miracle Max Says',
              {
                timeOut: 1000,
                fadeOut: 1000,
                onHidden: function () {
                    window.location.reload(true);  // true: lädt Seite nicht aus dem Cache
                  }
              }
            );*/
        });
    } else {
        alert('You are missing something.')
    }

});

});