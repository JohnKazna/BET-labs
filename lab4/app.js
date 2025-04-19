import express from 'express';
import 'dotenv/config';
import { getWeather } from './script.js';

const app = express();
const PORT = process.env.PORT || 3000;

app.set('view engine', 'hbs');
app.use(express.static('public'));

app.get('/weather', (req, res) => {
    res.render('weather');
});

app.get('/weather/current', async (req, res) => {
    try {
        const weather = await getWeather({ city: "Tetiiv" });

        res.render('city', { weather });
    } catch (error) {
        console.error(error);
        res.status(500).render('error', { message: 'Помилка сервера' });
    }
});

app.get('/weather/:city', async (req, res) => {
    try {
        const weather = await getWeather({ city: req.params.city });
        if (!weather) {
            return res.status(404).render('error', { message: 'Місто не знайдено' });
        }
        res.render('city', { weather });
    } catch (error) {
        console.error(error);
        res.status(500).render('error', { message: 'Помилка сервера' });
    }
});

app.listen(PORT, () => {
    console.log('Server is running on http://localhost:3000');
});