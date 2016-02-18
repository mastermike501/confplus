(function(){

	var app = angular.module('app', ['ngRoute', 'mainCtrl', 'cpService']);
	
	//configure your routes here
	app.config(function($routeProvider) {
	  	$routeProvider
	   	.when('/Book/:bookId', {
		    templateUrl: 'book.html',
		    controller: 'BookController',
		    resolve: {
		      // I will cause a 1 second delay
		      	delay: function($q, $timeout) {
			        var delay = $q.defer();
			        $timeout(delay.resolve, 1000);
			        return delay.promise;
		      	}
		    }
		})
	  	.when('/Book/:bookId/ch/:chapterId', {
		    templateUrl: 'chapter.html',
		    controller: 'ChapterController'
	 	});
	});

})();