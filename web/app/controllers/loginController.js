//noinspection JSUnresolvedFunction
var mainApp = angular.module('mainApp', []);

mainApp.controller('loginController', function($scope, $http) {
    $http.get('/api/list/').
    success(function(data) {
        $scope.greeting = data;
    });
});