const socket = io('http://localhost:8080')

var app = socket;
app.use(function(req, res, next){
    res.header("Access-Control-Allow-Origin", "*");
    res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    next();
})

socket.on('chat-message', data => {
    console.log(data)
})
