function initMap() {
         var stankin = {lat: 55.789745, lng: 37.595052};
         var map = new google.maps.Map(document.getElementById('map'),{zoom: 4,center: stankin});
         var marker = new google.maps.Marker({position: stankin,map: map});
	
	 var AfricaKml = new google.maps.KmlLayer({url: 'https://sites.google.com/site/radtarasov/kml/africa.kml',map: map});
  	 var AsiaKml = new google.maps.KmlLayer({url: 'https://sites.google.com/site/radtarasov/kml/asia.kml',map: map});
   	 var AustraliaKml = new google.maps.KmlLayer({url: 'https://sites.google.com/site/radtarasov/kml/australia.kml',map: map});
 	 var EuropeKml = new google.maps.KmlLayer({url: 'https://sites.google.com/site/radtarasov/kml/europe.kml',map: map});
 	 var NorthAmericaKml = new google.maps.KmlLayer({url: 'https://sites.google.com/site/radtarasov/kml/north_america.kml',map: map});
 	 var SouthAmericaKml = new google.maps.KmlLayer({url: 'https://sites.google.com/site/radtarasov/kml/south_america.kml',map: map});
  AfricaKml.addListener('click', function(kmlEvent) {
    var text = "АФРИКА\n\nТерритория:\n30 221 532 км²\n\nВключает:\n55 государств\n\nПлотность:\n30,51 чел./км²"
    showInContentWindow(text);
  });
 /* AsiaKml.addListener('click', function(kmlEvent) {
    var text = "АЗИЯ\n\nТерритория:\n44 579 000 км²\n\nВключает:\n49 государств\n\nПлотность:\n87 чел./км²"
    showInContentWindow(text);
  });
  AustraliaKml.addListener('click', function(kmlEvent) {
    var text = "АВСТРАЛИЯ\n\nТерритория:\n7 692 024 км²\n\nПлотность:\n2,8 чел./км²"
    showInContentWindow(text);
  });
  EuropeKml.addListener('click', function(kmlEvent) {
    var text = "ЕВРОПА\n\nТерритория:\n10,18 млн км²\n\nВключает:\n50 государств\n\nПлотность:\n72,5 чел./км²"
    showInContentWindow(text);
  });*/
  
  function showInContentWindow(text) {
    var sidediv = document.getElementById('content-window');
    sidediv.innerHTML = text;
  }
}
