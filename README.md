# Castle API Endpoint Tester

## Table of Contents
1. [Introduction](#introduction)
2. [Features](#features)
3. [Requirements](#requirements)
4. [Installation](#installation)
5. [Configuration](#configuration)
6. [Usage](#usage)
7. [File Structure](#file-structure)
8. [Function Documentation](#function-documentation)
9. [Troubleshooting](#troubleshooting)
10. [Contributing](#contributing)
11. [License](#license)

## Introduction

The japcontrols is a web-based application designed to interact with and control Just Add Power audio receivers. This tool provides a user-friendly interface for managing multiple audio receivers, allowing users to adjust volume levels and select audio channels for each device. The application communicates with the receivers using a RESTful API, making it easy to integrate with existing audio systems.

This project is particularly useful for any venue where multiple audio zones need to be controlled from a central interface. It offers a simple yet powerful way to manage audio settings across multiple devices simultaneously.

## Features

- **Multi-Device Management**: Control multiple Just Add Power audio receivers from a single interface.
- **Volume Control**: Adjust the volume of each receiver individually.
- **Channel Selection**: Choose the active audio channel for each receiver.
- **Real-Time Updates**: The interface reflects the current state of each receiver, including volume levels and active channels.
- **Error Handling**: Robust error handling ensures the application remains functional even if a device is unresponsive.
- **Logging**: Comprehensive logging system for tracking operations and troubleshooting.
- **Security**: Input sanitization to prevent injection attacks and ensure data integrity.

## Requirements

- PHP 7.0 or higher
- Web server (e.g., Apache, Nginx)
- cURL PHP extension enabled
- Network access to Just Add Power audio receivers

## Installation

1. Clone this repository to your web server's document root:
   ```
   git clone https://github.com/morroware/japcontrols.git
   ```

2. Ensure that the web server has write permissions for the log file:
   ```
   chmod 666 /path/to/japcontrols/app.log
   ```

3. Configure your web server to serve the project directory.

## Configuration

1. Open `config.php` in a text editor.
2. Modify the `$RECEIVERS` array to include your Just Add Power audio receivers:
   ```php
   $RECEIVERS = array(
       "Bowling Alley" => "192.168.1.100",
       "Main Rink" => "192.168.1.101",
       "Cafe Area" => "192.168.1.102",
   );
   ```
3. Adjust other configuration variables as needed:
   ```php
   $MAX_VOLUME = 100;
   $MIN_VOLUME = 0;
   $VOLUME_STEP = 1;
   $MAX_CHANNELS = 4;
   ```

## Usage

1. Navigate to the application URL in your web browser (e.g., `http://your-server.com/JAPcontrols/`).
2. You will see a list of configured receivers, each with its own control panel.
3. For each receiver:
   - Use the dropdown menu to select the desired audio channel.
   - Use the slider to adjust the volume.
   - Click the "Set Channel & Volume" button to apply the changes.
4. The interface will update to reflect the current state of each receiver.

## File Structure

- `index.php`: The main entry point of the application. It includes the HTML structure and form handling logic.
- `config.php`: Contains configuration variables, including the list of receivers and their IP addresses.
- `utils.php`: Houses utility functions for API communication, form generation, and other helper functions.
- `app.log`: Log file for tracking application events and errors.

## Function Documentation

### makeApiCall($method, $deviceIp, $endpoint, $data = null)
Handles API calls to the Castle devices.
- `$method`: HTTP method (GET, POST, etc.)
- `$deviceIp`: IP address of the target device
- `$endpoint`: Specific API endpoint
- `$data`: Optional data for POST requests

### getCurrentVolume($deviceIp)
Retrieves the current volume setting from a device.
- `$deviceIp`: IP address of the target device
- Returns: Integer representing the current volume (0-100)

### getCurrentChannel($deviceIp)
Retrieves the current channel setting from a device.
- `$deviceIp`: IP address of the target device
- Returns: Integer representing the current channel

### generateReceiverForm($receiverName, $deviceIp, $maxChannels, $minVolume, $maxVolume, $volumeStep)
Generates the HTML for a receiver control form.
- `$receiverName`: Name of the receiver (for display)
- `$deviceIp`: IP address of the receiver
- `$maxChannels`: Maximum number of channels
- `$minVolume`: Minimum volume level
- `$maxVolume`: Maximum volume level
- `$volumeStep`: Step size for volume adjustment
- Returns: HTML string for the receiver form

### sanitizeInput($data, $type, $options = [])
Sanitizes and validates input data.
- `$data`: Input data to sanitize
- `$type`: Type of data ('int' or 'ip')
- `$options`: Additional validation options
- Returns: Sanitized data or null if invalid

### logMessage($message, $level = 'info')
Logs messages to the application log file.
- `$message`: Message to log
- `$level`: Log level (e.g., 'error', 'info')

## Troubleshooting

- **Receiver Not Responding**: Ensure the IP address is correct in `config.php` and the device is powered on and connected to the network.
- **Volume Not Updating**: Check the network connection and verify that the API endpoint for volume control is correct.
- **Channel Selection Fails**: Confirm that the selected channel is within the range supported by the receiver.
- **PHP Errors**: Make sure your PHP version meets the minimum requirements and all necessary extensions are enabled.

## Contributing

Contributions to the Castle API Endpoint Tester are welcome! Please follow these steps:

1. Fork the repository.
2. Create a new branch for your feature or bug fix.
3. Make your changes and commit them with clear, descriptive messages.
4. Push your changes to your fork.
5. Submit a pull request with a detailed description of your changes.



