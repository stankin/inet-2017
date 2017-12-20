function initMap() {
    var KmlLayer = new google.maps.KmlLayer({
        url: 'https://stankin.github.io/inet-2017/idm-17-02/Team_1702-B/kml/Russia.kml',
        map: map
    });
/*  var map = new google.maps.Map(document.getElementById('map'), {
    zoom: 12,
    center: {lat: 37.06, lng: -95.68}
  });

  var kmlLayer = new google.maps.KmlLayer({
    url: 'http://googlemaps.github.io/kml-samples/kml/Placemark/placemark.kml',
    suppressInfoWindows: true,
    map: map
  });

  kmlLayer.addListener('click', function(kmlEvent) {
    var text = kmlEvent.featureData.description;
    showInContentWindow(text);
  });

  function showInContentWindow(text) {
    var sidediv = document.getElementById('content-window');
    sidediv.innerHTML = text;
  }*/
}
