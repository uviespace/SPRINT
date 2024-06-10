$(document).ready(function(){

var idStandard = getUrlVars()["idStandard"];
var idCalibration = getUrlVars()["id"];
var showLine = getUrlVars()["showline"];
if (showLine == "false") { confShowLine = false; } else { confShowLine = true; }
var maxy = getUrlVars()["maxy"];
if (maxy == undefined || maxy == "") maxy = 100.0;
var deltax = getUrlVars()["deltax"];
if (deltax == undefined || deltax == "") deltax = 1.0;
imax = maxy / deltax;
console.log(imax); 

/* get variables from URL */
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

  $.ajax({
    url : url + "api/getData_json-calibration-setting.php?idStandard="+idStandard+"&idCalibration="+idCalibration,
    type : "GET",
    success : function(data){
      console.log("Hello from AJAX! Polynomial Calibration.");
      console.log(JSON.stringify(data["setting"]));
      json = JSON.parse(data["setting"]);
      console.log(json); 
      console.log(json.Pol1);
      console.log(json.Pol2);
      console.log(json.Pol3);
      console.log(json.Pol4);
      console.log(json.Pol5);
      if (json.Pol1 == undefined) {
          pol1 = 0.0;
      } else {
          pol1 = parseFloat(json.Pol1);
      }
      if (json.Pol2 == undefined) {
          pol2 = 0.0;
      } else {
          pol2 = parseFloat(json.Pol2);
      }
      if (json.Pol3 == undefined) {
          pol3 = 0.0;
      } else {
          pol3 = parseFloat(json.Pol3);
      }
      if (json.Pol4 == undefined) {
          pol4 = 0.0;
      } else {
          pol4 = parseFloat(json.Pol4);
      }
      
      if (json.Pol5 == undefined) {
          pol5 = 0.0;
      } else {
          pol5 = parseFloat(json.Pol5);
      }
      console.log(pol1);
      console.log(pol2);
      console.log(pol3);
      console.log(pol4);
      console.log(pol5);
      
      var xval = [];
      var yval = [];
      
      for (var i=0; i<imax; i++) {
        x = i*deltax;
        y = pol1 + pol2*x + pol3*Math.pow(x, 2) + pol4*Math.pow(x, 3) + pol5*Math.pow(x, 4);
        xval.push(x);
        yval.push(y);
      }

      var chartdata = {
        labels: xval,
        datasets: [
          {
            label: "Y Value",
#            fill: false,
            lineTension: 0.1,
            backgroundColor: "rgba(59, 89, 152, 0.75)",
            borderColor: "rgba(59, 89, 152, 1)",
            pointHoverBackgroundColor: "rgba(59, 89, 152, 1)",
            pointHoverBorderColor: "rgba(59, 89, 152, 1)",
            data: yval,
            showLine: confShowLine,
            pointRadius: 5,
            pointHoverRadius: 10
          }
        ]
      };

      var ctx = $("#mycanvas");

      var LineGraph = new Chart(ctx, {
        type: 'line',
        data: chartdata,
        options: {
            scales: {
                x: {
                  title: {
                    display: true,
                    text: 'ADU'
                  }
                },
                y: {
                  title: {
                    display: true,
                    text: 'Y Value'
                  }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Calibration Curve',
                    padding: {
                        top: 10,
                        bottom: 30
                    }
                },
                legend: {
                    display: false
                }
            }
        }
      });
    },
    error : function(data) {

    }
  });
});
