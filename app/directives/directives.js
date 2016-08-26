(function(){  
    
    var app = angular.module('directives', []);
    
    app.directive("mainNav", function(){
        return {
            restrict: "E",
            templateUrl: "app/directives/elements/nav.html",
            controller: function(){},
            controllerAs: "nav"
        };
    });
    
    app.directive("mobileNav", function(){
        return {
            restrict: "E",
            templateUrl: "app/directives/elements/mobilenav.html",
            controller: function(){},
            controllerAs: "mobilenav"
        };
    });

    app.directive("headerBar", function(){
        return {
            restrict: "E",
            templateUrl: "app/directives/elements/header-bar.html",
            controller: function(){},
            controllerAs: "headerbar"
        };
    });

    app.directive("mobileHeaderBar", function(){
        return {
            restrict: "E",
            templateUrl: "app/directives/elements/mobile-header-bar.html",
            controller: function(){},
            controllerAs: "mobileheaderbar"
        };
    });

    app.directive('onFinishRender', function ($timeout) {
    return {
        restrict: 'A',
        link: function (scope, element, attr) {
            if (scope.$last === true) {
                $timeout(function () {
                    scope.$emit('ngRepeatFinished');
                });
            }
        }
    }
    });
    
})();