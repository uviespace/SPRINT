// get info from URL
var idComponent = getUrlVars()["id"];
var idProject = getUrlVars()["idProject"];
var idApplication = getUrlVars()["idApplication"];

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
  url: url+'api/getData_json-component-setting.php?idComponent='+idComponent+'&idApplication='+idApplication,
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

    // ICD Generator           (idComponent = 1; icd)
    // Datapool                (idComponent = 2; dp)
    // Specification           (idComponent = 3; spec)
    // MIB Generator           (idComponent = 4; mib)
    // Packet access functions (idComponent = 5; pck)
    // CordetFw                (idComponent = 6; cfw)
    // Datapool v2             (idComponent = 7; dp2)
    
    // ICD Generator           (idComponent = 1; icd)
    if (document.getElementById('editor_holder_1') != null) {

    // Initialize the editor
    var editor = new JSONEditor(document.getElementById('editor_holder_1'),{
        // The schema for the editor
        schema: {
            title: "setting",
            $ref: "#/definitions/setting",
            definitions: {
                setting: {
                    type: "object",
                    id: "setting",
                    // The object will start with only these properties
                    defaultProperties: [
                        "LaTeX",
                        "CSV"
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

    // Datapool                (idComponent = 2; dp)
    } else if (document.getElementById('editor_holder_2') != null) {

    // Initialize the editor
    var editor = new JSONEditor(document.getElementById('editor_holder_2'),{
        // The schema for the editor
        schema: {
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
        }
    });

    // Specification           (idComponent = 3; spec)
    } else if (document.getElementById('editor_holder_3') != null) {

    // Initialize the editor
    var editor = new JSONEditor(document.getElementById('editor_holder_3'),{
        // The schema for the editor
        schema: {
            title: "setting",
            $ref: "#/definitions/setting",
            definitions: {
                setting: {
                    type: "object",
                    id: "setting",
                    // The object will start with only these properties
                    defaultProperties: [
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

    // MIB Generator           (idComponent = 4; mib)
    } else if (document.getElementById('editor_holder_4') != null) {

    // Initialize the editor
    var editor = new JSONEditor(document.getElementById('editor_holder_4'),{
        // The schema for the editor
        schema: {
            title: "setting",
            $ref: "#/definitions/setting",
            definitions: {
                setting: {
                    type: "object",
                    id: "setting",
                    // The object will start with only these properties
                    defaultProperties: [
                        "general",
                        "txf",
                        "paf",
                        "pcf",
                        "pcpc",
                        "ccf",
                        "cpc",
                        "pid",
                        "prf"
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

    // Packet access functions (idComponent = 5; pck)
    } else if (document.getElementById('editor_holder_5') != null) {

    // Initialize the editor
    var editor = new JSONEditor(document.getElementById('editor_holder_5'),{
        // The schema for the editor
        schema: {
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
                        "max_line_length",
                        "indent",
                        "struct_attr",
                        "endian",
                        "crc_size",
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
        }
    });

    // CordetFw                (idComponent = 6; cfw)
    } else if (document.getElementById('editor_holder_6') != null) {

    // Initialize the editor
    var editor = new JSONEditor(document.getElementById('editor_holder_6'),{
        // The schema for the editor
        schema: {
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
                        "max_line_length",
                        "indent",
                        "CrFwOutFactoryMaxNOfOutCmp",
                        "CrFwInFactoryMaxNOfInCmd",
                        "CrFwInFactoryMaxNOfInRep",
                        "CrFwOutRegistryN",
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
        }
    });

    // Datapool v2             (idComponent = 7; dp2)
    } else if (document.getElementById('editor_holder_7') != null) {

    // Initialize the editor
    var editor = new JSONEditor(document.getElementById('editor_holder_7'),{
        // The schema for the editor
        schema: {
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
        
        if(id == '' && setting != ''){
            
            url = '/dbeditor/api/update_view-component-setting.php';
            var posting = $.post( url, { setting: setting2, idApplication: idApplication, idComponent: idComponent } );
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