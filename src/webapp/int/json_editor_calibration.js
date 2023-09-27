// get info from URL
var idCalibration = getUrlVars()["id"];
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
  url: url+'api/getData_json-calibration-setting.php?idCalibration='+idCalibration+'&idStandard='+idStandard,
  dataType: 'json',
  success: function(data) {
    data = JSON.parse(data.setting);
    console.log(data);
    if (typeof editor != 'undefined') {
      editor.setValue(data)
    }
  }
});


// JSON-Editor
    JSONEditor.defaults.theme = 'bootstrap3';
    JSONEditor.defaults.iconlib = 'fontawesome4';
    JSONEditor.plugins.sceditor.style = "//cdn.jsdelivr.net/sceditor/1.4.3/jquery.sceditor.default.min.css";

    // Numerical Calibration Curve (create new one)
    if (document.getElementById('editor_holder_num_new') != null) {

    // Initialize the editor
    var editor = new JSONEditor(document.getElementById('editor_holder_num_new'),{
        // The schema for the editor
        schema: {
            title: "Numerical Calibration Curve",
            $ref: "#/definitions/setting",
            definitions: {
                setting: { 
                    type: "object",
                    id: "setting",
                    // The object will start with only these properties
                    defaultProperties: [
                        "engfmt",
                        "rawfmt"
                    ],
                    properties: {
                        "engfmt" : {
                            "type": "string",
                            "title": "Engineering Value Format",
                        },
                        "rawfmt" : {
                            "type": "string",
                            "title": "Raw Value Format"
                        },
                        "radix" : {
                            "type": "string",
                            "title": "Radix"
                        },
                        "unit" : {
                            "type": "string",
                            "title": "Unit"
                        },
                        "ncurve" : {
                            "type": "string",
                            "title": "NCurve"
                        },
                        "inter" : {
                            "type": "string",
                            "title": "Interpolation"
                        },
                        "values" : {
                            "type": "array",
                            "title": "Values",
                            "format": "table",
                            "uniqueItems": true,
                            "items": {
                                "type": "object",
                                "properties": {
                                    "xval": { "type":"string" },
                                    "yval" : { "type": "string" }   
                                }
                            }
                        }
                    }
                }
            }
        }
    });        
                
    // Numerical Calibration Curve
    } else if (document.getElementById('editor_holder_num') != null) {

    // Initialize the editor
    var editor = new JSONEditor(document.getElementById('editor_holder_num'),{
        // The schema for the editor
        schema: {
            title: "Numerical Calibration Curve",
            $ref: "#/definitions/setting",
            definitions: {
                setting: {
                    type: "object",
                    id: "setting",
                    // The object will start with only these properties
                    defaultProperties: [
                        //"LaTeX",
                        //"CSV"
                    ],
                    patternProperties: {
                      // Self-referntial schema in patternProperties
                      "^cousin_[0-9]+$": {
                        $ref: "#/definitions/setting"
                      }
                    },
                    properties: {
                        "engfmt" : {
                            "type": "string",
                            "title": "Engineering Value Format",
                        },
                        "rawfmt" : {
                            "type": "string",
                            "title": "Raw Value Format"
                        },
                        "radix" : {
                            "type": "string",
                            "title": "Radix"
                        },
                        "unit" : {
                            "type": "string",
                            "title": "Unit"
                        },
                        "ncurve" : {
                            "type": "string",
                            "title": "NCurve"
                        },
                        "inter" : {
                            "type": "string",
                            "title": "Interpolation"
                        },
                        "values" : {
                            "type": "array",
                            "title": "Values",
                            "format": "table",
                            "uniqueItems": true,
                            "items": {
                                "type": "object",
                                "properties": {
                                    "xval": { "type":"string" },
                                    "yval" : { "type": "string" }   
                                }
                            }
                        }
                    }
                }
            }
        }
    });

    // Polynomial Calibration Curve (create new one)
    } else if (document.getElementById('editor_holder_pol_new') != null) {

    // Initialize the editor
    var editor = new JSONEditor(document.getElementById('editor_holder_pol_new'),{
        // The schema for the editor
        schema: {
            title: "Polynomial Calibration Curve",
            $ref: "#/definitions/setting",
            definitions: {
                setting: { 
                    type: "object",
                    id: "setting",
                    // The object will start with only these properties
                    defaultProperties: [
                        "Pol1",
                        "Pol2",
                        "Pol3",
                        "Pol4",
                        "Pol5"
                    ],
                    properties: {
                        "Pol1" : {
                            "type": "string",
                            "title": "Polynomial Coefficient 1",
                        },
                        "Pol2" : {
                            "type": "string",
                            "title": "Polynomial Coefficient 2",
                        },
                        "Pol3" : {
                            "type": "string",
                            "title": "Polynomial Coefficient 3",
                        },
                        "Pol4" : {
                            "type": "string",
                            "title": "Polynomial Coefficient 4",
                        },
                        "Pol5" : {
                            "type": "string",
                            "title": "Polynomial Coefficient 5",
                        }
                    }
                }
            }
        }
    });        
                
    // Polynomial Calibration Curve
    } else if (document.getElementById('editor_holder_pol') != null) {

    // Initialize the editor
    var editor = new JSONEditor(document.getElementById('editor_holder_pol'),{
        // The schema for the editor
        schema: {
            title: "Polynomial Calibration Curve",
            $ref: "#/definitions/setting",
            definitions: {
                setting: {
                    type: "object",
                    id: "setting",
                    // The object will start with only these properties
                    defaultProperties: [
                        //"LaTeX",
                        //"CSV"
                    ],
                    patternProperties: {
                      // Self-referntial schema in patternProperties
                      "^cousin_[0-9]+$": {
                        $ref: "#/definitions/setting"
                      }
                    },
                    properties: {
                        "Pol1" : {
                            "type": "string",
                            "title": "Polynomial Coefficient 1",
                        },
                        "Pol2" : {
                            "type": "string",
                            "title": "Polynomial Coefficient 2",
                        },
                        "Pol3" : {
                            "type": "string",
                            "title": "Polynomial Coefficient 3",
                        },
                        "Pol4" : {
                            "type": "string",
                            "title": "Polynomial Coefficient 4",
                        },
                        "Pol5" : {
                            "type": "string",
                            "title": "Polynomial Coefficient 5",
                        }
                    }
                }
            }
        }
    });

    // Logarithmic Calibration Curve (create new one)
    } else if (document.getElementById('editor_holder_log_new') != null) {

    // Initialize the editor
    var editor = new JSONEditor(document.getElementById('editor_holder_log_new'),{
        // The schema for the editor
        schema: {
            title: "Logarithmic Calibration Curve",
            $ref: "#/definitions/setting",
            definitions: {
                setting: { 
                    type: "object",
                    id: "setting",
                    // The object will start with only these properties
                    defaultProperties: [
                        "Log1",
                        "Log2",
                        "Log3",
                        "Log4",
                        "Log5"
                    ],
                    properties: {
                        "Log1" : {
                            "type": "string",
                            "title": "Logarithmic Coefficient 1",
                        },
                        "Log2" : {
                            "type": "string",
                            "title": "Logarithmic Coefficient 2",
                        },
                        "Log3" : {
                            "type": "string",
                            "title": "Logarithmic Coefficient 3",
                        },
                        "Log4" : {
                            "type": "string",
                            "title": "Logarithmic Coefficient 4",
                        },
                        "Log5" : {
                            "type": "string",
                            "title": "Logarithmic Coefficient 5",
                        }
                    }
                }
            }
        }
    });        
                
    // Logarithmic Calibration Curve
    } else if (document.getElementById('editor_holder_log') != null) {

    // Initialize the editor
    var editor = new JSONEditor(document.getElementById('editor_holder_log'),{
        // The schema for the editor
        schema: {
            title: "Logarithmic Calibration Curve",
            $ref: "#/definitions/setting",
            definitions: {
                setting: {
                    type: "object",
                    id: "setting",
                    // The object will start with only these properties
                    defaultProperties: [
                        //"LaTeX",
                        //"CSV"
                    ],
                    patternProperties: {
                      // Self-referntial schema in patternProperties
                      "^cousin_[0-9]+$": {
                        $ref: "#/definitions/setting"
                      }
                    },
                    properties: {
                        "Log1" : {
                            "type": "string",
                            "title": "Logarithmic Coefficient 1",
                        },
                        "Log2" : {
                            "type": "string",
                            "title": "Logarithmic Coefficient 2",
                        },
                        "Log3" : {
                            "type": "string",
                            "title": "Logarithmic Coefficient 3",
                        },
                        "Log4" : {
                            "type": "string",
                            "title": "Logarithmic Coefficient 4",
                        },
                        "Log5" : {
                            "type": "string",
                            "title": "Logarithmic Coefficient 5",
                        }
                    }
                }
            }
        }
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
        
        var type = document.getElementById('type').value;
        console.log("type: "+type);
        var name = document.getElementById('name').value;
        console.log("name: "+name);
        var shortDesc = document.getElementById('shortDesc').value;
        console.log("shortDesc: "+shortDesc);
        
        if(id == '' && setting != ''){
            
            url = '/dbeditor/api/update_view-calibration-setting.php';
            //url = '/SPRINT/api/update_view-calibration-setting.php';
            var posting = $.post( url, { type:type, name:name, shortDesc:shortDesc, setting: setting2, idStandard: idStandard, idCalibration: idCalibration } );
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