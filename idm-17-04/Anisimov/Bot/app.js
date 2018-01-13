var TelegramBot = require('node-telegram-bot-api');
var fs = require('fs');
var http = require('http');
const fetch = require('node-fetch');
const sqlite3 = require('sqlite3').verbose();
var token = ''; // Telegram bot token
var bot = new TelegramBot(token, {
    polling: true
});
let db = new sqlite3.Database('./song.db3', sqlite3.OPEN_READWRITE, (err) => {
    if (err) {
        console.error(err.message);
    }
    console.log('Connected to the song database.');
});

bot.onText(/\/song (.+)/, function(msg, match) {
    var userId = msg.from.id;
    var full_name = match[1].split(' - ');
    var artist = full_name[0];
    var song = full_name[1];

    var api = 'http://lyrics.wikia.com/wikia.php?controller=LyricsApi&method=getSong&artist=' + encodeURIComponent(artist.trim()) + '&song=' + encodeURIComponent(song.trim());
    fetch(api)
        .then(function(response) {
            if (!response.ok) {
                bot.sendMessage(userId, 'Сорян, я не нашёл песню 🤪');
            }
            return response;
        })
        .then(res => res.json())
        .then(json => {
            var performer = json.result.artist.name;
            var title = json.result.name;
            var clean_name = json.result.name;
            var file_tname = json.result.artist.name + ' - ' + json.result.name + ' (' + json.result.album.name + ')';
            var response = 'Артист: *' + json.result.artist.name + '*\nПесня: *' + json.result.name + '*\n\n' + json.result.lyrics;
            bot.sendMessage(userId, response, {
                parse_mode: "Markdown"
            });
            bot.sendPhoto(userId, json.result.medium_image);
            var oturl = 'http://onetwo.tv:3000/search?type=releases&query=' + json.result.artist.name + ' ' + json.result.album.name;
            fetch(oturl)
                .then(res => res.json())
                .then(json => {
                    if (json.length < 1) {
                        console.log("no such release");
                    }
                    var album = json[0].id;
                    oturl = 'http://onetwo.tv:3000/release/' + album;
                    fetch(oturl)
                        .then(res => res.json())
                        .then(json => {
                            if (json.tracks.length < 1) {
                                console.log("wrong release id");
                            }
                            var tracks = json.tracks[0];
                            var songfile = findHash(clean_name, tracks);
                            if (songfile) {
                                var mp3 = songfile.file;
                                var mp3_id = songfile.id;
                                var tg_exist = false;

                                let sql = `SELECT tg_fileid FROM songs WHERE ot_sid  = ? LIMIT 1`;
                                db.each(sql, [mp3_id], function(err, row) {
                                    if (err) {
                                        return console.error(err.message);
                                    }
                                    return row ?
                                        tg_exist = row.tg_fileid :
                                        console.log(`No song found with the onetwo id ${mp3_id}`);

                                }, function() {
                                    if (tg_exist) {
                                        console.log(`Cached found and returned with tg_fileid ${tg_exist}`);
                                        bot.sendAudio(userId, tg_exist);
                                    } else {
                                        var download = function(url, dest, cb) {
                                            var file = fs.createWriteStream(dest);
                                            var request = http.get(url, function(response) {
                                                response.pipe(file);
                                                file.on('finish', function() {
                                                    file.close(cb);
                                                });
                                            }).on('error', function(err) { // Handle errors
                                                fs.unlink(dest); // Delete the file async. (But we don't check the result)
                                                if (cb) cb(err.message);
                                            });
                                        };
                                        var rnd = randomStr(9);
                                        const fileOptions = {
                                            filename: file_tname
                                        };
                                        download(mp3, rnd + ".mp3", function() {
                                            bot.sendAudio(userId, rnd + ".mp3", {
                                            bot.sendAudio(userId, rnd + ".mp3", {
                                                performer: performer,
                                                title: title
                                            }).then(function(resp) {
                                                db.run(`INSERT INTO songs(ot_sid, tg_fileid) VALUES(?, ?)`, [mp3_id, resp.audio.file_id], function(err) {
                                                    if (err) {
                                                        return console.log(err.message);
                                                    }
                                                    console.log(`A song has been inserted with rowid ${this.lastID}`);
                                                });
                                            });
                                            fs.unlink(rnd + ".mp3");
                                        });
                                    }
                                });
                            }
                        });
                });
        });
});


bot.onText(/\/author/, function(msg, match) {
    bot.sendMessage(msg.chat.id, 'Бота создал: *Анисимов Александр*, _ИДМ-17-04_', {
        parse_mode: "Markdown"
    });
});

bot.onText(/\/start/, function(msg, match) {

});

bot.onText(/\/song/, function(msg) {
    if (msg.text == "/song") bot.sendMessage(msg.chat.id, 'Как искать? /song исполнитель - название');
});

function randomStr(m) {
    var m = m || 9;
    s = '', r = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    for (var i = 0; i < m; i++) {
        s += r.charAt(Math.floor(Math.random() * r.length));
    }
    return s;
};

function findHash(hash, data) {
    var dataLen = data.length;
    for (dataLen > 0; dataLen--;) {
        if (data[dataLen].name == hash) {
            return data[dataLen];
        }
    }
    return false;
}