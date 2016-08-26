(function () {
    'use strict';
    var app = angular.module('loginService', []);

    app.factory('loginService', function ($http) {
        return {
            login: function (user) {
                var $promise = $http.post('api/login.php', user);

                $promise.then(function (msg) {
                    if (msg.data === 'success') {
                        console.log('success login');
                    } else {
                        console.log('error login');
                    }
                });
                return $promise;
            }
        }
    });
}());