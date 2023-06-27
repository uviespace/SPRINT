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
      console.log("Hello from AJAX! Logarithmic Calibration.");
      console.log(JSON.stringify(data["setting"]));
      json = JSON.parse(data["setting"]);
      console.log(json); 
      console.log(json.Log1);
      console.log(json.Log2);
      console.log(json.Log3);
      console.log(json.Log4);
      console.log(json.Log5);
      if (json.Log1 == undefined) {
          log1 = 0.0;
      } else {
          log1 = parseFloat(json.Log1);
      }
      if (json.Log2 == undefined) {
          log2 = 0.0;
      } else {
          log2 = parseFloat(json.Log2);
      }
      if (json.Log3 == undefined) {
          log3 = 0.0;
      } else {
          log3 = parseFloat(json.Log3);
      }
      if (json.Log4 == undefined) {
          log4 = 0.0;
      } else {
          log4 = parseFloat(json.Log4);
      }
      
      if (json.Log5 == undefined) {
          log5 = 0.0;
      } else {
          log5 = json.Log5;
      }
      console.log(log1);
      console.log(log2);
      console.log(log3);
      console.log(log4);
      console.log(log5);

      var xval = [];
      var yval = [];
      
      for (var i=0; i<100; i++) {
        x = i*1.0;
        y = 1/(log1 + log2*Math.log(x) + log3*Math.pow(2, Math.log(x)) + log4*Math.pow(3, Math.log(x)) + log5*Math.pow(4, Math.log(x)));
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