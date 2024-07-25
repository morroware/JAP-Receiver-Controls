<?php
/**
 * Configuration file for the Castle API Endpoint Tester
 * 
 * This file contains the list of receivers and their corresponding IP addresses.
 * Edit this file to add, remove, or modify receiver configurations.
 */

// Array of receivers with their names as keys and IP addresses as values
$RECEIVERS = array(
    "Bowling Music" => "192.168.8.25",
    "Rink Music" => "192.168.8.15",
    // Add more receivers here as needed
);

// Other configuration variables can be added here as needed
$MAX_VOLUME = 11;
$MIN_VOLUME = 1;
$VOLUME_STEP = 1;
$MAX_CHANNELS = 4;
?>
