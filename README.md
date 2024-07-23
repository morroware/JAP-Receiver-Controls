# Castle Audio/Visual Control System

The Castle Audio/Visual Control System is a web-based application designed to manage audio and video playback across multiple zones in a castle or similar large venue. This intuitive interface allows staff to easily control audio channels, volume levels, and HDMI signal routing using Just Add Power devices.

## Features

- Control audio and video for multiple zones
- Select from 4 different audio/video channels for each zone
- Adjust volume levels with an easy-to-use slider
- Compatible with Just Add Power HDMI transmitters and receivers for video routing
- Responsive design for various screen sizes
- Modern, dark-themed user interface

## Technology Stack

- PHP: Backend logic
- HTML5: Structure
- CSS3: Styling (with custom properties for theming)
- JavaScript: Interactive elements

## Setup

1. Clone this repository to your web server.
2. Ensure PHP is installed and configured on your server.
3. Update the `config.php` file (not provided in the current code snippet) with the correct IP addresses for your audio receivers and Just Add Power devices.
4. Access the application through your web browser.

## Usage

1. Open the application in a web browser.
2. For each zone:
   - Select the desired audio channel from the dropdown menu.
   - Adjust the volume using the slider.
   - Click "Set Channel & Volume" to apply the audio changes.
3. For video routing (using Just Add Power devices):
   - [Add specific instructions for video routing here]

## Customization

The application uses CSS custom properties (variables) for easy theming. You can modify the following variables in the `<style>` section of `index.php` to change the color scheme:

```css
:root {
    --bg-color: #121212;
    --text-color: #e0e0e0;
    --primary-color: #bb86fc;
    --secondary-color: #03dac6;
    --surface-color: #1e1e1e;
    --error-color: #cf6679;
}
```


## Just Add Power Integration

This system is compatible with Just Add Power HDMI transmitters and receivers, allowing for flexible and scalable video distribution alongside audio control. The integration enables:

- Routing of HDMI signals from multiple sources to multiple displays
- Seamless switching between video sources
- Control of both audio and video through a single interface



