'use strict';

//noinspection JSUnresolvedFunction
var mainApp = angular.module('mainApp', []);

mainApp.controller('loginController', function($scope, $http) {
    console.log($scope.password);

    $scope.password = '123';

    $http.post('/admin/check/', {
        '__username': $scope.username,
        '__password': $scope.password
    }).
    success(function(data) {
        $scope.greeting = data;
    });
});