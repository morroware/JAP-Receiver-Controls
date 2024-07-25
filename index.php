<?php
/**
 * Castle AV Controls - Main Script
 * 
 * This script provides a user interface for controlling multiple music receivers
 * in the Castle Bowling and Rink facility. It allows users to select channels
 * and adjust volumes for each receiver.
 *
 * @Seth Morrow
 * @version 0.01.5
 */

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering to prevent any unwanted output
ob_start();

// Start the session to handle page reloads
session_start();

// Initialize $postResponse
$postResponse = '';

// Include configuration and utility files
$configFile = __DIR__ . '/config.php';
$utilsFile = __DIR__ . '/utils.php';

if (!file_exists($configFile) || !file_exists($utilsFile)) {
    die("Error: Required files not found. Please ensure config.php and utils.php exist in the same directory as this script.");
}

require_once $configFile;
require_once $utilsFile;

// Verify that the required functions are available
if (!function_exists('generateReceiverForm') || !function_exists('getCurrentChannel')) {
    die("Error: Required functions not found. Please check utils.php.");
}

// Check if required variables are set in config.php
if (!isset($RECEIVERS) || !isset($MAX_CHANNELS) || !isset($MIN_VOLUME) || !isset($MAX_VOLUME) || !isset($VOLUME_STEP)) {
    die("Error: Required configuration variables are not set. Please check config.php.");
}

// Check if the page should be reloaded after a POST request
if (isset($_SESSION['reload']) && $_SESSION['reload'] === true) {
    $_SESSION['reload'] = false;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $selectedChannel = sanitizeInput($_POST['channel'], 'int', ['min' => 1, 'max' => $MAX_CHANNELS]);
    $selectedVolume = sanitizeInput($_POST['volume'], 'int', ['min' => $MIN_VOLUME, 'max' => $MAX_VOLUME]);
    $deviceIp = sanitizeInput($_POST['receiver_ip'], 'ip');

    if ($selectedChannel && $selectedVolume && $deviceIp) {
        try {
            $channelResponse = makeApiCall('POST', $deviceIp, 'command/channel', $selectedChannel);
            $volumeResponse = makeApiCall('POST', $deviceIp, 'command/audio/stereo/volume', $selectedVolume);

            $postResponse .= "<h3>POST Responses:</h3>";
            $postResponse .= "<pre>Channel: " . htmlspecialchars($channelResponse) . "</pre>";
            $postResponse .= "<pre>Volume: " . htmlspecialchars($volumeResponse) . "</pre>";

            $_SESSION['reload'] = true;
            $postResponse .= "<script>setTimeout(function() { window.location.href = window.location.href; }, 3000);</script>";
        } catch (Exception $e) {
            $postResponse .= "<h3>Error:</h3><pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
            logMessage("Error setting channel/volume: " . $e->getMessage(), 'error');
        }
    } else {
        $postResponse .= "<h3>Error:</h3><pre>Invalid input data.</pre>";
        logMessage("Invalid input data received in POST request", 'error');
    }
}

// Clear the output buffer and start the HTML output
ob_end_clean();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Castle AV Controls</title>
    <style>
        :root {
            --bg-color: #121212;
            --text-color: #e0e0e0;
            --primary-color: #bb86fc;
            --secondary-color: #03dac6;
            --surface-color: #1e1e1e;
            --error-color: #cf6679;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        h1 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .receivers-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .receiver {
            background-color: var(--surface-color);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .receiver:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        }

        .receiver form {
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }

        button {
            background-color: var(--secondary-color);
            color: var(--bg-color);
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            font-weight: bold;
            margin-bottom: 15px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #04ebd2;
        }

        .receiver select, .receiver input[type='range'] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            background-color: var(--bg-color);
            color: var(--text-color);
            border: 1px solid var(--primary-color);
            border-radius: 5px;
        }

        .receiver select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23bb86fc' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 20px;
            padding-right: 40px;
        }

        .receiver .slider-label {
            text-align: center;
            margin-bottom: 10px;
            font-weight: bold;
            color: var(--primary-color);
        }

        input[type="range"] {
            -webkit-appearance: none;
            width: 100%;
            height: 10px;
            border-radius: 5px;
            background: #333;
            outline: none;
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        input[type="range"]:hover {
            opacity: 1;
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--secondary-color);
            cursor: pointer;
        }

        input[type="range"]::-moz-range-thumb {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--secondary-color);
            cursor: pointer;
        }

        .post-response {
            background-color: var(--surface-color);
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .post-response h3 {
            color: var(--primary-color);
            margin-top: 0;
        }

        .post-response pre {
            background-color: var(--bg-color);
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            h1 {
                font-size: 2em;
            }

            .receiver {
                padding: 15px;
            }
        }
    </style>
    <script>
        function updateVolumeLabel(slider) {
            var label = slider.parentElement.querySelector('.slider-label');
            label.textContent = 'Volume: ' + slider.value;
        }
    </script>
</head>
<body>
    <h1>Castle Bowling and Rink Music Control</h1>
    <div class="receivers-wrapper">
        <?php
        // Generate and output the receiver forms
        foreach ($RECEIVERS as $receiverName => $deviceIp) {
            try {
                echo generateReceiverForm($receiverName, $deviceIp, $MAX_CHANNELS, $MIN_VOLUME, $MAX_VOLUME, $VOLUME_STEP);
            } catch (Exception $e) {
                echo "<div class='receiver'><p>Error generating form for {$receiverName}: {$e->getMessage()}</p></div>";
                logMessage("Error generating form for {$receiverName}: " . $e->getMessage(), 'error');
            }
        }
        ?>
    </div>
    <?php
    // Output any POST response messages
    if (!empty($postResponse)) {
        echo "<div class='post-response'>{$postResponse}</div>";
    }
    ?>
</body>
</html>
