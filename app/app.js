(function(){
    var app = angular.module('salaby', ['controllers', 'directives', 'loginService', 'logoutService', 'ngRoute', 'ngAnimate']);

    // configure our routes
    app.config(function($routeProvider) {
        $routeProvider

            // route for the register page
            .when('/registerStudent', {
                templateUrl : 'views/registerStudent.html',
                controller  : 'registerController'
            })

            // route for the home page
            .when('/', {
                templateUrl : 'views/login.html',
                controller  : 'loginController'
            })

            // route for the logout
            .when('/logout', {
                template : ' ',
                controller  : 'logoutController'
            })

            // route for the home page
            .when('/home', {
                templateUrl : 'views/home.html',
                controller  : 'homeController'
            })

            // route for the subject page
            .when('/home/:subjectsid', {
                templateUrl : 'views/subject.html',
                controller  : 'subjectCtrl'
            })

            // route for the game page
            .when('/home/:subjectsid/:gameid', {
                templateUrl : 'views/game.html',
                controller  : 'gameCtrl'
            })
        
            // route for the game page
            .when('/home/oppgave/:gameid', {
                templateUrl : 'views/game.html',
                controller  : 'gameCtrl'
            })

            // route for the home page
            .when('/mobile/:subjectsid', {
                templateUrl : 'views/responsive.html',
                controller  : 'mobileCtrl'
            })

            // route for the home page
            .when('/mobile', {
                templateUrl : 'views/responsive.html',
                controller  : 'mobileCtrl'
            })

        
        // use the HTML5 History API
        //http://localhost/salaby-dev/
        //$locationProvider.html5Mode(true);
    });
    
})();
