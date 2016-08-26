(function(){
    'use strict';
    var app = angular.module('logoutService', []);

    app.factory('logoutService', function($http){
        return{
            logout:function(){
                var $promise = $http.get('api/logout.php');
                return $promise;
            }
        }
    });

}());