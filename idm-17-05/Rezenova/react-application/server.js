var fs = require('fs');
var path = require('path');
var express = require('express');
var bodyParser = require('body-parser');
var request = require('request');
var sass = require('node-sass-middleware');
var app = express();

var srcPath = __dirname + '/sass';
var destPath = __dirname + '/public/css';

app.set('port', (process.env.PORT || 3000));

app.use(sass({

    src: srcPath,
    dest: destPath,
    debug: true,
    outputStyle: 'compressed',
    prefix:  '/css'
}));

app.use('/', express.static(path.join(__dirname, 'public')));
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({extended: true}));

app.use(function(req, res, next) {

    res.setHeader('Access-Control-Allow-Origin', '*');

    res.setHeader('Cache-Control', 'no-cache');
    next();
});


function FToC(x) {
  return Math.floor((x - 32) * 0.5555555555555556)
}

function enToRu(x) {
  if (x === 'Mon') return 'Пн'
  if (x === 'Tue') return 'Вт'
  if (x === 'Wed') return 'Ср'
  if (x === 'Thu') return 'Чт'
  if (x === 'Fri') return 'Пт'
  if (x === 'Sat') return 'Сб'
  if (x === 'Sun') return 'Вс'
  return null;
}

function YahooCodesToSkyconsString(x) {
  /*
  0	tornado
  1	tropical storm
  2	hurricane
  3	severe thunderstorms
  4	thunderstorms
  5	mixed rain and snow
  6	mixed rain and sleet
  7	mixed snow and sleet
  8	freezing drizzle
  9	drizzle
  10	freezing rain
  11	showers
  12	showers
  13	snow flurries
  14	light snow showers
  15	blowing snow
  16	snow
  17	hail
  18	sleet
  19	dust
  20	foggy
  21	haze
  22	smoky
  23	blustery
  24	windy
  25	cold
  26	cloudy
  27	mostly cloudy (night)
  28	mostly cloudy (day)
  29	partly cloudy (night)
  30	partly cloudy (day)
  31	clear (night)
  32	sunny
  33	fair (night)
  34	fair (day)
  35	mixed rain and hail
  36	hot
  37	isolated thunderstorms
  38	scattered thunderstorms
  39	scattered thunderstorms
  40	scattered showers
  41	heavy snow
  42	scattered snow showers
  43	heavy snow
  44	partly cloudy
  45	thundershowers
  46	snow showers
  47	isolated thundershowers
  3200	not available*/

  if (0 === x || 2 === x || 23 === x || 24 === x) return "wind"

  else if (1 === x || 3 === x || 4 === x || 12 === x || 13 === x || 35 === x || 37 === x ||
    38 === x || 39 === x || 40 === x || 45 === x || 46 === x || 47 === x) return "sleet"

  else if (5 === x || 7 === x || 14 === x || 15 === x ||
    16 === x || 17  === x || 18  === x || 41  === x || 42  === x || 43 === x) return "snow"

  else if (32 === x || 36 === x) return "clear-day"

  else if (31 === x) return "clear-night"

  else if (28 === x || 30 === x || 34 === x) return "partly-cloudy-day"

  else if (27 === x || 29 === x || 33 === x) return "partly-cloudy-night"

  else if (26 === x || 44 === x) return "cloudy"

  else if (6 === x || 10 === x || 11 === x) return "rain"

  else if (8 === x || 9 === x || 19 === x || 20 === x || 21 === x || 22 === x || 25 === x) return "fog"

  return ""
}

app.post('/v1/api/forecast', function(req, res, next) {
  var city = decodeURI(req.body.city);
  var now = new Date;
  var hh = (now.getHours() < 10) ? ('0' + now.getHours()) : (now.getHours());
  var mm = (now.getMinutes() < 10) ? ('0' + now.getMinutes()) : (now.getMinutes());
  var currTime = (hh + ':' + mm);
  console.log(currTime + ' -- Request from: ' + city);
  var query = "select * from weather.forecast where woeid in (select woeid from geo.places(1) where text=\"" + city + "\")";
  var yahooQuery = "https://query.yahooapis.com/v1/public/yql?q=" + encodeURI(query) + "&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys";
  request(yahooQuery, function (err, rsp, body) {
    if (err) {
      res.status(500);
      return next(err);
    }

    var data = JSON.parse(body);

    var frcst = {};
    frcst.wind = Math.floor(data.query.results.channel.wind.speed*0.44704);
    frcst.humi = data.query.results.channel.atmosphere.humidity;
    frcst.temp = FToC(data.query.results.channel.item.condition.temp);
    frcst.type = YahooCodesToSkyconsString(parseInt(data.query.results.channel.item.condition.code));
    frcst.day  = enToRu(data.query.results.channel.item.condition.date.slice(0, 3));
    frcst.time = currTime;
    frcst.items = data.query.results.channel.item.forecast.slice(0, 5);
    frcst.items.forEach((g, i) => {
      frcst.items[i].high = FToC(g.high);
      frcst.items[i].low = FToC(g.low);
      frcst.items[i].day = enToRu(g.day);
      frcst.items[i].code = YahooCodesToSkyconsString(parseInt(g.code));
    });
    res.json(frcst);
  })
});


app.listen(app.get('port'), function() {
  console.log('Server started: http://localhost:' + app.get('port') + '/');
});
