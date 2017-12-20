src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDTGd8xI_Tz0Im3QmVxHEEt0m-taVxCg1w&callback=initMap">
        
function initMap() {
         var stankin = {lat: 55.789745, lng: 37.595052};
         var map = new google.maps.Map(document.getElementById('map'),{zoom: 4,center: stankin});
         var marker = new google.maps.Marker({position: stankin,map: map});
	 var KmlLayer = new google.maps.KmlLayer({url: 'https://sites.google.com/site/radtarasov/kml/Russia.kml',map: map});
  
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
