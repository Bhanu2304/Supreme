/**
* mainRoutes Module;
*
* Description
*/
var app = angular.module('mainRoutes', ['ngRoute']);

app.config(function ($routeProvider) {
	$routeProvider.when('/', {
		controller: 'mainController',
		templateUrl: '../public/views/main.php'
	}).when('/about', {
		templateUrl: '../public/views/home/about.html'
	}).otherwise({
		redirectTo: '/'
	});
});