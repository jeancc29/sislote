var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http, {pingTimeout: 50000,});
// var io = require('socket.io')(http);
var Redis = require('ioredis');
var redis = new Redis();
var jwtAuth = require('socketio-jwt-auth');
// var process = require('dotenv').config({path: '/var/www/html/sislote/.env'});
var process = require('dotenv').config();


//user auth
// using middleware
io.use(jwtAuth.authenticate({
    secret: process.parsed.SOCKET_KEY,    // required, used to verify the token's signature
    algorithm: 'HS256',        // optional, default to be HS256
    succeedWithoutToken: false
  }, function(payload, done) {
    // you done callback will not include any payload data now
    // if no token was supplied
  
  return done(null, 'hey se connet: ');
    if (payload && payload.id) {
      User.findOne({id: payload.id}, function(err, user) {
        //return done('dentro user');
      if (err) {
          // return error
      return done('user error');
          return done(err);
        }
        if (!user) {
          // return fail with an error message
          return done(null, false, 'user does not exist');
        }
        // return success with a user info
        return done(null, user);
      });
    } else {
      return done('done error');
      return done() // in your connection handler user.logged_in will be false
    }
  }));
  
  //end user auth


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
redis.subscribe('blocksdirty', function(err, count) {
});
redis.subscribe('blocksdirtygenerals', function(err, count) {
});
redis.subscribe('versions', function(err, count) {
});
redis.subscribe('users', function(err, count) {
});
redis.subscribe('lotteries', function(err, count) {
});
redis.subscribe('notification', function(err, count) {
});
redis.subscribe('settings', function(err, count) {
});
redis.on('message', function(channel, message) {
    message = JSON.parse(message);
    console.log('Message Recieved: ' + message.data.room);
    var room = "valentin";
    if(message.data.room != "default")
      room = message.data.room;
    
    io.to(room).emit(channel + ':' + message.event, message.data);
});

io.on('connection', function(socket){
  // console.log("socket var: ", socket.handshake.query);
  //subscribe to the room or channel, en este caso uso 
  //un room diferente para cada servidor de cada cliente, asi
  //redusco el trabajo que deben hacer las aplicaciones moviles
  socket.join(socket.handshake.query.room);
});


http.listen(3000, function(){
    console.log('Listening on Port 3000');
});