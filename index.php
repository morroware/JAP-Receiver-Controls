<?php
/**
 * Utility functions for the Castle AV Control System
 * 
 * This file contains all the necessary utility functions for interacting with
 * Just Add Power Device APIs and generating HTML forms for the user interface. It provides
 * a set of reusable functions to handle API calls, retrieve current device settings,
 * generate HTML forms, sanitize user input, and log messages.
 *
 * @author Seth Morrow
 * @version 0.01.5
 */

/**
 * Function to handle API calls to the device
 * 
 * This function constructs and executes a cURL request to interact with the Castle API.
 * It supports various HTTP methods and can send data with the request if needed.
 * 
 * @param string $method - The HTTP method to be used for the API call (e.g., GET, POST)
 * @param string $deviceIp - The IP address of the device to interact with
 * @param string $endpoint - The specific API endpoint to call (e.g., 'details/channel')
 * @param mixed $data - Optional data to send with the API request (default is null)
 * @return string - The response from the API call as a string
 * @throws Exception if the API call fails due to cURL errors or non-200 HTTP responses
 */
function makeApiCall($method, $deviceIp, $endpoint, $data = null) {
    // Construct the full API URL
    $apiUrl = 'http://' . $deviceIp . '/cgi-bin/api/' . $endpoint;
    
    // Initialize cURL session
    $ch = curl_init($apiUrl);
    
    // Set the HTTP method for the request
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    // If data is provided, add it to the request
    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));
    }

    // Set options to return the response as a string and set a timeout
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 10-second timeout

    // Execute the cURL request
    $result = curl_exec($ch);

    // Check for cURL errors
    if ($result === false) {
        throw new Exception('cURL error: ' . curl_error($ch));
    }

    // Get the HTTP response code
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Close the cURL session
    curl_close($ch);

    // Check for HTTP errors (4xx or 5xx responses)
    if ($httpCode >= 400) {
        throw new Exception('HTTP error: ' . $httpCode);
    }

    return $result;
}

/**
 * Function to get the current volume setting from a device
 * 
 * This function retrieves the current volume setting from a specified device
 * using the Castle API. It handles potential errors and provides a safe default.
 * 
 * @param string $deviceIp - The IP address of the device
 * @return int - The current volume level, defaulting to 0 if not set or in case of an error
 */
function getCurrentVolume($deviceIp) {
    try {
        // Make an API call to get the current volume
        $response = makeApiCall('GET', $deviceIp, 'details/audio/stereo/volume');
        
        // Parse the JSON response
        $data = json_decode($response, true);
        
        // Extract the volume value, defaulting to 0 if not set
        return isset($data['data']) ? intval($data['data']) : 0;
    } catch (Exception $e) {
        // Log the error and return a safe default value
        error_log('Error getting current volume: ' . $e->getMessage());
        return 0;
    }
}

/**
 * Function to get the current channel setting from a device
 * 
 * This function retrieves the current channel setting from a specified device
 * using the Castle API. It includes debug logging and error handling.
 * 
 * @param string $deviceIp - The IP address of the device
 * @return int - The current channel number, defaulting to 1 if not set or in case of an error
 */
function getCurrentChannel($deviceIp) {
    try {
        // Make an API call to get the current channel
        $response = makeApiCall('GET', $deviceIp, 'details/channel');
        
        // Parse the JSON response
        $data = json_decode($response, true);
        
        // Extract the channel value, defaulting to 1 if not set
        $channel = isset($data['data']) ? intval($data['data']) : 1;
        
        // Debug logging
        error_log("getCurrentChannel for $deviceIp: Raw response: " . print_r($response, true));
        error_log("getCurrentChannel for $deviceIp: Parsed channel: $channel");
        
        return $channel;
    } catch (Exception $e) {
        // Log the error and return a safe default value
        error_log('Error getting current channel: ' . $e->getMessage());
        return 1;
    }
}

