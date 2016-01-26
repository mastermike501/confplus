(function(){

	var ctrl = angular.module('mainCtrl', []);

	var injectables = ['$scope', 'userApi'];

	function ctrlFunction ($scope, userApi) {
		$scope.userData = {};

    	$scope.loading = true;

    	$scope.init = function() {
    		userApi.get().then(function(data) {
	            $scope.users = data.data;
	            $scope.loading = false;
	        });
    	};

        $scope.createUser = function() {
	        $scope.loading = true;

	        userApi.save($scope.userData)
	            .then(function(data) {
	                userApi.get()
	                    .then(function(getData) {
	                    	console.log(getData);
	                        $scope.users = getData.data;
	                        $scope.loading = false;
	                    });
	            }, function(err) {
	                console.log(err);
	            });
	    };

	    $scope.init();

	}

	ctrl.controller('mainController', injectables.concat([ctrlFunction]));

})();