<?php
/**
 * This file is meant to convert the rawish flu
 * data to a more consumable format for geo stats.
 *
 * We are using Sag to connect to Couch DB.
 */

// Includes
require_once 'libraries/krumo/class.krumo.php';
require_once 'libraries/sag-0.4.0/src/Sag.php';
require_once 'libraries/geoPHP/geoPHP.inc';


// Variables
$server = 'health.iriscouch.com';
$port = '5984';
$db_orig = 'vaccination';
$db_new = 'flu-features';
$records = array();
$geocoded = array();

// Connect to database
$sag = new Sag($server, $port);
$sag->setDatabase($db_orig);

// Get some records
try {
  $count = 0;
  
  // Get all records and go through them
  $all = $sag->get('/_all_docs');
  foreach ($all->body->rows as $key => $doc_meta) {
    $count++;
    // Limit for testing
    if ($count > 10) continue;
    
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
    if (empty($geocoded[$area])) {
      // Get state polygon
      
      // Mark and put in structure
      $geocoded[$area] = TRUE;
      $records[$area]['place'] = TRUE;
    }
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