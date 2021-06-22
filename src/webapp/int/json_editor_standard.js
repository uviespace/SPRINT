// get info from URL
var idProject = getUrlVars()["idProject"];
var idStandard = getUrlVars()["idStandard"];

/* get variables from URL */
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

// find elements
var banner = $("#banner-message")
var button = $("button")

// handle click and add class
/*button.on("click", function(){
  banner.addClass("alt")
})*/


$(function(){

// https://randomuser.me/
$.ajax({
  //url: 'https://randomuser.me/api/',
  //url: 'http://localhost/jsoneditor/json/randomuser.me-api.json',
  //url: 'http://localhost/jsoneditor/json/mib.json',
  //url: url+'api/getData_json-component-setting.php',
  url: url+'api/getData_json-standard-setting.php?idStandard='+idStandard+'&idProject='+idProject,
  dataType: 'json',
  success: function(data) {
    data = JSON.parse(data.setting);
    console.log(data);
    editor.setValue(data)
  }
});


// JSON-Editor
    JSONEditor.defaults.theme = 'bootstrap3';
    JSONEditor.defaults.iconlib = 'fontawesome4';
    JSONEditor.plugins.sceditor.style = "//cdn.jsdelivr.net/sceditor/1.4.3/jquery.sceditor.default.min.css";

    // Initialize the editor
    var editor = new JSONEditor(document.getElementById('editor_holder'),{
        // The schema for the editor
        schema: {
            title: "setting",
            $ref: "#/definitions/setting",
            definitions: {
                setting: {
                    type: "object",
                    id: "person",
                    // The object will start with only these properties
                    defaultProperties: [
                        "datapool"
                    ],
                    patternProperties: {
                      // Self-referntial schema in patternProperties
                      "^cousin_[0-9]+$": {
                        $ref: "#/definitions/setting"
                      }
                    },
                    properties: {
                    }
                },
                year: {
                    type: "integer",
                    pattern: "^[0-9]{4}$",
                    minimum: 1900,
                    maximum: 2100
                }
            }
        }
    });

    // Hook up the save button to log to the console
    document.getElementById('save').addEventListener('click',function() {
        // Get the value from the editor
        var id = '';
        var setting = editor.getValue();
        console.log(setting);
        var setting2 = JSON.stringify(setting);
        
        if(id == '' && setting != ''){
            
            url = '/dbeditor/api/update_view-standard-setting.php';
            var posting = $.post( url, { setting: setting2, idStandard: idStandard, idProject: idProject } );
            //alert('You are sending ...: (posting = '+posting+')');
            
            //getPageData();
            //$(".modal").modal('hide');
            toastr.success('Item Updated Successfully.', 'Success Alert', {timeOut: 5000});
            location.reload();
            
            /*$.ajax({
                dataType: 'json',
                type:'POST',
                url: url + '/api/update_view-component-setting.php',
                data:{id:id, name:name, desc:desc, owner:owner, isPublic:isPublic, setting:setting}
            }).done(function(data){
                getPageData();
                $(".modal").modal('hide');
                toastr.success('Item Updated Successfully.', 'Success Alert', {timeOut: 5000});
            });*/
        }else{
            alert('You are missing something: (id = '+id+', setting = '+setting2+')')
        }
        
    });

    // Hook up the submit button to log to the console
    document.getElementById('submit').addEventListener('click',function() {
        // Get the value from the editor
        console.log(editor.getValue());
    });

    // Hook up the Restore to Default button
    document.getElementById('restore').addEventListener('click',function() {
        editor.setValue(starting_value);
    });

    // Hook up the enable/disable button
    document.getElementById('enable_disable').addEventListener('click',function() {
        // Enable form
        if(!editor.isEnabled()) {
            editor.enable();
        }
        // Disable form
        else {
            editor.disable();
        }
    });

    // Hook up the validation indicator to update its
    // status whenever the editor changes
    editor.on('change',function() {
        // Get an array of errors from the validator
        var errors = editor.validate();

        var indicator = document.getElementById('valid_indicator');

        // Not valid
        if(errors.length) {
            indicator.className = 'label label-danger'
            indicator.textContent = "not valid";
        }
        // Valid
        else {
            indicator.className = 'label label-success'
            indicator.textContent = "valid";
        }
    });
    
});