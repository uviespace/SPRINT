$(document).ready(function(){

var idStandard = getUrlVars()["idStandard"];
var idCalibration = getUrlVars()["id"];

/* get variables from URL */
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

  $.ajax({
    url : "http://localhost/dbeditor/api/getData_json-calibration-setting.php?idStandard="+idStandard+"&idCalibration="+idCalibration,
    type : "GET",
    success : function(data){
      console.log("Hello from AJAX! Numerical Calibration.");
      console.log(JSON.stringify(data["setting"]));
      json = JSON.parse(data["setting"]);
      console.log(json); 
      console.log(json["engfmt"]);
      values = json["values"];
      console.log(values);
      console.log(values[0].xval);
      unit = json["unit"];
      console.log(unit);
      if (unit == "degC") {
          yText = 'Temperature in °C';
      } else if (unit == "V" || unit == "VOLT") {
          yText = 'Voltage in V';
      } else if (unit == "mV") {
          yText = 'Voltage in mV';
      } else if (unit == "A" || unit == "AMP") {
          yText = 'Current in A';
      } else if (unit == "mA") {
          yText = 'Current in mA';
      } else {
          yText = 'Y Value';
      }

      var xval = [];
      var yval = [];

      for(var i in values) {
        xval.push(values[i].xval);
        yval.push(values[i].yval);
      }

      var chartdata = {
        labels: xval,
        datasets: [
          {
            label: "Y Values",
            fill: false,
            lineTension: 0.1,
            backgroundColor: "rgba(59, 89, 152, 0.75)",
            borderColor: "rgba(59, 89, 152, 1)",
            pointHoverBackgroundColor: "rgba(59, 89, 152, 1)",
            pointHoverBorderColor: "rgba(59, 89, 152, 1)",
            data: yval
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
                    text: yText
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