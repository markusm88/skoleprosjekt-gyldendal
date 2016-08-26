(function(){  	
	var dashboard = angular.module('controllers', ['ngToast']);
	dashboard.run(['$rootScope', '$http', function($rootScope, $http){
		$rootScope.$on("$routeChangeStart", function (event, next, current) {
			$http.get('../api/session.php').success(function (data) {
				$rootScope.sessionInfo = data;
				$rootScope.userlvl = data.userlvl;
				$rootScope.grade = data.grade;
				if (data.loggedin == 'true' && data.userlvl > 1) {
					$rootScope.loggedIn = 'true';
				} else {
					$rootScope.loggedIn = 'false';
					window.location.replace("../");
				}
			});
		});
	}]);

	dashboard.config(['ngToastProvider', function(ngToast) {
		ngToast.configure({
			dismissOnTimeout: true,
			timeout: 6000,
			dismissButton: true,
			dismissButtonHtml: '&times;',
			dismissOnClick: true
		});
	}]);

	// Controller for Dashboard
	dashboard.controller('dashboardCtrl', function($scope, $rootScope, $http, $location) {

	});

	// Controller for Navigation
	dashboard.controller('navCtrl', function($scope, $rootScope, $http, $location) {
		$scope.isActive = function(route) {
			return route === $location.path();
		}
		
	});

	// Controller for Students list
	dashboard.controller('studentsCtrl', function($scope, $rootScope, $location, $http, ngToast) {
        $http.get('../api/session.php').success(function(data){
			$scope.userinfo = data;
		});
        
		$scope.classes = [
			{
				"classid":'',
				"classname":'Velg klasse'
			}
		];

		$http.get('../api/session.php').success(function(data){
			$http.get('../api/database.php?request=getClassesBySchoolId&id=' + data["schoolid"])
			.success(function(data){
				for (var i = 0; i < data.length; i++) {
					$scope.classes.push(data[i]);
				};
			});
			$http.get('../api/database.php?request=getUsersBySchoolId&id=' + $rootScope.sessionInfo["schoolid"])
			.success(function(data){
				for (var i = 0; i < data.length; i++) {
					data[i].fullName = data[i].name + ' ' + data[i].surname;
				};
				$scope.students = data;
			});
		});

		$scope.edit = function(username) {
			$location.path( '/student/edit/' + username );
		}

		$scope.add = function() {
			$location.path( '/student/add/' );
		}

		$scope.delete = function(userid, username) {
			if (confirm("Sikker på at du vil slette " + username + "?") == true) {
				var request = {"delete" : "student","id" : userid};
				$http
				.post('../api/database.php', request)
				.success(function(data){
					$http
					.get('../api/database.php?request=getUsersBySchoolId&id=' + $rootScope.sessionInfo["schoolid"])
					.success(function(data){
						$scope.students = data;
					});
				});
				ngToast.create({
					content: 'Bruker: ' + username + ' ble slettet',
					className: 'success'
				});
			} else {
				ngToast.create({
					content: 'Sletting ble avbrutt',
					className: 'warning'
				});
			}
		}

		$scope.selected = '';
		$scope.filterSearch = '';
		$scope.placeholder = 'Søk på elevnavn';
	});

	// Controller for edit student
	dashboard.controller('editStudentCtrl', function($scope, $rootScope, $location, $http, $routeParams, ngToast) {
		$scope.request = {};
		$scope.selected;

		var getUser = function() {
			$http
			.get('../api/database.php?request=getUserByUsername&username=' + $routeParams.username)
			.success(function(data){
				$scope.student = data[0];
				$scope.request.name = $scope.student.name;
				$scope.request.surname = $scope.student.surname;
				$scope.request.username = $scope.student.username;
				$scope.request.email = $scope.student.email;
				$scope.request.classyear = parseInt($scope.student.classyear);
				$scope.selected = $scope.student.classid;
			});
		};
		

		$http
		.get('../api/database.php?request=getClassesBySchoolId&id=' + $rootScope.sessionInfo["schoolid"])
		.success(function(data){
			$scope.classes = data;
		});

		$scope.edit = function(userid) {
			$scope.request.update = 'student';
			$scope.request.id = userid;
			$scope.request.classid = $scope.selected;
			$http
			.post('../api/database.php', $scope.request)
			.success(function(data){
				getUser();
				$location.path( '/students' );
				ngToast.create({
					content: 'Bruker oppdatert',
					className: 'success'
				});
			});
		}

		$scope.cancel = function() {
			$location.path( '/students' );
		}

		getUser();

	});

	// Controller for add student
	dashboard.controller('addStudentCtrl', function($scope, $rootScope, $location, $http, ngToast) {
		$http.get('../api/session.php').success(function(data){
			$http.get('../api/database.php?request=getClassesBySchoolId&id=' + $rootScope.sessionInfo["schoolid"])
			.success(function(data){
				$scope.classes = data;
			});
		});

		$scope.add = function() {
			$scope.request.insert = 'student';
			$scope.request.schoolid = $rootScope.sessionInfo.schoolid;
			$scope.request.classid = $scope.selected;
			$http
			.post('../api/database.php', $scope.request).
			success(function(data, status, headers, config) {
				ngToast.create({
					content: 'Bruker ble opprettet',
					className: 'success'
				});
				$location.path( '/students' );
			}).
			error(function(data, status, headers, config) {
			});
		}

		$scope.cancel = function() {
			$location.path( '/students' );
		}

	});

	// Controller for Homework list
	dashboard.controller('homeworkCtrl', function($scope, $rootScope, $location, $http, $filter, ngToast) {
        $http.get('../api/session.php').success(function(data){
			$scope.userinfo = data;
		});
		$scope.weeks = [];
		$scope.weeks.push("Vis alle")
		for (var i = 1; i <= 52; i++) {
			$scope.weeks.push(i);
		};
		$scope.selectedWeek = $scope.weeks[0];

		$scope.classes = [
			{
				"classid":'',
				"classname":'Velg klasse'
			}
		];

		function addWeek() {
			for (var i = 0; i < $scope.homeworks.length; i++) {
				$scope.homeworks[i].startweek = $filter('date')(new Date($scope.homeworks[i].startdate), 'w');
				$scope.homeworks[i].endweek = $filter('date')(new Date($scope.homeworks[i].enddate), 'w');
			};
		}

		$http.get('../api/session.php').success(function(data){
			$http.get('../api/database.php?request=getClassesBySchoolId&id=' + data["schoolid"])
			.success(function(data){
				for (var i = 0; i < data.length; i++) {
					$scope.classes.push(data[i]);
				};
			});
		});

		

		$scope.edit = function(id) {
			$location.path( '/edit/' + id );
		}

		$scope.add = function() {
			$location.path( '/add' );
		}

		$scope.delete = function(homework) {
			var msg = "Sikker på at du vil slette " + homework.title + "?";
			if (homework.teacher.teacherid != $rootScope.sessionInfo.teacherid) {
				msg = "OBS! Denne leksen ble opprettet av " + homework.teacher.name + " " + homework.teacher.surname + ". " + msg;
			}
			if (confirm(msg) == true) {
				var request = {"delete" : "homework","id" : homework.homeworkid};
				$http
				.post('../api/database.php', request)
				.success(function(data){
					$http
					.get('../api/database.php?request=getHomeworkByClassId&id=' + $scope.selected)
					.success(function(data){
						$scope.homeworks = data;
					});
				});
				ngToast.create({
					content: 'Lekse: ' + homework.title + ' ble slettet',
					className: 'success'
				});
			} else {
				ngToast.create({
					content: 'Sletting ble avbrutt',
					className: 'warning'
				});
			}
		}

		$scope.selected = '';
		$scope.$watch('selected', function(selected) {
			if (selected == '') {

			} else {
				$http.get('../api/database.php?request=getHomeworkByClassId&id='+selected)
				.success(function(data) {
					$scope.homeworks = data;
				});
			}
		});
	});
	

	// Controller for edit homework
	dashboard.controller('editHomeworkCtrl', function($scope, $rootScope, $location, $http, $routeParams, ngToast) {

		$scope.request = {};
		$scope.difficultyFilter = 0;
		$scope.subjectFilter = '';

		$scope.$watch("difficultyFilter", function(newVal, oldVal) {
			$rootScope.currDiff = newVal;
		});

		var getHomework = function() {
			$http
			.get('../api/database.php?request=getHomeworkById&id=' + $routeParams.id)
			.success(function(data){
				$scope.homework = data[0];
				$scope.request.title = $scope.homework.title;
				$scope.request.description = $scope.homework.description;
				$scope.request.gameid = $scope.homework.gameid;
				$scope.sd = new Date(moment($scope.homework.startdate).format());
				$scope.ed = new Date(moment($scope.homework.enddate).format());
				$scope.selected = $scope.homework.classid;
				$scope.subject = $scope.homework.subject;
			});
		};
		
		$http.get('../api/session.php').success(function(data){
			$http.get('../api/database.php?request=getClassesBySchoolId&id=' + data["schoolid"])
			.success(function(data){
				$scope.classes = data;
			});
			getHomework();
		});

		$scope.grades = [{value: 0,label:'Sorter etter klasse'}];
		$scope.subjects = [{subjectsid:0,name:'', label:'Sorter etter fag'}]

		for (var i = 1; i <= 7; i++) {
			var grade = {
				value: i,
				label: i+'. klasse'
			}
			$scope.grades.push(grade);
		};

		$http.get('../api/database.php?request=getSubjects')
		.success(function(data){
			for (var i = 0; i < data.length; i++) {
				data[i].label = data[i].name;
				$scope.subjects.push(data[i]);
			};
		});

		$http
		.get('../api/database.php?request=getGames')
		.success(function(data){
			$scope.games = data;
		});

		$scope.edit = function(homeworkid) {
			$scope.request.update = 'homework';
			$scope.request.homeworkid = homeworkid;
			$scope.request.subject = $scope.subject;
			$scope.request.startdate = moment(new Date($scope.sd)).format();
			$scope.request.enddate = moment(new Date($scope.ed)).format();
			$scope.request.classid = $scope.selected;
			$scope.request.teacherid = $rootScope.sessionInfo.teacherid;
			$http.post('../api/database.php', $scope.request)
			.success(function(data){
				getHomework();
				$location.path( '/' );
				ngToast.create({
					content: 'Lekse oppdatert',
					className: 'success'
				});
			});
		}

		$scope.cancel = function() {
			$location.path( '/students' );
		}


	});

	// Controller for add homework
	dashboard.controller('addHomeworkCtrl', function($scope, $rootScope, $location, $http, ngToast) {
		$scope.difficultyFilter = 0;
		$scope.subjectFilter = '';

		$scope.$watch("difficultyFilter", function(newVal, oldVal) {
			$rootScope.currDiff = newVal;
		});

		$http.get('../api/session.php').success(function(data){
			$http.get('../api/database.php?request=getClassesBySchoolId&id=' + data["schoolid"])
			.success(function(data){
				$scope.classes = data;
			});
		});

		$scope.grades = [{value: 0,label:'Sorter etter klasse'}];
		$scope.subjects = [{subjectsid:0,name:'', label:'Sorter etter fag'}]

		for (var i = 1; i <= 7; i++) {
			var grade = {
				value: i,
				label: i+'. klasse'
			}
			$scope.grades.push(grade);
		};

		$http.get('../api/database.php?request=getSubjects')
		.success(function(data){
			for (var i = 0; i < data.length; i++) {
				data[i].label = data[i].name;
				$scope.subjects.push(data[i]);
			};
		});


		$http
		.get('../api/database.php?request=getGames')
		.success(function(data){
			$scope.games = data;
		});

		$scope.add = function() {
			$scope.request.insert = 'homework';
			$scope.request.startdate = moment(new Date($scope.sd)).format();
			$scope.request.enddate = moment(new Date($scope.ed)).format();
			$scope.request.teacherid = $rootScope.sessionInfo.teacherid;
			$scope.request.classid = $scope.selected;
			$scope.request.subject = $scope.subject;
			$http.post('../api/database.php', $scope.request)
			.success(function(data){
				ngToast.create({
					content: 'Lekse ble opprettet',
					className: 'success'
				});
				$location.path( '/' );
			});
		}

		$scope.cancel = function() {
			$location.path( '/' );
		}

	});

	dashboard.filter('diffFilter', function($rootScope){
			return function(items) {
				if (items) {
				var filtered = [];
				for (var i = 0; i < items.length; i++) {
					if($rootScope.currDiff >= items[i].difficultyfrom && $rootScope.currDiff <= items[i].difficultyto){
						filtered.push(items[i]);
					};
					
					if($rootScope.currDiff == 0){
						filtered.push(items[i]);
					}
				};
				return filtered;
				};
			};
		});

	dashboard.filter('filterByWeek', function () {
		return function (items, week) {
		if (items) {
			if (!isNaN(week)) {
				var filtered = [];
				for (var i = 0; i < items.length; i++) {
					var item = items[i];
					if (item.startweek <= week && item.endweek >= week) {
						filtered.push(item);
					}
				}
				return filtered;
			} else {
				return items;
			}
			
		}
	};
});
})();