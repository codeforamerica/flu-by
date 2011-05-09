<?php
/**
 * This file is meant to convert the rawish flu
 * data to a more consumable format for geo stats.
 *
 * We are using Sag to connect to Couch DB.
 */
session_start();

// Includes
require_once 'libraries/krumo/class.krumo.php';
require_once 'libraries/sag-0.4.0/src/Sag.php';
require_once 'libraries/geoPHP/geoPHP.inc';

// SimpleGeo
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/libraries/');
require_once 'Services_SimpleGeo/Services/SimpleGeo.php';
require_once 'authentication/auth.php';

// Variables
$server = 'health.iriscouch.com';
$port = '5984';
$db_orig = 'vaccination';
$db_new = 'flu-features';
$records = array();

// Connect to database
$sag = new Sag($server, $port);
$sag->setDatabase($db_orig);

// Create SimpleGeo client
$simple_geo = new Services_SimpleGeo($simplegeo_oath_token, $simplegeo_oath_secret);

// Set up session for geocoding, since we don't need to do
// it more than once, usually
if (empty($_SESSION['geocoded']) || !empty($_GET['geocode_reset'])) {
  $_SESSION['geocoded'] = array();
}

// Get some records
try {
  $count = 0;
  
  // Get all records and go through them
  $all = $sag->get('/_all_docs');
  foreach ($all->body->rows as $key => $doc_meta) {
    $count++;
    // Limit for testing
    if ($count > 50) continue;
    
    // Create a structure that will be helpful
    $doc = $sag->get('/' . $doc_meta->id);
    $body = (array) $doc->body;
    $area = clean_string($body['Area']);
    $records[$area]['time']
      [clean_string($body['Month'])]
        [clean_string($body['Category']) . ': '  . clean_string($body['Groups'])] = array(
          'coverage' => (float) clean_string($body['Coverage']),
          'ci95' => (float) clean_string($body['95CI']),
        );
        
    // Geocode, if we have not already
    if (empty($_SESSION['geocoded'][$area])) {
      $geometry = array();
    
      // Geocode
      $location = geocode($area);
      if (!empty($location)) {
        // Save point
        $_SESSION['geocoded'][$area]['place']['centroid']['lat'] = $location->lat->__toString();
        $_SESSION['geocoded'][$area]['place']['centroid']['lon'] = $location->lng->__toString();
      
        // Get context
        $geo_data = $simple_geo->getContext($location->lat->__toString(), $location->lng->__toString());

        // Find the state feature
        foreach ($geo_data->features as $k => $geo_feature) {
          if ($geo_feature->classifiers[0]->subcategory == 'State') {
            $geometry = $simple_geo->getFeature($geo_data->features[$k]->handle);
          }
        }
      }
      
      // Mark and put in structure
      $_SESSION['geocoded'][$area]['place']['state_geometry'] = $geometry;
    }
    $records[$area]['place'] = $_SESSION['geocoded'][$area]['place'];
  }
  
  create_output($records, TRUE);
} 
catch (SagCouchException $e) { 
  // Just debug to screen
  create_output($e, TRUE);
  throw $e;
}
catch (SagException $e) {
  // Just debug to screen
  create_output($e, TRUE);
  create_output($e->getMessage(), TRUE);
}
catch (Services_SimpleGeo_Exception $e) {
  // Just debug to screen
  create_output($e, TRUE);
  create_output($e->getMessage(), TRUE);
  create_output($e->getCode(), TRUE);
}

// Make an HTML page or CLI output, accordingly
if (defined('STDIN')) {
  // This will get really messy with debugging info
  print create_output('');
}
else { 
  print print_page();
}

/**
 * Output function
 */
function create_output($output, $debug = FALSE) {
  static $full_output = '';
  
  // Create output, check for Debug
  if ($debug == TRUE) {
    $debug_output = krumo($output);
    if (is_object($output)) {
      $debug_output .= krumo(get_class_methods($output));
    }
    $output = $debug_output;
  }
  
  // Add to output
  $full_output .= $output;
}

/**
 * Clean output
 */
function clean_string($input) {
  // Special cases
  $input = str_replace('³', '&ge;', $input);

  // Convert all non-alpha numeric characters to spaces
  return trim(preg_replace("/[^a-zA-Z0-9.<>=&\-\s]/", " ", (string) $input));
}

/**
 * Print page
 */
function print_page() {
  return '
<!doctype html>
<html>
<head>
  <title>Convert Vaccination Data to Geographical Feature Data</title>
</head>
<body>
  <div class="main">' . create_output('') . '</div>
  
  <script src="libraries/krumo/krumo.js"></script>
</body>
</html>
  ';
}

/**
 * Geocode a string using Googles Geocoder
 */
function geocode($input) {
  // "OK" indicates that no errors occurred; the address 
  //      was successfully parsed and at least one geocode was returned.
  // "ZERO_RESULTS" indicates that the geocode was 
  //      successful but returned no results. This may occur 
  //      if the geocode was passed a non-existent address or 
  //      a latlng in a remote location.
  // "ZERO_RESULTS" indicates that you are over your quota.
  // "REQUEST_DENIED" indicates that your request was denied, 
  //     generally because of lack of a sensor parameter.
  // "INVALID_REQUEST" generally indicates that the
  //     query (address or latlng) is missing.
  $delay = 100000;
  set_time_limit(1000);
  $base_url = "http://maps.google.com/maps/api/geocode/xml?sensor=false";
  
  // Make address.  Hard code US in there
  $request_url = $base_url . "&address=" . urlencode($input).",USA";
  $response = simplexml_load_file($request_url);
  if ($response->status = 'OK') {
		$location = $response->result->geometry->location;
		return $location;
	}
	else {
		create_output($response, TRUE);
		return FALSE;
	}
}
