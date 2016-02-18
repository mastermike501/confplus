<html>
<head>
    <title>Test</title>
</head>
<body ng-app="app" ng-controller="mainController">

<div class="col-md-8 col-md-offset-2">

    <!-- PAGE TITLE =============================================== -->
    <div class="page-header">
        <h2>Laravel and Angular Single Page Application</h2>
        <h4>User System</h4>
    </div>
    
    <!-- NEW COMMENT FORM =============================================== -->
    <form ng-submit="createUser()"> <!-- ng-submit will disable the default form action and use our function -->
    
        <!-- AUTHOR -->
        <div class="form-group">
            <input type="text" class="form-control input-sm" name="name" ng-model="userData.name" placeholder="Name">
        </div>
    
        <!-- SUBMIT BUTTON -->
        <div class="form-group text-right">   
            <button type="submit" class="btn btn-primary btn-lg">Submit</button>
        </div>
    </form>
    
    <!-- LOADING ICON =============================================== -->
    <!-- show loading icon if the loading variable is set to true -->
    <p class="text-center" ng-show="loading">O</p>
    
    <!-- THE COMMENTS =============================================== -->
    <!-- hide these comments if the loading variable is true -->

    <ul ng-show="!loading">
        <li ng-repeat="user in users">{{ user }}</li>
    </ul>
    
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.9/angular.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.9/angular-route.min.js"></script>
<script src="/js/controllers/mainCtrl.js"></script> <!-- load our controller -->
<script src="/js/services/userService.js"></script> <!-- load our service -->
<script src="/js/app.js"></script> <!-- load our application -->
</body>
</html>