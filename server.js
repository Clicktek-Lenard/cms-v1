const axios = require('axios');
const express = require('express');
const fs = require('fs');
const app = express(); 
const https = require('https'); 
const { Server } = require('socket.io'); 
const { createServer } = require("https"); 

const options = {
    key: fs.readFileSync('/etc/ssl/private/bae.cms.nwdi.ad.selfsigned.key'),
    cert: fs.readFileSync('/etc/ssl/certs/bae.cms.nwdi.ad.selfsigned.crt')
};

const server = https.createServer(options, app);
const httpServer = createServer(app); 
const io = require('socket.io')(server, {   
     cors: { origin: "*"} });  

const users = {}; // Store connected users with their socket IDs

io.on('connection', (socket) => {

    socket.on('display', () => {
        console.log(`Queuing display connected`);

    });

    socket.on('username', (username) => {
        console.log(`User ${username} connected`);
        // Store the username along with the socket ID
        users[socket.id] = username;

        io.emit('broadcastUsername', username);
    });

    socket.on('call', (data) => {
        const queueID = data.queueID;
        const username = users[socket.id];

        console.log(`${users[socket.id]} called patient number ${queueID}`);

        io.emit('call', {
            username: username,
            queueID: queueID
        });
    });

    socket.on('numOfCallUpdated', (data) => {
        socket.broadcast.emit('numOfCallUpdated', data);
    });

    socket.on('currentRoom', (data) => {
        socket.broadcast.emit('currentRoom', data);
    });

    socket.on('queueDisplay', (queueDisplay) => {
        console.log('----------------------');
        console.log('Counter:', queueDisplay.counter);
        console.log('Department:', queueDisplay.department);
        console.log('Queue No:', queueDisplay.queueNo);
        console.log('----------------------');

        socket.broadcast.emit('newQueueDisplay', queueDisplay);
    });

    socket.on('queueDisplayExit', (queueDisplayExit) => {
        console.log('----------------------');
        console.log('Counter:', queueDisplayExit.counter);
        console.log('Department:', queueDisplayExit.department);
        console.log('Queue No:', queueDisplayExit.queueNo);
        console.log('----------------------');

        socket.broadcast.emit('newqueueDisplayExit', queueDisplayExit);

        socket.emit('queueDisplayExitResponse', queueDisplayExit);
    });

    socket.on('in', (data) => {
        const queueID = data.queueID;
        const username = users[socket.id];

        console.log(`Patient ${queueID} entered room`);

        io.emit('in', {
            username: username,
            queueID: queueID
        });
    });

    socket.on('disconnect', () => {
        // Retrieve the username using the socket ID
        const username = users[socket.id];
        console.log(`User ${username} disconnected`);

        delete users[socket.id];
    });

});

const cms = require('minimist')(process.argv.slice(2));

 console.log(cms.p);

const PORT = cms.p;
const HOST = cms.b+'.cms.nwdi.ad';

server.listen(PORT, HOST, () => {
    console.log(`Server is running on https://${HOST}:${PORT}`);
});


