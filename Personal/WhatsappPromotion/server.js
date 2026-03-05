const express = require('express');
const fs = require('fs');
const cors = require('cors');
const bodyParser = require('body-parser');
const path = require('path');

const app = express();
const PORT = 3000;

app.use(cors());
app.use(bodyParser.json());

// Serve static HTML files
app.use(express.static(path.join(__dirname, 'public')));

const PROMO_FILE = './promotions.json';
const SCHEDULE_FILE = './scheduled.json';

// Ensure JSON files exist
if (!fs.existsSync(PROMO_FILE)) {
    fs.writeFileSync(PROMO_FILE, JSON.stringify([]));
}

if (!fs.existsSync(SCHEDULE_FILE)) {
    fs.writeFileSync(SCHEDULE_FILE, JSON.stringify([]));
}

/* ---------- PROMOTION ROUTES ---------- */

// Add Promotion
app.post('/add-promotion', (req, res) => {
    const promotions = JSON.parse(fs.readFileSync(PROMO_FILE));
    promotions.push(req.body);
    fs.writeFileSync(PROMO_FILE, JSON.stringify(promotions, null, 2));
    res.json({ message: "Promotion saved successfully" });
});

// Get Promotions
app.get('/promotions', (req, res) => {
    const promotions = JSON.parse(fs.readFileSync(PROMO_FILE));
    res.json(promotions);
});

/* ---------- SCHEDULE ROUTES ---------- */

// Add Schedule
app.post('/add-schedule', (req, res) => {
    const schedules = JSON.parse(fs.readFileSync(SCHEDULE_FILE));
    schedules.push(req.body);
    fs.writeFileSync(SCHEDULE_FILE, JSON.stringify(schedules, null, 2));
    res.json({ message: "Schedule saved successfully" });
});

// Get Schedules
app.get('/scheduled', (req, res) => {
    const schedules = JSON.parse(fs.readFileSync(SCHEDULE_FILE));
    res.json(schedules);
});

app.listen(PORT, () => {
    console.log(`Server running at http://localhost:${PORT}`);
});
