/**
 * GLOBAL FUNCTIONS
 */

/* get role with maximal access level */
function get_role(projectid, callback) {
    $.get('session-role.php', {'var':'role', 'idProject':projectid}, function(data) {
       callback(data);
    }, 'json');
}

/* get variables from URL */
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}