/**
 * Function to generate the HTML for a receiver form
 * 
 * This function creates an HTML form for controlling a specific receiver,
 * including channel selection and volume control.
 * 
 * @param string $receiverName - The name of the receiver
 * @param string $deviceIp - The IP address of the receiver
 * @param int $maxChannels - The maximum number of channels
 * @param int $minVolume - The minimum volume level
 * @param int $maxVolume - The maximum volume level
 * @param int $volumeStep - The step size for the volume slider
 * @return string - The HTML for the receiver form
 */
function generateReceiverForm($receiverName, $deviceIp, $maxChannels, $minVolume, $maxVolume, $volumeStep) {
    // Get current settings for the receiver
    $currentVolume = getCurrentVolume($deviceIp);
    $currentChannel = getCurrentChannel($deviceIp);
    
    // Debug logging
    error_log("generateReceiverForm for $receiverName ($deviceIp): Current Channel: $currentChannel, Current Volume: $currentVolume");
    
    // Start building the HTML for the form
    $html = "<div class='receiver'>";
    $html .= "<form method='POST'>";
    $html .= "<button type='button'>" . htmlspecialchars($receiverName) . "</button>";
    
    // Generate channel selection dropdown
    $html .= "<select id='channel_" . htmlspecialchars($receiverName) . "' name='channel'>";
    for ($channel = 1; $channel <= $maxChannels; $channel++) {
        $selected = ($channel == $currentChannel) ? ' selected' : '';
        $html .= "<option value='$channel'$selected>Channel $channel</option>";
    }
    $html .= "</select>";
    
    // Generate volume slider
    $html .= "<div class='slider-label'>Volume: $currentVolume</div>";
    $html .= "<input type='range' name='volume' min='$minVolume' max='$maxVolume' step='$volumeStep' value='$currentVolume' oninput='updateVolumeLabel(this)'>";
    
    // Add hidden input for receiver IP and submit button
    $html .= "<input type='hidden' name='receiver_ip' value='" . htmlspecialchars($deviceIp) . "'>";
    $html .= "<button type='submit'>Set Channel & Volume</button>";
    $html .= "</form>";
    $html .= "</div>";
    
    return $html;
}

/**
 * Function to sanitize and validate input data
 * 
 * This function sanitizes and validates various types of input data,
 * providing type-specific validation and optional range checking for integers.
 * 
 * @param mixed $data - The input data to sanitize
 * @param string $type - The type of data (e.g., 'int', 'ip')
 * @param array $options - Additional options for validation (e.g., min, max for integers)
 * @return mixed - The sanitized data, or null if invalid
 */
function sanitizeInput($data, $type, $options = []) {
    switch ($type) {
        case 'int':
            // Validate and sanitize integer input, with optional range checking
            $sanitized = filter_var($data, FILTER_VALIDATE_INT, [
                'options' => [
                    'min_range' => $options['min'] ?? PHP_INT_MIN,
                    'max_range' => $options['max'] ?? PHP_INT_MAX
                ]
            ]);
            break;
        case 'ip':
            // Validate and sanitize IP address input
            $sanitized = filter_var($data, FILTER_VALIDATE_IP);
            break;
        default:
            // For unsupported types, return null
            $sanitized = null;
    }
    // Return the sanitized value if valid, or null if invalid
    return $sanitized !== false ? $sanitized : null;
}

/**
 * Function to log errors or important events
 * 
 * This function writes log messages to a file, including a timestamp and log level.
 * 
 * @param string $message - The message to log
 * @param string $level - The log level (e.g., 'error', 'info')
 */
function logMessage($message, $level = 'info') {
    // Define the log file path
    $logFile = __DIR__ . '/app.log';
    
    // Get the current timestamp
    $timestamp = date('Y-m-d H:i:s');
    
    // Format the log message
    $formattedMessage = "[$timestamp] [$level] $message" . PHP_EOL;
    
    // Append the message to the log file
    file_put_contents($logFile, $formattedMessage, FILE_APPEND);
}

?>
