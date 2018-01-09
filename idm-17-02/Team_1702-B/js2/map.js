function initMap() {
         var stankin = {lat: 55.789745, lng: 37.595052};
         var map = new google.maps.Map(document.getElementById('map'),{zoom: 4,center: stankin});
         var marker = new google.maps.Marker({position: stankin,map: map});
	
	 var AfricaKml = new google.maps.KmlLayer({url: 'https://sites.google.com/site/radtarasov/kml/africa.kml', map: map});
  	 var AsiaKml = new google.maps.KmlLayer({url: 'https://sites.google.com/site/radtarasov/kml/asia.kml',map: map});
   	 var AustraliaKml = new google.maps.KmlLayer({url: 'https://sites.google.com/site/radtarasov/kml/australia.kml',map: map});
 	 var EuropeKml = new google.maps.KmlLayer({url: 'https://sites.google.com/site/radtarasov/kml/europe.kml',map: map});
 	 var NorthAmericaKml = new google.maps.KmlLayer({url: 'https://sites.google.com/site/radtarasov/kml/north_america.kml',map: map});
 	 var SouthAmericaKml = new google.maps.KmlLayer({url: 'https://sites.google.com/site/radtarasov/kml/south_america.kml',map: map});
	
  AfricaKml.addListener('click', function(kmlEvent){
   showInContentWindow("АФРИКА\n\nТерритория:\n 30 221 532 км²\n\nВключает:\n55 государств\n\nПлотность:\n30,51 чел./км²");
  });
  AsiaKml.addListener('click', function(kmlEvent) {
   showInContentWindow("АЗИЯ\n\nТерритория:\n44 579 000 км²\n\nВключает:\n49 государств\n\nПлотность:\n87 чел./км²");
  });
  AustraliaKml.addListener('click', function(kmlEvent) {
   showInContentWindow("АВСТРАЛИЯ\n\nТерритория:\n7 692 024 км²\n\nПлотность:\n2,8 чел./км²");
  });
  EuropeKml.addListener('click', function(kmlEvent) {
   showInContentWindow("ЕВРОПА\n\nТерритория:\n10,18 млн км²\n\nВключает:\n50 государств\n\nПлотность:\n72,5 чел./км²");
  });
  NorthAmericaKml.addListener('click', function(kmlEvent) {
   showInContentWindow("СЕВЕРНАЯ АМЕРИКА\n\nТерритория:\n24,25 млн км²\n\nВключает:\n23 государств\n\nПлотность:\n22,9 чел./км²");
  });
  SouthAmericaKml.addListener('click', function(kmlEvent) {
   showInContentWindow("ЮЖНАЯ АМЕРИКА\n\nТерритория:\n17,84 млн км²\n\nВключает:\n12 государств\n\nПлотность:\n21,4 чел./км²");
  });
  function showInContentWindow(text) {
    var sidediv = document.getElementById('content-window');
    sidediv.innerHTML = text;
  }
}
