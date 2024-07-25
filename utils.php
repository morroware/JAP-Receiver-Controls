<?php
/**
 * Utility functions for the Castle API Endpoint Tester
 * 
 * This file contains a comprehensive set of utility functions designed to interact with
 * the Castle API and generate HTML forms for the user interface. These functions handle
 * various aspects of the application, including API communication, data retrieval,
 * form generation, input sanitization, and logging.
 *
 * The main components of this utility file include:
 * 1. API interaction (makeApiCall)
 * 2. Device state retrieval (getCurrentVolume, getCurrentChannel)
 * 3. HTML form generation (generateReceiverForm)
 * 4. Input sanitization and validation (sanitizeInput)
 * 5. Logging functionality (logMessage)
 *
 * Each function is designed to be modular and reusable, promoting clean code practices
 * and easier maintenance. Error handling is implemented throughout to ensure robust
 * operation even in case of API or network issues.
 *
 * @author Seth Morrow
 * @version 0.01.5
 * @last_updated 2024-07-25
 */

/**
 * Function to handle API calls to the device
 * 
 * This function serves as the central point for all API communications with the Castle devices.
 * It constructs and executes a cURL request based on the provided parameters, handling various
 * HTTP methods and optional data payloads.
 * 
 * The function includes error handling for both cURL execution errors and HTTP response codes,
 * throwing exceptions in case of failures. This allows calling functions to implement their own
 * error handling strategies.
 * 
 * @param string $method - The HTTP method for the API call (e.g., 'GET', 'POST', 'PUT', 'DELETE')
 * @param string $deviceIp - The IP address of the target device
 * @param string $endpoint - The specific API endpoint to call (e.g., 'settings/audio/stereo/volume')
 * @param mixed $data - Optional data to send with the request (default is null)
 * 
 * @return string - The raw response from the API call
 * 
 * @throws Exception If there's a cURL error or if the HTTP response code is 400 or greater
 * 
 * @example
 * try {
 *     $response = makeApiCall('GET', '192.168.1.100', 'settings/audio/stereo/volume');
 *     // Process $response
 * } catch (Exception $e) {
 *     // Handle the error
 * }
 */
function makeApiCall($method, $deviceIp, $endpoint, $data = null) {
    // Construct the full API URL
    $apiUrl = 'http://' . $deviceIp . '/cgi-bin/api/' . $endpoint;
    
    // Initialize cURL session
    $ch = curl_init($apiUrl);
    
    // Set the HTTP method for the request
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    // If data is provided, add it to the request
    // This is typically used for POST or PUT requests
    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));
    }

    // Set options to return the response as a string instead of outputting it
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // Set a timeout to prevent hanging on slow or unresponsive devices
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 10-second timeout

    // Execute the cURL request
    $result = curl_exec($ch);

    // Check for cURL errors
    if ($result === false) {
        $errorMessage = curl_error($ch);
        curl_close($ch);
        throw new Exception('cURL error: ' . $errorMessage);
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
 * This function retrieves the current volume setting from a specified Castle device
 * using the API. It includes error handling to ensure a safe default is returned
 * in case of any issues with the API call or response parsing.
 * 
 * @param string $deviceIp - The IP address of the target device
 * 
 * @return int - The current volume level (0-100), or 0 if not set or in case of an error
 * 
 * @example
 * $volume = getCurrentVolume('192.168.1.100');
 * echo "Current volume: $volume";
 */
function getCurrentVolume($deviceIp) {
    try {
        // Attempt to retrieve the current volume via API
        $response = makeApiCall('GET', $deviceIp, 'details/audio/stereo/volume');
        
        // Parse the JSON response
        $data = json_decode($response, true);
        
        // Extract the volume value if it exists, otherwise default to 0
        // The intval function is used to ensure we always return an integer
        return isset($data['data']) ? intval($data['data']) : 0;
    } catch (Exception $e) {
        // Log the error for debugging purposes
        error_log('Error getting current volume: ' . $e->getMessage());
        
        // Return 0 as a safe default in case of any errors
        return 0;
    }
}

/**
 * Function to get the current channel setting from a device
 * 
 * This function retrieves the current channel setting from a specified Castle device
 * using the API. It includes error handling and debug logging to aid in troubleshooting.
 * 
 * @param string $deviceIp - The IP address of the target device
 * 
 * @return int - The current channel number (typically 1-4), or 1 if not set or in case of an error
 * 
 * @example
 * $channel = getCurrentChannel('192.168.1.100');
 * echo "Current channel: $channel";
 */
function getCurrentChannel($deviceIp) {
    try {
        // Attempt to retrieve the current channel via API
        $response = makeApiCall('GET', $deviceIp, 'details/channel');
        
        // Parse the JSON response
        $data = json_decode($response, true);
        
        // Extract the channel value if it exists, otherwise default to 1
        $channel = isset($data['data']) ? intval($data['data']) : 1;
        
        // Debug logging to aid in troubleshooting
        error_log("getCurrentChannel for $deviceIp: Raw response: " . print_r($response, true));
        error_log("getCurrentChannel for $deviceIp: Parsed channel: $channel");
        
        return $channel;
    } catch (Exception $e) {
        // Log the error for debugging purposes
        error_log('Error getting current channel: ' . $e->getMessage());
        
        // Return 1 as a safe default in case of any errors
        return 1;
    }
}

/**
 * Function to generate the HTML for a receiver form
 * 
 * This function creates an HTML form for controlling a specific audio receiver,
 * including channel selection and volume control. It retrieves the current settings
 * for the device and pre-populates the form accordingly.
 * 
 * @param string $receiverName - The name of the receiver (for display purposes)
 * @param string $deviceIp - The IP address of the receiver
 * @param int $maxChannels - The maximum number of channels supported by the receiver
 * @param int $minVolume - The minimum volume level (typically 0)
 * @param int $maxVolume - The maximum volume level (typically 100)
 * @param int $volumeStep - The step size for the volume slider (e.g., 1 for fine control, 5 for coarser control)
 * 
 * @return string - The complete HTML for the receiver form
 * 
 * @example
 * $html = generateReceiverForm("Living Room", "192.168.1.100", 4, 0, 100, 1);
 * echo $html;
 */
function generateReceiverForm($receiverName, $deviceIp, $maxChannels, $minVolume, $maxVolume, $volumeStep) {
    // Retrieve current settings for the receiver
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
 * This function provides a centralized way to sanitize and validate various types of input data.
 * It supports different data types and provides type-specific validation, including range checking
 * for integers. This helps prevent injection attacks and ensures data integrity.
 * 
 * @param mixed $data - The input data to sanitize and validate
 * @param string $type - The type of data ('int' or 'ip' are currently supported)
 * @param array $options - Additional options for validation (e.g., 'min' and 'max' for integers)
 * 
 * @return mixed - The sanitized and validated data, or null if the input is invalid
 * 
 * @example
 * $cleanVolume = sanitizeInput($_POST['volume'], 'int', ['min' => 0, 'max' => 100]);
 * $cleanIp = sanitizeInput($_POST['ip_address'], 'ip');
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
 * This function provides a centralized way to log messages to a file. It includes
 * timestamps and log levels to aid in debugging and monitoring the application.
 * 
 * @param string $message - The message to log
 * @param string $level - The log level (e.g., 'error', 'info', 'debug')
 * 
 * @return void
 * 
 * @example
 * logMessage("API call failed for device 192.168.1.100", "error");
 * logMessage("User updated volume for Living Room receiver", "info");
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
