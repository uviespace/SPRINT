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
          pol5 = json.Pol5;
      }
      console.log(pol1);
      console.log(pol2);
      console.log(pol3);
      console.log(pol4);
      console.log(pol5);
      
      var xval = [];
      var yval = [];
      
      for (var i=0; i<100; i++) {
        x = i*1.0;
        y = pol1 + pol2*x + pol3*Math.pow(2, x) + pol4*Math.pow(3, x) + pol5*Math.pow(4, x);
        xval.push(x);
        yval.push(y);
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