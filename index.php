<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Castle Music Control</title>
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
        <div class='receiver'><form method='POST'><button type='button'>Bowling Music</button><select id='channel_Bowling Music' name='channel'><option value='1'>Channel 1</option><option value='2'>Channel 2</option><option value='3' selected>Channel 3</option><option value='4'>Channel 4</option></select><div class='slider-label'>Volume: 4</div><input type='range' name='volume' min='0' max='11' step='1' value='4' oninput='updateVolumeLabel(this)'><input type='hidden' name='receiver_ip' value='192.168.8.16'><button type='submit'>Set Channel & Volume</button></form></div><div class='receiver'><form method='POST'><button type='button'>Rink Music</button><select id='channel_Rink Music' name='channel'><option value='1'>Channel 1</option><option value='2'>Channel 2</option><option value='3' selected>Channel 3</option><option value='4'>Channel 4</option></select><div class='slider-label'>Volume: 11</div><input type='range' name='volume' min='0' max='11' step='1' value='11' oninput='updateVolumeLabel(this)'><input type='hidden' name='receiver_ip' value='192.168.8.25'><button type='submit'>Set Channel & Volume</button></form></div>    </div>
    </body>
</html>
