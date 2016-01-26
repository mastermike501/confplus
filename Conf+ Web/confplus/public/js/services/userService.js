(function(){
    var service = angular.module('userService', []);

    // service.constant('API_URL', 'http://localhost/api/');

    var injectables = ['$http'];

    function serviceFunction($http) {

        return {
            // get all the users
            get: function() {
                return $http.get('api/' + 'users');
            },

            // save a comment (pass in comment data)
            save: function(userData) {
                return $http({
                    method: 'POST',
                    url: 'http://localhost:8000/api/' + 'users',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    data: $.param(userData)
                });
            }
        }

    }

    service.factory('userApi', injectables.concat([serviceFunction]));

})();