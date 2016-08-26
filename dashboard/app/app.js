(function(){
	var dashboard = angular.module('dashboard', ['controllers', 'directives', 'ngRoute', 'ngAnimate', 'ngToast', 'ngSanitize', 'angular-loading-bar', 'ui.date']);

	dashboard.config(function(cfpLoadingBarProvider) {
		cfpLoadingBarProvider.includeSpinner = false;
		cfpLoadingBarProvider.includeBar = true;
	})

	// configure our routes
	dashboard.config(function($routeProvider) {
		$routeProvider
			// route for the homework page
			.when('/', {
				templateUrl : 'views/homeworkList.html',
				controller  : 'homeworkCtrl'
			})
			.when('/add', {
				templateUrl : 'views/addHomework.html',
				controller  : 'addHomeworkCtrl'
			})
			.when('/edit/:id', {
				templateUrl : 'views/editHomework.html',
				controller  : 'editHomeworkCtrl'
			})
			// route for the students page
			.when('/students', {
				templateUrl : 'views/studentList.html',
				controller  : 'studentsCtrl'
			})
			// route for the students page
			.when('/student/edit/:username', {
				templateUrl : 'views/editStudent.html',
				controller  : 'editStudentCtrl'
			})
			// route for the students page
			.when('/student/add', {
				templateUrl : 'views/addStudent.html',
				controller  : 'addStudentCtrl'
			})
			.otherwise({redirectTo: '/'});
		// use the HTML5 History API
		//http://localhost/salaby-dev/
		//$locationProvider.html5Mode(true);
	});
	
})();
