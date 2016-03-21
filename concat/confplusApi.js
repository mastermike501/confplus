(function(){
    var service = angular.module('confplus-api', [
        'user-services',
        'user-tag-services',
        'paper-services',
        'event-services',
        'session-services',
        'venue-services',
        'room-services',
        'resource-services'
    ]);

    var injectables = [
        '$http',
        '$q',
        'userServices',
        'userTagServices',
        'paperServices',
        'eventServices',
        'sessionServices',
        'venueServices',
        'roomServices',
        'resourceServices'
    ];

    function serviceFunction(
        $http,
        $q,
        userServices,
        userTagServices,
        paperServices,
        eventServices,
        sessionServices,
        venueServices,
        roomServices,
        resourceServices
    ) {
        var http = $http;

        /**
         * Creates a new user with params and tags
         * @param  {Object} userParams
         * @param  {Array} userTags
         * @return {Promise}
         */
        function createUser(userParams, userTags) {
            var deferred = $q.defer();

            //create the new user
            userServices.create(userParams).then(function(userResponse) {
                //if user creation succeeds, add tags for the user
                var params = {
                    email: userParams['email'],
                    tag_name: userTags
                };
                userTagServices.create(params).then(function(userTagResponse) {
                    deferred.resolve({
                        success: true,
                        data: {
                            userResponse:       userResponse,
                            userTagResponse:    userTagResponse
                        }
                    });
                }, function(err) {
                    console.log('confplusApi.js', 'createUser', 'userTagServices', err);
                    deferred.reject({
                        success: false,
                        error: err
                    });
                });
            }, function(err) {
                console.log('confplusApi.js', 'createUser', 'userServices', err);
                deferred.reject({
                    success: false,
                    error: err
                });
            });

            return deferred.promise;
        }

        return {
            createUser:     createUser
        }

    }

    service.factory('confplusApi', injectables.concat([serviceFunction]));

})();
(function(){
    var service = angular.module('event-services', ['helper-services']);

    var injectables = ['$http', 'helperServices'];

    function serviceFunction($http, helperServices) {

        var http = $http;
        var url = 'api/events';

        /**
         * Gets event using the params.event_id key.
         * @param  {Object} params
         * @param  {String|number} params.event_id
         * @return {Promise|void}
         */
        function get(params) {
			var requiredFields = ['event_id'];

            if (helperServices.fieldCheck(params, requiredFields)) {
                return http.get([url, helperServices.parameterize(params)].join('?'));
            }
        }

		/**
		 * For param keys to be recognized in the database, please refer to the
		 * technical manual for the names of the fields and reuse those names.
		 * @param  {Object} params
		 * @param  {String} params.name
		 * @param  {String} params.type
		 * @param  {String|Date} params.from_date
		 * @param  {String|Date} params.to_date
		 * @param  {String} params.description
		 * @return {Promise|void}
		 */
        function create(params) {
            var requiredFields = ['name', 'type', 'from_date', 'to_date', 'description'];

            //save user only if fields are valid
            if(helperServices.fieldCheck(params, requiredFields)) {
                return http.post(url, params);
                // return http({
                //     method: 'POST',
                //     url: url,
                //     headers: {
                //         'Content-Type': 'application/x-www-form-urlencoded'
                //     },
                //     data: params
                // });
            }
        }

        return {
            get:    get,
            create: create
        }
    }

    service.factory('eventServices', injectables.concat([serviceFunction]));

})();
(function(){
    var service = angular.module('helper-services', []);

    var injectables = [''];

    function serviceFunction() {

		/**
		 * Checks whether the params object contains non-empty or non-valid
		 * keys based on the given requiredFields.
		 * @param  {Object} params                 [Must be a flat object]
		 * @param  {array[string]} requiredFields  [An array of string field names]
		 * @return {Boolean}                       [true if required fields exist]
		 */
		function fieldCheck(params, requiredFields) {
			var areParamsValid = true;

            angular.forEach(requiredFields, function(fieldName) {
                //if field name does not exist, display warning
                if(!params[fieldName] || params[fieldName] == '') {
                    areParamsValid = false;
                    console.log('"' + fieldName + '" field is required.');
                }
            });

			return areParamsValid;
		}

		/**
		 * Creates a URI compliant string that is appendable to GET requests.
		 * @param  {Object} params [Must be a flat object]
		 * @return {string}        [URI compliant string]
		 */
		function parameterize(params) {
			var urlParams = [];
			for (var key in params) {
				var kvPair = [key, '=', encodeURIComponent(String(params[key]))].join('');
				urlParams.push(kvPair);
			}

			return urlParams.join('&');
		}

        return {
            fieldCheck:		fieldCheck,
			parameterize:	parameterize
        }
    }

    service.factory('helperServices', injectables.concat([serviceFunction]));

})();
(function(){
    var service = angular.module('paper-services', ['helper-services']);

    var injectables = ['$http', 'helperServices'];

    function serviceFunction($http, helperServices) {

        var http = $http;
        var url = 'api/papers';

        /**
         * Gets paper using the params.title and params.publish_date key.
         * @param  {Object} params
         * @param  {String} params.title
         * @param  {String|Date} params.publish_date
         * @return {Promise|void}
         */
        function get(params) {
			var requiredFields = ['title', 'publish_date'];

            if (helperServices.fieldCheck(params, requiredFields)) {
                return http.get([url, helperServices.parameterize(params)].join('?'));
            }
        }

		/**
		 * For param keys to be recognized in the database, please refer to the
		 * technical manual for the names of the fields and reuse those names.
		 * @param  {Object} params
		 * @param  {String} params.title
		 * @param  {String|Date} params.publish_date
		 * @param  {String|Date} params.latest_sub_date
		 * @param  {String} params.status
		 * @return {Promise|void}
		 */
        function create(params) {
            var requiredFields = ['title', 'publish_date', 'latest_sub_date', 'status'];

            //save user only if fields are valid
            if(helperServices.fieldCheck(params, requiredFields)) {
                return http.post(url, params);
                // return http({
                //     method: 'POST',
                //     url: url,
                //     headers: {
                //         'Content-Type': 'application/x-www-form-urlencoded'
                //     },
                //     data: params
                // });
            }
        }

        return {
            get:    get,
            create: create
        }
    }

    service.factory('paperServices', injectables.concat([serviceFunction]));

})();
(function(){
    var service = angular.module('resource-services', ['helper-services']);

    var injectables = ['$http', 'helperServices'];

    function serviceFunction($http, helperServices) {

        var http = $http;
        var url = 'api/resources';

        /**
         * Gets user using the params.venue_id, params.room_name and params.name keys.
         * @param  {Object} params
         * @param  {String|number} params.venue_id
         * @param  {String} params.room_name
         * @param  {String} params.name
         * @return {Promise|void}
         */
        function get(params) {
            var requiredFields = ['venue_id', 'room_name', 'name'];

            if (helperServices.fieldCheck(params, requiredFields)) {
                return http.get([url, helperServices.parameterize(params)].join('?'));
            }
        }

		/**
		 * For param keys to be recognized in the database, please refer to the
		 * technical manual for the names of the fields and reuse those names.
		 * @param  {Object} params
		 * @param  {String|number} params.venue_id
         * @param  {String} params.room_name
         * @param  {String} params.name
         * @param  {String} params.type
         * @param  {String|number} params.number
		 * @return {Promise|void}
		 */
        function create(params) {
            var requiredFields = ['venue_id', 'room_name', 'name', 'type', 'number'];

            //save user only if fields are valid
            if(helperServices.fieldCheck(params, requiredFields)) {
                return http.post(url, params);
                // return http({
                //     method: 'POST',
                //     url: url,
                //     headers: {
                //         'Content-Type': 'application/x-www-form-urlencoded'
                //     },
                //     data: params
                // });
            }
        }

        return {
            get:    get,
            create: create
        }
    }

    service.factory('resourceServices', injectables.concat([serviceFunction]));

})();
(function(){
    var service = angular.module('room-services', ['helper-services']);

    var injectables = ['$http', 'helperServices'];

    function serviceFunction($http, helperServices) {

        var http = $http;
        var url = 'api/rooms';

        /**
         * Gets user using the params.venue_id and params.name keys.
         * @param  {Object} params
         * @param  {String|number} params.venue_id
         * @param  {String} params.name
         * @return {Promise|void}
         */
        function get(params) {
            var requiredFields = ['venue_id', 'name'];

            if (helperServices.fieldCheck(params, requiredFields)) {
                return http.get([url, helperServices.parameterize(params)].join('?'));
            }
        }

		/**
		 * For param keys to be recognized in the database, please refer to the
		 * technical manual for the names of the fields and reuse those names.
		 * @param  {Object} params
		 * @param  {String|number} params.venue_id
         * @param  {String} params.name
         * @param  {String} params.type
         * @param  {String|number} params.capacity
		 * @return {Promise|void}
		 */
        function create(params) {
            var requiredFields = ['venue_id', 'name', 'type', 'capacity'];

            //save user only if fields are valid
            if(helperServices.fieldCheck(params, requiredFields)) {
                return http.post(url, params);
                // return http({
                //     method: 'POST',
                //     url: url,
                //     headers: {
                //         'Content-Type': 'application/x-www-form-urlencoded'
                //     },
                //     data: params
                // });
            }
        }

        return {
            get:    get,
            create: create
        }
    }

    service.factory('roomServices', injectables.concat([serviceFunction]));

})();
(function(){
    var service = angular.module('session-services', ['helper-services']);

    var injectables = ['$http', 'helperServices'];

    function serviceFunction($http, helperServices) {

        var http = $http;
        var url = 'api/sessions';

        /**
         * Gets session using the params.event_id, params.title and params.speaker_email keys.
         * @param  {Object} params
         * @param  {String|number} params.event_id
         * @param  {String} params.title
         * @param  {String} params.speaker_email
         * @return {Promise|void}
         */
        function get(params) {
			var requiredFields = ['event_id', 'title', 'speaker_email'];

            if (helperServices.fieldCheck(params, requiredFields)) {
                return http.get([url, helperServices.parameterize(params)].join('?'));
            }
        }

		/**
		 * For param keys to be recognized in the database, please refer to the
		 * technical manual for the names of the fields and reuse those names.
		 * @param  {Object} params
		 * @param  {String|number} params.event_id
		 * @param  {String} params.title
		 * @param  {String} params.speaker_email
		 * @param  {String|Date} params.start_time
		 * @param  {String|Date} params.end_time
		 * @return {Promise|void}
		 */
        function create(params) {
            var requiredFields = ['event_id', 'title', 'speaker_email', 'start_time', 'end_time'];

            //save user only if fields are valid
            if(helperServices.fieldCheck(params, requiredFields)) {
                return http.post(url, params);
                // return http({
                //     method: 'POST',
                //     url: url,
                //     headers: {
                //         'Content-Type': 'application/x-www-form-urlencoded'
                //     },
                //     data: params
                // });
            }
        }

        return {
            get:    get,
            create: create
        }
    }

    service.factory('sessionServices', injectables.concat([serviceFunction]));

})();
(function(){
    var service = angular.module('user-services', ['helper-services']);

    var injectables = ['$http', 'helperServices'];

    function serviceFunction($http, helperServices) {

        var http = $http;
        var url = 'api/users';

        /**
         * Gets user using the params.email key.
         * @param  {Object} params
         * @param  {String} params.email
         * @return {Promise|void}
         */
        function get(params) {
            var requiredFields = ['email'];

            if (helperServices.fieldCheck(params, requiredFields)) {
                return http.get([url, helperServices.parameterize(params)].join('?'));
            }
        }

		/**
		 * For param keys to be recognized in the database, please refer to the
		 * technical manual for the names of the fields and reuse those names.
		 * @param  {Object} params
		 * @param  {String} params.email
		 * @param  {String} params.username
		 * @param  {String} params.password
		 * @return {Promise|void}
		 */
        function create(params) {
            var requiredFields = ['email', 'username', 'password'];

            //save user only if fields are valid
            if(helperServices.fieldCheck(params, requiredFields)) {
                return http.post(url, params);
                // return http({
                //     method: 'POST',
                //     url: url,
                //     headers: {
                //         'Content-Type': 'application/x-www-form-urlencoded'
                //     },
                //     data: params
                // });
            }
        }

        /**
         * Updates the user based on the given email
         * @param  {Object} params
         * @param  {string} params.email
         * @param  {*} params.* Any number of attributes to update
         * @return {Promise|void}
         */
        function update(params) {
            var requiredFields = ['email'];

            if (helperServices.fieldCheck(params, requiredFields)) {
                return http.put(url, params);
            }
        }

        return {
            get:    get,
            create: create,
            update: update
        }
    }

    service.factory('userServices', injectables.concat([serviceFunction]));

})();
(function(){
    var service = angular.module('user-tag-services', ['helper-services']);

    var injectables = ['$http', 'helperServices'];

    function serviceFunction($http, helperServices) {

        var http = $http;
        var url = 'api/user_tag';

        /**
         * Gets user using the params.email key.
         * @param  {Object} params
         * @param  {String} params.email
         * @return {Promise|void}
         */
        // function get(params) {
        //     var requiredFields = ['email'];
		//
        //     if (helperServices.fieldCheck(params, requiredFields)) {
        //         return http.get([url, helperServices.parameterize(params)].join('?'));
        //     }
        // }

		/**
		 * For param keys to be recognized in the database, please refer to the
		 * technical manual for the names of the fields and reuse those names.
		 * @param  {Object} params
		 * @param  {String} params.email
		 * @param  {Array[String]} params.tag_name [An array of tags]
		 * @return {Promise|void}
		 */
        function create(params) {
            var requiredFields = ['email', 'tag_name'];

            //save user only if fields are valid
            if(helperServices.fieldCheck(params, requiredFields)) {
				//check if tag_name key is an array
				if(Array.isArray(params['tag_name'])) {
					return http.post(url, params);
				} else {
					console.log('"tag_name" field must be an array');
				}
                // return http({
                //     method: 'POST',
                //     url: url,
                //     headers: {
                //         'Content-Type': 'application/x-www-form-urlencoded'
                //     },
                //     data: params
                // });
            }
        }

        /**
         * Updates the user based on the given email
         * @param  {Object} params
         * @param  {string} params.email
         * @param  {*} params.* Any number of attributes to update
         * @return {Promise|void}
         */
        function update(params) {
            var requiredFields = ['email'];

            if (helperServices.fieldCheck(params, requiredFields)) {
                return http.put(url, params);
            }
        }

        return {
            get:    get,
            create: create,
            update: update
        }
    }

    service.factory('userTagServices', injectables.concat([serviceFunction]));

})();
(function(){
    var service = angular.module('venue-services', ['helper-services']);

    var injectables = ['$http', 'helperServices'];

    function serviceFunction($http, helperServices) {

        var http = $http;
        var url = 'api/venues';

        /**
         * Gets venue using the params.venue_id key.
         * @param  {Object} params
         * @param  {String|number} params.venue_id
         * @return {Promise|void}
         */
        function get(params) {
			var requiredFields = ['venue_id'];

            if (helperServices.fieldCheck(params, requiredFields)) {
                return http.get([url, helperServices.parameterize(params)].join('?'));
            }
        }

		/**
		 * For param keys to be recognized in the database, please refer to the
		 * technical manual for the names of the fields and reuse those names.
		 * @param  {Object} params
		 * @param  {String} params.name
		 * @param  {String} params.type
		 * @param  {String|Boolean} params.has_room
		 * @param  {String} params.street
		 * @param  {String} params.city
		 * @param  {String} params.state
		 * @param  {String} params.country
		 * @param  {String|number} params.longitude
		 * @param  {String|number} params.latitude
		 * @return {Promise|void}
		 */
        function create(params) {
            var requiredFields = ['name', 'type', 'has_room', 'street', 'city', 'state', 'country', 'longitude', 'latitude'];

            //save user only if fields are valid
            if(helperServices.fieldCheck(params, requiredFields)) {
                return http.post(url, params);
                // return http({
                //     method: 'POST',
                //     url: url,
                //     headers: {
                //         'Content-Type': 'application/x-www-form-urlencoded'
                //     },
                //     data: params
                // });
            }
        }

        return {
            get:    get,
            create: create
        }
    }

    service.factory('venueServices', injectables.concat([serviceFunction]));

})();
