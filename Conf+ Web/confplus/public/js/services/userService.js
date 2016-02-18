(function(){
    var service = angular.module('cpService', []);

    // service.constant('API_URL', 'http://localhost/api/');

    var injectables = ['$http'];

    function serviceFunction($http) {

        var http = $http;
        var url = 'api/users';

        function getUsers () {
            return http.get(url);
        }

        function saveUser(userData) {
            return http({
                method: 'POST',
                url: url,
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

    service.factory('cpApi', injectables.concat([serviceFunction]));

})();