/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/charts/roundChart.js":
/*!*******************************************!*\
  !*** ./resources/js/charts/roundChart.js ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

/* import Chart from 'chart.js'

$(document).ready(function () {
    $.ajax({
        url: "/charts/1",
        method: "GET",
        success: function (data) {
            Chart.defaults.global.defaultFontFamily = '"Open Sans",sans-serif';
            var counter = [];
            var punkte_1 = data.points[0];
            var punkte_2 = data.points[1];
            var punkte_3 = data.points[2];
            var punkte_4 = data.points[3];
            var punkte_5 = data.points[4];

            for (var i in data.points[0]) {
                counter.push(i);
            }

            if (true) {
                var chartdata = {
                    labels: counter,
                    datasets: [
                        {
                            label: data.names[0],
                            borderWidth: 1.8,
                            fill: false,
                            lineTension: 0.1,
                            borderColor: "#E37222",
                            backgroundColor: "#E37222",
                            data: punkte_1
                        },
                        {
                            label: data.names[1],
                            borderWidth: 1.8,
                            fill: false,
                            lineTension: 0.1,
                            borderColor: "#07889B",
                            backgroundColor: "#07889B",
                            data: punkte_2
                        },
                        {
                            label: data.names[2],
                            borderWidth: 1.8,
                            fill: false,
                            lineTension: 0.1,
                            borderColor: "#24980C",
                            backgroundColor: "#24980C",
                            data: punkte_3
                        },
                        {
                            label: data.names[3],
                            borderWidth: 1.8,
                            fill: false,
                            lineTension: 0.1,
                            borderColor: "#C70E00",
                            backgroundColor: "#C70E00",
                            data: punkte_4
                        },
                        {
                            label: data.names[4],
                            borderWidth: 1.8,
                            fill: false,
                            lineTension: 0.1,
                            borderColor: "#481620",
                            backgroundColor: "#481620",
                            data: punkte_5
                        }
                    ]
                };
            } else {
                var chartdata = {
                    labels: counter,
                    datasets: [
                        {
                            label: data[0].name_1,
                            borderWidth: 1.8,
                            fill: false,
                            lineTension: 0.1,
                            borderColor: "#E37222",
                            backgroundColor: "#E37222",
                            data: punkte_1
                        },
                        {
                            label: data[0].name_2,
                            borderWidth: 1.8,
                            fill: false,
                            lineTension: 0.1,
                            borderColor: "#07889B",
                            backgroundColor: "#07889B",
                            data: punkte_2
                        },
                        {
                            label: data[0].name_3,
                            borderWidth: 1.8,
                            fill: false,
                            lineTension: 0.1,
                            borderColor: "#24980C",
                            backgroundColor: "#24980C",
                            data: punkte_3
                        },
                        {
                            label: data[0].name_4,
                            borderWidth: 1.8,
                            fill: false,
                            lineTension: 0.1,
                            borderColor: "#C70E00",
                            backgroundColor: "#C70E00",
                            data: punkte_4
                        }
                    ]
                };
            }


            var ctx = $('#roundChart');

            var LineGraph = new Chart(ctx, {
                type: 'line',
                data: chartdata,
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            ticks: {
                                display: true
                            },
                            scaleLabel: {
                                display: true,
                                labelString: "Spiele",
                                lineHeight: 0.2
                            },
                            gridLines: {
                                drawBorder: false
                            }
                        }],
                        yAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: "Punkte",
                                lineHeight: 0.3
                            },
                            gridLines: {
                                drawBorder: false
                            }
                        }]
                    },
                    tooltips: {
                        //intersect: false
                    },
                    elements: {
                        point: {
                            radius: 0,
                            pointHitRadius: 10
                        }
                    },
                    legend: {
                        labels: {
                            boxWidth: 12
                        }
                    },
                }
            });


        },
        error: function (data) {
            console.log(data);
        },
        dataType: "json"
    });
});

 */

/***/ }),

/***/ 1:
/*!*************************************************!*\
  !*** multi ./resources/js/charts/roundChart.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\xampp\htdocs\EwigeListe\resources\js\charts\roundChart.js */"./resources/js/charts/roundChart.js");


/***/ })

/******/ });