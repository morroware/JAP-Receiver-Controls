<?php
/**
 * Castle API Endpoint Tester - Main Script
 * 
 * This script provides a user interface for controlling multiple music receivers
 * in the Castle Bowling and Rink facility. It allows users to select channels
 * and adjust volumes for each receiver.
 *
 * @author Your Name
 * @version 1.4
 */

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include configuration and utility files
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/utils.php';

// Handle AJAX form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $response = array('success' => false, 'message' => '');

    $selectedChannel = sanitizeInput($_POST['channel'], 'int', ['min' => 1, 'max' => $MAX_CHANNELS]);
    $deviceIp = sanitizeInput($_POST['receiver_ip'], 'ip');

    if ($selectedChannel && $deviceIp) {
        try {
            $channelResponse = setChannel($deviceIp, $selectedChannel);
            $response['message'] .= "Channel: " . ($channelResponse ? "Successfully updated" : "Update failed") . "\n";

            if (supportsVolumeControl($deviceIp)) {
                $selectedVolume = sanitizeInput($_POST['volume'], 'int', ['min' => $MIN_VOLUME, 'max' => $MAX_VOLUME]);
                if ($selectedVolume) {
                    $volumeResponse = setVolume($deviceIp, $selectedVolume);
                    $response['message'] .= "Volume: " . ($volumeResponse ? "Successfully updated" : "Update failed") . "\n";
                }
            }

            $response['success'] = true;
        } catch (Exception $e) {
            $response['message'] = "Error: " . $e->getMessage();
            logMessage("Error updating settings: " . $e->getMessage(), 'error');
        }
    } else {
        $response['message'] = "Invalid input data.";
        logMessage("Invalid input data received in POST request", 'error');
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
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

        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo {
            max-width: 200px;
            height: auto;
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
            margin-top: 15px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #04ebd2;
        }

        .receiver-title {
            background-color: var(--primary-color);
            color: var(--bg-color);
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: default;
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 15px;
            width: 100%;
            text-align: center;
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

        .warning {
            color: var(--error-color);
            font-weight: bold;
        }

        .home-button {
            display: block;
            width: 200px;
            margin: 30px auto;
            padding: 15px 20px;
            background-color: var(--primary-color);
            color: var(--bg-color);
            text-align: center;
            text-decoration: none;
            font-size: 1.2em;
            font-weight: bold;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .home-button:hover {
            background-color: #cf96fc;
        }

        #response-message {
            background-color: var(--surface-color);
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: none;
        }

        #response-message.success {
            border: 2px solid var(--secondary-color);
        }

        #response-message.error {
            border: 2px solid var(--error-color);
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

            .home-button {
                width: 80%;
            }

            .logo {
                max-width: 150px;
            }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateVolumeLabel(slider) {
            var label = slider.parentElement.querySelector('.volume-label');
            label.textContent = slider.value;
        }

        $(document).ready(function() {
            $('.receiver form').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                $.ajax({
                    url: '',
                    type: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        var messageDiv = $('#response-message');
                        messageDiv.removeClass('success error').addClass(response.success ? 'success' : 'error');
                        messageDiv.text(response.message).show();
                        setTimeout(function() {
                            messageDiv.hide();
                        }, 5000);
                    },
                    error: function() {
                        $('#response-message').removeClass('success').addClass('error')
                            .text("An error occurred. Please try again.").show();
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div class="logo-container">
        <img src="logo.png" alt="Castle AV Controls Logo" class="logo">
    </div>
    <h1>Castle AV Controls</h1>
    <div class="receivers-wrapper">
        <?php
        // Generate and output the receiver forms
        foreach ($RECEIVERS as $receiverName => $deviceIp) {
            try {
                echo generateReceiverForm($receiverName, $deviceIp, $MAX_CHANNELS, $MIN_VOLUME, $MAX_VOLUME, $VOLUME_STEP);
            } catch (Exception $e) {
                echo "<div class='receiver'><p class='warning'>Error generating form for {$receiverName}: {$e->getMessage()}</p></div>";
                logMessage("Error generating form for {$receiverName}: " . $e->getMessage(), 'error');
            }
        }
        ?>
    </div>
    <div id="response-message"></div>
    <a href="http://192.168.8.127" class="home-button">Home</a>
</body>
</html>
