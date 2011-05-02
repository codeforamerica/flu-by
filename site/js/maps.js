/**
 * This is a workaround for a bug involving IE and VML support
 * for OpenLayers
 */
document.namespaces;

/**
 * Main map
 */
(function($) {
  $(document).ready(function() {
    
    var options = {};
    
    // Set Custom image path (TODO make local)
    OpenLayers.ImgPath = 'http://js.mapbox.com/theme/dark/';
    
    var waxPrimary = { 'wax': [
      '@group',
      [
        '@new OpenLayers.Map',
        'map-primary',
        {
          'maxExtent': [
            '@new OpenLayers.Bounds', -20037508.34, -20037508.34, 20037508.34, 20037508.34
          ],
          "theme": "http://mapbox-js.s3.amazonaws.com/ol/2.8/mapbox.css",
          "maxResolution": 1.40625,
          "projection": [
            '@new OpenLayers.Projection',
            'EPSG:900913'
          ],
          "displayProjection": [
            '@new OpenLayers.Projection',
            'EPSG:900913'
          ],
          "units": "m"
        }
      ],
      ['@inject addLayer',
        ['@new OpenLayers.Layer.TMS',
          'MapBox Layer', 
          'http://a.tile.mapbox.com/',
          {
            "layername": 'world-light',
            "type": 'png',
            "projection": [
              "@new OpenLayers.Projection",
              "EPSG:900913"
            ],
            "serverResolutions": [
              156543.0339,78271.51695,39135.758475,19567.8792375,9783.93961875,
              4891.96980938,2445.98490469,1222.99245234,611.496226172,
              305.748113086,152.874056543,76.4370282715,38.2185141357,
              19.1092570679,9.55462853394,4.77731426697,2.38865713348,
              1.19432856674,0.597164283371
            ],
            "resolutions": [
              156543.0339,78271.51695,39135.758475,19567.8792375,9783.93961875,
              4891.96980938,2445.98490469,1222.99245234,611.496226172,
              305.748113086,152.874056543,76.4370282715,38.2185141357,
              19.1092570679,9.55462853394,4.77731426697,2.38865713348,
              1.19432856674,0.597164283371
            ],
            'maxExtent': [
              '@new OpenLayers.Bounds',
              -20037508.34, -20037508.34, 20037508.34, 20037508.34
            ]
          }
        ]
      ],
      ['@inject zoomTo', 3]
    ]};

    // Get some nice controls
    OpenLayers.ImgPath = "http://js.mapbox.com/theme/dark/";
    
    // Create maps
    var mapPrimary = wax.Record(waxPrimary.wax);
    var waxSecondary = waxPrimary;
    waxSecondary.wax[1][1] = 'map-secondary';
    var mapSecondary = wax.Record(waxSecondary.wax);
    
    // Data stuff
    $.getJSON('data/test/vaccination-total.json', function(data) {
      var items = [];
      $.each(data, function(key, val) {
        // var geocodeURL = 'http://maps.googleapis.com/maps/api/geocode/json?address=' + val.Area + '&sensor=false';
        //$.getJSON(geocodeURL, function(data) {});
      });
    });
    
  });
})(jQuery);