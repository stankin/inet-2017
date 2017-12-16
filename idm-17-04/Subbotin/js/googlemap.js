  var greyStyles = [
    {
      featureType: "all",
      stylers: [
        { saturation: -80 }
      ]
    },
    {
featureType: 'road.highway',
              rules: [
                {hue: -80},
                {saturation: -80},
                {lightness: 8.8}
]
    }
  ];

  var marker;
  var map;
  var village = new google.maps.LatLng(38.019427,12.519608);
  
  
function initialize() {
  var greyMapType = new google.maps.StyledMapType(greyStyles,
    {name: "Grey Area"});
  var mapOptions = {
    zoom: 14,
    center: village,
	};
      mapTypeControlOptions: {
      mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'grey_area']
    
  };
  var map = new google.maps.Map(document.getElementById('map_canvas'),
    mapOptions);

  //Associate the styled map with the MapTypeId and set it to display.
  map.mapTypes.set('grey_area', greyMapType);
  map.setMapTypeId('grey_area');

               
    marker = new google.maps.Marker({
      map:map,
 	  scrollwheel: true,
      navigationControl: true,
      mapTypeControl: true,
      scaleControl: true,
      draggable: true,
      animation: google.maps.Animation.DROP,
      position: village
    });
    google.maps.event.addListener(marker, 'click', toggleBounce);
  }

  function toggleBounce() {

    if (marker.getAnimation() != null) {
      marker.setAnimation(null);
    } else {
      marker.setAnimation(google.maps.Animation.BOUNCE);
    }
  }


