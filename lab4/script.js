import axios from "axios";

const API_KEY = process.env.OPENWEATHER_API_KEY;

function getUserLocation() {
    showLoader();
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;
            fetchWeatherByCoords(lat, lon);
            history.pushState(null, '', '/weather/');
        }, error => {
            showError("Не вдалося отримати ваше місцезнаходження. " + error.message);
        });
    } else {
        showError("Ваш браузер не підтримує геолокацію");
    }
}

export const getWeather = async (city) => {
    try {
        const response = await axios.get(`https://api.openweathermap.org/data/2.5/weather?q=${city.city}&appid=${API_KEY}&units=metric&lang=uk`);
        return response.data;
    } catch (error) {
        console.error("Error fetching weather data");
        return null;
    }
};