var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var Redis = require('ioredis');
var redis = new Redis();
var jwtAuth = require('socketio-jwt-auth');
var process = require('dotenv').config();


redis.subscribe('test-channel', function(err, count) {
});
redis.subscribe('realtime-stock', function(err, count) {
});
redis.subscribe('blocksgenerals', function(err, count) {
});
redis.subscribe('blockslotteries', function(err, count) {
});
redis.subscribe('blocksplays', function(err, count) {
});
redis.subscribe('blocksplaysgenerals', function(err, count) {
});
redis.on('message', function(channel, message) {
    console.log('Message Recieved: ' + message);
    message = JSON.parse(message);
    io.emit(channel + ':' + message.event, message.data);
});

//user auth
// using middleware
// io.use(jwtAuth.authenticate({
//   secret: process.parsed.SOCKET_KEY,    // required, used to verify the toke$
//   algorithm: 'HS256',        // optional, default to be HS256
//   succeedWithoutToken: false
// }, function(payload, done) {
//   // you done callback will not include any payload data now
//   // if no token was supplied

// return done(null, 'hey se connet: ');
//   if (payload && payload.id) {
//     User.findOne({id: payload.id}, function(err, user) {
//       //return done('dentro user');
//      if (err) {
//         // return error
//      return done('user error');
//         return done(err);
//       }
//       if (!user) {
//         // return fail with an error message
//         return done(null, false, 'user does not exist');
//       }
//       // return success with a user info
//       return done(null, user);
//     });
//   } else {
//      return done('done error');
//     return done() // in your connection handler user.logged_in will be false
//   }
// }));

//end user auth

http.listen(3000, function(){
    console.log('Listening on Port 3000');
});