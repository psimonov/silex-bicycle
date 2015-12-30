'use strict';

var loginApp = angular.module('loginApp', []);

loginApp.controller('sendController', function($scope, $http) {
    $scope.check = function () {
        $http.post('/admin/check/', {
            '__username': $scope.username,
            '__password': $scope.password
        }).
        success(function(data) {
            $scope.greeting = data;
        });
    };
});
