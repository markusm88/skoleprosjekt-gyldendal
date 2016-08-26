(function(){  
	
	var app = angular.module('controllers', []);

	app.run(['$rootScope', '$location', '$http', function ($rootScope, $location, $http){
		$rootScope.$on("$routeChangeStart", function (event, next, current) {
			$http.get('api/session.php').success(function (data) {
				$rootScope.sessionInfo = data;
				$rootScope.userlvl = data.userlvl;
				$rootScope.grade = data.grade;
				if (data.loggedin == 'true') {
					var nextUrl = next.$$route.originalPath;
					if (nextUrl == '/registerStudent') {
						$location.path('/');
					}
					$rootScope.loggedIn = 'true';
				} else {
					$rootScope.loggedIn = 'false';
					var nextUrl = next.$$route.originalPath;
					var urls = ['/registerStudent'];
					if (urls.indexOf(nextUrl) > -1) {
					} else {
						$location.path("/");
					}
				}
			});
		});
		moment.locale("nb_NO");
	}]);

	// Controller for Main
	app.controller('mainController', function($scope, $http, $rootScope, logoutService, $location) {
		$scope.logOut = function(){
			logoutService.logout().then(function(){
				$http.get('api/session.php').success(function(data){
				$rootScope.sessionInfo = data;
				$rootScope.loggedIn = $rootScope.sessionInfo.loggedin;
				$rootScope.userlvl = $rootScope.sessionInfo.userlvl;
				$rootScope.grade = $rootScope.sessionInfo.grade;
				$location.path("/");
				});
			});
		};

		$rootScope.difficulty = {
			level: ''
		};
		
		function hideMobile(){
			if($(window).width() > 768){
				if($location.path().substring(0,7) == '/mobile'){
					$location.path("/home");
				};
			};
		};
		
		$(window).resize(function(){
			hideMobile();
			$scope.$apply();
		});
        
		hideMobile();
		
	});
	
	// Controller for Registrering
	app.controller('registerController', function($scope, $http, $location, loginService, $rootScope) {
		$scope.selectedSchool = '';
		$scope.currentyear = new Date().getFullYear();

		$http.get('api/database.php?request=getSchools').success(function(data){
				$scope.schoolList = data;
			});

		$scope.$watch('selectedSchool', function(selectedSchool) {
			$http.get('api/database.php?request=getClassesBySchoolId&id='+selectedSchool
			).success(function(data) {
				$scope.classList = data;
			});
		});
				
		$scope.regUser = function(register) {
			register.insert = 'student';
			register.schoolid = $scope.selectedSchool;
			register.classid = $scope.selectedClass;
			register.classyear = $scope.currentyear;
			
			if(!$scope.register.email){
				register.email = null;
			}else{
				register.email = $scope.register.email;
			};
			$http.post('api/database.php', register
			).success(function(data) {
				var user = {
					username: data.username,
					password: register.password
				};

				loginService.login(user).then(function(){
					$http.get('api/session.php').success(function(data){
						$rootScope.sessionInfo = data;
						$rootScope.loggedIn = $rootScope.sessionInfo.loggedin;
						$rootScope.userlvl = $rootScope.sessionInfo.userlvl;
						$rootScope.grade = $rootScope.sessionInfo.grade;
						if ($rootScope.loggedIn == 'true') {
							$location.path("/home");
						};
					});
				});
			});
		
		};
	});

	// Controller for Login
	app.controller('loginController', function($scope, $rootScope, loginService, $http, $location) {
		$http.get('api/session.php').success(function(data){
		if (data.loggedin == 'true') {
				$location.path("/home");
			};
		});
		
		$rootScope.loginUser = function(user) {
			loginService.login(user).then(function(msg){
				if(msg.data!='success'){
					$scope.loginError = true;
				};

				$http.get('api/session.php').success(function(data){
					$rootScope.sessionInfo = data;
					$rootScope.loggedIn = $rootScope.sessionInfo.loggedin;
					$rootScope.userlvl = $rootScope.sessionInfo.userlvl;
					$rootScope.grade = $rootScope.sessionInfo.grade;
					if ($rootScope.loggedIn == 'true') {
						$location.path("/home");
					};
				});
			})
		};
	});

	// Controller for Home (Logged in, all subjects)
	app.controller('homeController', function($scope, $rootScope, $http, $location) {
		$http.get('api/session.php').success(function(data){
			if (data.loggedin == 'false') {
				$location.path("/");
			};
		});
												
		$http.get('api/database.php?request=getSubjects').success(function(data){
			$scope.subjects = data;
		});
		
		
		$scope.selectedSubject = function(subjects) {
			var windowWidth = $(window).width();
			if(windowWidth >= 768){
				$location.path( '/home/' + subjects.name);  
			}else {
				$location.path( '/mobile/' + (parseInt(subjects.subjectsid) - 1));
			}
		};
	});

	// Controller for Subject
	app.controller('subjectCtrl', function($scope, $rootScope, $http, $location, $routeParams) {
		$http.get('api/database.php?request=getGamesBySubjectName&name='+ $routeParams.subjectsid
		).success(function(data){
			$scope.subjectGames = data;
		});

		$scope.subjectHeader = $routeParams.subjectsid;

		$scope.selectedGame = function(gameid) {
			$location.path( '/home/' + $routeParams.subjectsid + '/' + gameid);
		};
	});


	// Controller for Single game
	app.controller('gameCtrl', function($scope, $rootScope, $http, $location, $routeParams) {
		$http.get('api/database.php?request=getGameByGamecode&code='+ $routeParams.gameid
		).success(function(data){
			$scope.gameInfo = data;
		});
		
		$scope.gameClass = "onpageGame";
		
		$scope.goFullscreen = function(){
			if($scope.gameClass === "onpageGame"){
				$scope.gameClass = "lightboxGame";
				$scope.onFull = true;
			}else {
				$scope.gameClass = "onpageGame";
				$scope.onFull = false;
			}
		};
	});

	// Controller for Sidebar
	app.controller('sidebarCtrl', function($scope, $rootScope, $http, $location, $routeParams) {
		$scope.allClasses = function(n) {
			return new Array(n);
		};

		$scope.grades = [
			{
				value: 0,
				label:'Velg klasse'
			}
		];

		$http.get('api/session.php').success(function(data) {
			for (var i = 1; i <= 7; i++) {
				var grade = {
					value: i,
					label: i+'. klasse'
				}
				$scope.grades.push(grade);
			};
			
			if(data.userlvl > 1) {
				$scope.selectDifficulty = $scope.grades[0];
			}else{
				$scope.selectDifficulty = $scope.grades[parseInt(data.grade)];
			}

			// Get and show homework
			$http.get('api/database.php?request=getHomeworkByClassId&id='+$rootScope.sessionInfo.classid
			).success(function(data){
				$scope.getHomework = [];
				
				var today = new Date();            
				
				for(i = 0; i < data.length; i++){
					var homewordEnd = new Date(moment(data[i].enddate).format());
					if(homewordEnd >= today){
						$scope.getHomework.push(data[i]);
					}
				}
				
				for(i = 0; i < $scope.getHomework.length; i++){
					$scope.getHomework[i].enddate = moment(data[i].enddate).fromNow();
				}
			});
			
		});

		//Check for changes in class choser
		$scope.$watch('selectDifficulty', function(){
			if ($scope.selectDifficulty != undefined) {
				$rootScope.currDiff = $scope.selectDifficulty.value;
			}
		});

		$scope.$on('$routeChangeStart', function(event, args) {
			$rootScope.showMenu = false;
		});
		
		
	/*});

	// Controller for Header <--  Velger å ha en controller for header og sidebar. sidebarCtrl <-- 
	app.controller('headerCtrl', function($scope, $rootScope, $http, $location, $routeParams) {*/
		$scope.$watch('gamecodeInput', function(newCode){
			if(!isNaN(parseInt(newCode))){
				$http.get('api/database.php?request=getGameByGamecode&code='+newCode
				).success(function(data){
					$scope.gameCodeInfo = data;
				});	
			}else if(isNaN(parseInt(newCode))){};
			
			// Remove gameinfo on body click
			$(window).click(function(){
				$scope.gamecodeInput = "";
				$scope.$apply();
			});
		});

		$scope.toggleMenu = function() {
			$rootScope.showMenu = !$rootScope.showMenu;
		}
		
		$scope.backwards = function(){
			history.back(); 
		};

		$scope.toGame = function(gameCode){
            if(!isNaN(parseInt(gameCode))){
			$location.path( '/home/' + 'games' + '/' + gameCode);
			$scope.gamecodeInput = '';
            };
		};
		
		$scope.placeholder = "####";
	});



	// Controller for Home (Logged in, all subjects)
	app.controller('mobileCtrl', function($scope, $rootScope, $http, $routeParams, $location) {
		var subjectid = 0;
		if (!isNaN(parseInt($routeParams.subjectsid))) {
			subjectid = $routeParams.subjectsid;
		}

		var allGames = []
		$scope.subjectGames = [];
		$scope.subjects = [];
		$scope.subjectsLength = $scope.subjects.length;
		

		var swiper = new Swiper('.swiper-container', {
			pagination: '.swiper-pagination',
			paginationClickable: true
		});

		swiper.on('onSlideChangeEnd', function (swiper) {
			$scope.selectedSubject = $scope.subjects[swiper.activeIndex];
			$scope.$apply();
		});

		$scope.goToPrev = function goToPrev() {swiper.slidePrev(500);}
		$scope.goToNext = function goToNext() {swiper.slideNext(500);}

		var i = 1;

		$scope.$watch('selectedSubject', function(newSubject, oldSubject){
			if (newSubject != undefined) {
				if (i == 0) {
					swiper.slideTo(newSubject.subjectsid - 1, 500);
				} else {
					i--;
				}
			}
		});

		$http.get('api/database.php?request=getSubjects').success(function(data){
			$scope.subjects = data;
			$scope.selectedSubject = $scope.subjects[swiper.activeIndex];
			$http.get('api/database.php?request=getGames'
				).success(function(data){
					allGames = data;
					for (var i = 0; i < $scope.subjects.length; i++) {
					var subjectGame = [];
						for (var y = 0; y < allGames.length; y++) {
							if (allGames[y].subjectname == $scope.subjects[i].name) {
								subjectGame.push(allGames[y]);
							}
						};		
					$scope.subjectGames.push(subjectGame);
				};
			});
		});

		$scope.$on('ngRepeatFinished', function(ngRepeatFinishedEvent) {
			swiper.update();
			swiper.slideTo(subjectid, 500);
		});

		/*$http.get('api/database.php?request=getGamesBySubjectName&name='+ $routeParams.subjectsid
		).success(function(data){
			$scope.subjectGames = data;
		});*/
        
        $scope.selectedGame = function(gameid) {
			$location.path( '/home/' + $routeParams.subjectsid + '/' + gameid);
		};
	});

	// Filter for difficulty on games
	app.filter('diffFilter', function($rootScope){
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

})();