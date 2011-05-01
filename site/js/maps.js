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
    var mapID = 'map-main';
    
    var mm = com.modestmaps;
    var m = new mm.Map(
      'map-main',
      new com.modestmaps.WaxProvider({
        baseUrl: 'http://a.tiles.mapbox.com/mapbox/',
        layerName: 'world-light'
      })
    );
    
    m.setCenterZoom(new com.modestmaps.Location(38.8225909, -97.5585), 4);
    
    
    
/* OpenLayes maps
    var options = {};
    
    // Set Custom image path (TODO make local)
    OpenLayers.ImgPath = 'http://js.mapbox.com/theme/dark/';
    
    // Set options for map
    options.projection = new OpenLayers.Projection('EPSG:900913');
    options.displayProjection = new OpenLayers.Projection('EPSG:4326');
    options.maxExtent = new OpenLayers.Bounds(
        -20037508.34, -20037508.34, 20037508.34, 20037508.34);
    }
    options.maxResolution = 1.40625;
    options.controls = [];
    
    // Make map
    var mainMap = new OpenLayers.Map(mapID, options);
    
    // Add layers
    mainMap.addLayers();
*/
  
  });
})(jQuery);