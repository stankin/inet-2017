var icons = new Skycons( { "monochrome": false } );

class Forecast extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    return (
      <div className="forecast">
        {this.props.data.map(function(item, i) {
          const uniqId = ("day-" + i);
          return (
            <Mini uniqId={uniqId} key={i} data={item} />
          );
        }, this)}
      </div>
    );
  }
}

class Mini extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      id: this.props.uniqId
    }
  }

  componentDidMount() {
    icons.add(this.state.id, this.props.data.code)
  }

  render() {
    const id = this.state.id;
    return (
       <div className="mini">
         <p>{this.props.data.day}</p>
         <canvas id={id} width={64} height={64}></canvas>
         <p><span>{this.props.data.high} °</span><span>{this.props.data.low} °</span></p>
       </div>
     )
  }
}

class Content extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    return (
      <div className="content">
        <Temp data={this.props.data.temp} type={this.props.data.type} />
        <Info data={this.props.data.info} />
      </div>
    );
  }
}

class Temp extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      celsium: true
    };
    this.handleClick = this.handleClick.bind(this);
  }

  handleClick() {
    this.setState({
      celsium: !this.state.celsium
    })
  }

  componentDidMount() {
    icons.add('nowIcon', this.props.type)
  }

  render() {
    const currTemp = this.state.celsium ? this.props.data : Math.floor(this.props.data * 1.8 + 32);
    return this.state.celsium ? (
      <div className="temp">
        <canvas id="nowIcon" width={96} height={96}></canvas>
        <p><span>{currTemp}</span>
        <a className="active">°C</a> |
        <a href="#" onClick={this.handleClick}>°F</a>
        </p>
      </div>
    ) : (
      <div className="temp">
        <canvas width={96} height={96}></canvas>
        <p><span>{currTemp}</span>
        <a href="#" onClick={this.handleClick}>°C</a> |
        <a className="active">°F</a>
        </p>
      </div>
    );
  }
}

class Info extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    return (
      <div className="info">
        <ul>
          <li>Влажность: {this.props.data.humi}%</li>
          <li>Ветер: {this.props.data.wind} м/с</li>
        </ul>
      </div>
    );
  }
}

class Head extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    return (
      <div className="head">
        <h1>{this.props.data.city}</h1>
        <h4>{this.props.data.head}</h4>
      </div>
    );
  }
}


class App extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      url: '/v1/api/forecast',
      city: 'Владивосток',
      res: {
        time: '',
        day: '',
        humi: '',
        temp: '',
        type: '',
        text: '',
        wind: '',
        items: []
      },
      loaded: false
    };
    this.getForecast = this.getForecast.bind(this);
  }

  componentDidMount() {
    this.getForecast();
    setInterval(this.getForecast.bind(this), this.props.pollInterval);
  }

  getForecast() {
    const self = this;
    ymaps.ready(() => setTimeout(() => {
        const ct = ymaps.geolocation.city;
        const settings = {
          url: self.state.url,
          dataType: 'json',
          type: 'POST',
          data: { city: ct },
          success: (data) => {
            self.setState({
              res: data,
              loaded: true,
              city: ct
            });
            icons.play();
          },
          error: (xhr, status, err) => {
            console.error('Cannot load forecast from server :(');
          }
        }
        $.ajax(settings);

      }, 500));
  }



  render() {
    return this.state.loaded ? (
        <main className="container">
          <header className="header">
            <h1>Прогноз погоды для Вашего города</h1>
            <h2>Для прогноза были использованы <a href="https://developer.yahoo.com/weather/">Yahoo API</a> и <a href="https://tech.yandex.ru/maps/">Yandex.Maps API</a></h2>
          </header>
          <div className="card">
            <Head data={{city: this.state.city, head: (this.state.res.day + ', ' + this.state.res.time) }} />
            <Content data={{temp: this.state.res.temp, type: this.state.res.type, info: { humi: this.state.res.humi, wind: this.state.res.wind }}} />
            <h4>Прогноз на 5 дней</h4>
            <Forecast data={this.state.res.items} />
          </div>
        </main>
      ) : (
        <div className="loader">
          <div className="spin">
            <div className="box box1"></div>
            <div className="box box2"></div>
            <div className="box box3"></div>
            <div className="box box4"></div>
            <div className="box box5"></div>
            <div className="box box6"></div>
            <div className="box box7"></div>
            <div className="box box8"></div>
            <div className="box box9"></div>
          </div>
        </div>
      )
  }
}

ReactDOM.render(
  <App pollInterval={1.8e+6} />,
  document.getElementById('app')
);
