var concat = require('concat');
var fs = require('fs');

var path = '../Conf+ Web/confplus/public/js/services';

var files = fs.readdirSync(path);

for (var i = 0; i < files.length; i++) {
	files[i] = path + '/' + files[i];
}

console.log(files);

concat(files, 'confplusApi.js', function (error) {
	console.log(error);
});
