// get info from URL
var idType = getUrlVars()["id"];
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

//var response = JSON.parse(
$.ajax({
  //url: 'https://randomuser.me/api/',
  //url: 'http://localhost/jsoneditor/json/randomuser.me-api.json',
  //url: 'http://localhost/jsoneditor/json/mib.json',
  //url: url+'api/getData_json-component-setting.php',
  url: url+'api/getData_json-type-setting.php?idType='+idType,
  dataType: 'json',
  success: function(data) {
    console.log(data);
    console.log(data.setting);
    if (data == "{}") {
        console.log("data is empty!");
        return "{}";
    } else {
        data = JSON.parse(data.setting);
        console.log(data);
        editor.setValue(data);
        return data;
    }
  }
});

var response2 = JSON.parse(
$.ajax({
  url: url+'api/getData_json-type-schema.php?idType='+idType,
  dataType: 'json',
  global: false,
  async: false,
  success: function(data) {
    data = JSON.parse(data.schema);
    console.log(data);
    return data;
  }
}).responseText);
var schema = JSON.parse(response2.schema);

// JSON-Editor
    //JSONEditor.defaults.theme = 'bootstrap3';
    JSONEditor.defaults.iconlib = 'fontawesome4';
    JSONEditor.defaults.options.theme = 'bootstrap3';
    //JSONEditor.defaults.options.schema = '{title: "setting",$ref: "#/definitions/setting",definitions: {setting: {type: "object",id: "setting",defaultProperties: ["Enumerations","PUS"],}}}';
    //var schema = '{title: "setting",$ref: "#/definitions/setting",definitions: {setting: {type: "object",id: "setting",defaultProperties: ["Enumerations","PUS"],}}}';
/*    var schema = {
  "type": "object",
  "title": "setting",
  "properties": {
    "Enumerations": {
      "type": "array",
      "title": "Enumerations",
      "uniqueItems": true,
      "items": {
                "type": "object",
                "properties": {
                    "Value": {"type":"string"},
                    "Name": { "type": "string" },
                    "Description" : { "type": "string" },
                    "Parameters" : { "type": "string" }
                }
      }
    },
    "PUS": {
      "type": "array",
      "title": "PUS"
    }
  }
};*/

/*var schema = 
{
    "title": "Event Reports",
    "type": "object",
    "properties": {
        "Enumerations" : {
            "type": "array",
            "format": "table",
            "uniqueItems": true,
            "items": {
                "type": "object",
                "properties": {
                    "Value": {"type":"string"},
                    "Name": { "type": "string" },
                    "Description" : {  "type": "string", "format":"textarea" } ,
                    "Parameters" : {  "type": "string"}         
                }
            }
        }
    }
};*/

console.log(schema);

    JSONEditor.plugins.sceditor.style = "//cdn.jsdelivr.net/sceditor/1.4.3/jquery.sceditor.default.min.css";

    if (document.getElementById('editor_holder_type') != null) {

    // Initialize the editor
    var editor = new JSONEditor(document.getElementById('editor_holder_type'),{
        // The schema for the editor, TODO: get schema from database
        schema: schema
        /*schema: {
            title: "setting",
            $ref: "#/definitions/setting",
            definitions: {
                setting: {
                    type: "object",
                    id: "setting",
                    // The object will start with only these properties
                    defaultProperties: [
                        "Enumerations",
                        "PUS"
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
        }*/
    });

    } else if (document.getElementById('editor_holder_type_schema') != null) {

    // Initialize the editor
    var editor = new JSONEditor(document.getElementById('editor_holder_type_schema'),{
        // The schema for the editor
        schema: schema
        /*schema: {
            title: "setting",
            $ref: "#/definitions/setting",
            definitions: {
                setting: {
                    type: "object",
                    id: "setting",
                    // The object will start with only these properties
                    defaultProperties: [
                        "prefix",
                        "author",
                        "copyright",
                        "param_attr",
                        "var_attr",
                        "max_line_length",
                        "indent",
                        "includes"
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
        }*/
    });

    } else {

    // Initialize the editor
    var editor_def = new JSONEditor(document.getElementById('editor_holder'),{
        // The schema for the editor
        schema: {
            title: "MIB", // was Person
            $ref: "#/definitions/person",
            definitions: {
                person: {
                    type: "object",
                    id: "person",
                    // The object will start with only these properties
                    defaultProperties: [
                        "fname",
                        "lname",
                        "bestFriend",
                        "coworkers"
                    ],
                    patternProperties: {
                      // Self-referntial schema in patternProperties
                      "^cousin_[0-9]+$": {
                        $ref: "#/definitions/person"
                      }
                    },
                    properties: {
                        fname: {
                            title: "first name",
                            type: "string"
                        },
                        lname: {
                            title: "last name",
                            type: "string"
                        },
                        bestFriend: {
                          title: "best friend",
                          oneOf: [
                            {
                              title: "none",
                              type: "null"
                            },
                            // Self-referential schema as 2nd choice in oneOf
                            {
                              title: "person",
                              $ref: "#/definitions/person"
                            }
                          ]
                        },
                        coworkers: {
                          type: "array",
                          // Self-referential schema in array items
                          items: {
                            title: "Coworker",
                            $ref: "#/definitions/person"
                          }
                        },
                        // Self-referential schemas in non-default properties
                        mother: {
                          title: "mother",
                          $ref: "#/definitions/person"
                        }
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
    
    }

    // Hook up the save button to log to the console
    document.getElementById('save').addEventListener('click',function() {
        // Get the value from the editor
        var id = '';
        var setting = editor.getValue();
        console.log(setting);
        var setting2 = JSON.stringify(setting);
        
        if(id == '' && setting != ''){
            
            url = '/dbeditor/api/update_view-type-setting.php';
            var posting = $.post( url, { setting: setting2, idStandard: idStandard, idType: idType } );
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