# JAP Receiver Controls

## Overview

JAP Receiver Controls is a user-friendly web interface designed to manage and control multiple "Just Add Power" brand AV over IP receivers. This system allows for seamless control of both audio and video settings across various receivers in a network, making it an ideal solution for environments such as conference rooms, educational institutions, or any setting requiring centralized AV management.

![JAP Receiver Controls Logo](logo.png)

## Features

- **Multi-Receiver Management**: Control multiple "Just Add Power" receivers from a single interface.
- **Channel Selection**: Easily switch between channels for each receiver.
- **Volume Control**: Adjust volume levels for compatible receivers.
- **Responsive Design**: User-friendly interface that works on various devices and screen sizes.
- **Real-time Updates**: AJAX-powered forms for instant feedback without page reloads.
- **Error Handling**: Robust error management with user-friendly notifications.
- **Logging**: Comprehensive logging system for troubleshooting and auditing.

## System Requirements

- PHP 7.4 or higher
- Web server (e.g., Apache, Nginx)
- Network access to "Just Add Power" receivers

## Installation

1. Clone the repository to your web server's document root:
   ```
   git clone https://github.com/yourusername/jap-receiver-controls.git
   ```

2. Configure your web server to serve the project directory.

3. Copy `config.php.example` to `config.php` and update the settings:
   ```php
   const RECEIVERS = [
       "Rink Music" => "192.168.8.15",
       "Rink Video" => "192.168.8.22",
       // Add more receivers as needed
   ];

   const HOME_URL = 'http://your-home-page-url.com';
   ```

4. Ensure the `log` directory is writable by your web server.

5. Access the interface through your web browser.

## Usage

1. **Accessing the Interface**: Navigate to the URL where you've installed the JAP Receiver Controls.

2. **Controlling Receivers**: 
   - Each receiver is represented by a card on the interface.
   - Select the desired channel from the dropdown menu.
   - Adjust the volume using the slider (if supported by the receiver).
   - Click "Update" to apply the changes.

3. **Viewing Feedback**: After each action, a message will appear at the bottom of the page indicating success or failure.

4. **Returning Home**: Use the "Home" button at the bottom of the page to return to your main control panel or homepage.

## Configuration

The `config.php` file contains several important settings:

- `RECEIVERS`: An array of receiver names and their IP addresses.
- `MAX_VOLUME` and `MIN_VOLUME`: Set the volume range for receivers.
- `MAX_CHANNELS`: The maximum number of channels available.
- `HOME_URL`: The URL of your home page or main control panel.
- `LOG_FILE` and `LOG_LEVEL`: Configure logging behavior.
- `API_TIMEOUT`: Set the timeout for API calls to receivers.
- `VOLUME_CONTROL_MODELS`: List of receiver models that support volume control.

## Troubleshooting

- **Receiver Not Responding**: Ensure the IP address is correct in `config.php` and the receiver is powered on and connected to the network.
- **Volume Control Not Available**: Check if the receiver model is listed in the `VOLUME_CONTROL_MODELS` array in `config.php`.
- **Error Messages**: Check the log file specified in `config.php` for detailed error information.

## Contributing

Contributions to the JAP Receiver Controls project are welcome! Please follow these steps:

1. Fork the repository.
2. Create a new branch for your feature or bug fix.
3. Make your changes and commit them with clear, descriptive messages.
4. Push your changes to your fork.
5. Submit a pull request with a detailed description of your changes.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

## Acknowledgments

- Just Add Power for their AV over IP technology
- The open-source community for inspiration and resources

## Support

For support, please open an issue on the GitHub repository or contact the maintainer at support@example.com.

---

Developed with ❤️ by Seth Morrow
