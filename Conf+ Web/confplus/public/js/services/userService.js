(function(){
    var service = angular.module('userService', []);

    // service.constant('API_URL', 'http://localhost/api/');

    var injectables = ['$http'];

    function serviceFunction($http) {

        var http = $http;

        function getUsers () {
            return http.get('api/' + 'users');
        }

        function saveUser(userData) {
            return http({
                method: 'POST',
                url: 'http://localhost:8000/api/' + 'users',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                data: $.param(userData)
            });
        }

        return {
            get: getUsers, // get all the users
            save: saveUser //save user
        }

    }

    service.factory('userApi', injectables.concat([serviceFunction]));

})();