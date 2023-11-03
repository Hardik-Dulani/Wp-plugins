<?php
/*
Plugin Name: Weather Plugin
Description: A WordPress plugin for displaying weather information.
Version: 1.0
Author: Hardik Dulani
*/


function weather_shortcode($atts) {
    // Load the city data from a JSON file or URL.
    $cityData = json_decode(file_get_contents('C:\Users\Hardik\Desktop\Non_Academics\Wordpress-practice\xampp\htdocs\wordpress\wp-content\plugins\weather-plugin\indian_cities_.json'), true);

    ob_start();
    ?>
    

    <div id="weather-results">
    <!-- Weather information will be displayed here -->
    <?php
    date_default_timezone_set("Asia/Kolkata");

    // Replace 'YOUR_API_KEY' with your actual OpenWeatherMap API key
    $apiKey = "0c9661493b748ff5d257f29caca6c5d9";

    // Replace '1273293' with the city ID you want to retrieve weather data for
    $cityId = isset($_GET['id']) ? $_GET['id'] : '1261481'; // Default to Delhi if no ID is provided


    $googleApiUrl = "http://api.openweathermap.org/data/2.5/weather?id=" . $cityId . "&units=metric&lang=en&appid=" . $apiKey;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response);

    $currentTime = time();
    ?>

    <h2><?php echo $data->name; ?> Weather Status</h2>
    <div class="time">
        <div><?php echo date("l h:i A", $currentTime); ?></div>
        <div><?php echo date("jS F, Y", $currentTime); ?></div>
        <div><?php echo ucwords($data->weather[0]->description); ?></div>
    </div>
    <div class="weather-forecast">
        <img src="http://openweathermap.org/img/w/<?php echo $data->weather[0]->icon; ?>.png" class="weather-icon" />
        <?php echo $data->main->temp_max; ?> &deg;C
        <span class="min-temperature"><?php echo $data->main->temp_min; ?>&deg;C</span>
    </div>
    <div class="time">
        <div>Humidity: <?php echo $data->main->humidity; ?>%</div>
        <div>Wind: <?php echo $data->wind->speed; ?> km/h</div>
    </div>
</div>
<div class="weather-widget">
        
        <form action="" method="get">
            <label for="city-select">Select a city: </label>
            <select name="id" id="city-select">
            <?php
            $CityData = array_reverse($cityData); // Reverse the city data array
            foreach ($CityData as $city) {
                echo '<option value="' . $city['id'] . '">' . $city['name'] . '</option>';
            }
            ?>
            </select>
            <div style="text-align: center; margin-top: 10px;"> <!-- Center-align the button -->
            <input type="submit" value="Show Weather">
            </div>
        </form>
    </div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#city-select').change(function() {
            var selectedCityId = $(this).val();
            console.log(selectedCityId);
            fetchWeather(selectedCityId);
        });

        function fetchWeather(cityId) {
            var apiKey = 'YOUR API from OPEN WEATHER MAP';

            $.ajax({
                url: 'http://api.openweathermap.org/data/2.5/weather',
                data: {
                    id: cityId,
                    appid: apiKey,
                    units: 'metric',
                },
                success: function(data) {
                    var temperature = data.main.temp;
                    var description = data.weather[0].description;
                    var html = '<p>Temperature: ' + temperature + '°C</p>';
                    html += '<p>Description: ' + description + '</p>';
                    $('#weather-results').html(html);
                },
                error: function() {
                    console.log('Failed to fetch weather data.');
                },
            });
        }
    });
</script>

    <?php

    // Your weather display code here

    return ob_get_clean();
}

add_shortcode('weather', 'weather_shortcode');
