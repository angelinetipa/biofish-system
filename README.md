# BIO-FISH 🐟

**Bioplastic Formation Monitoring System**

A comprehensive web-based system for monitoring and controlling bioplastic production from fish scales. Built with modern claymorphism UI design.

## Features

- 🔍 Real-time process monitoring
- 📦 Material inventory management
- 💬 Quality feedback & assessment
- 🎛️ Machine control (pause, resume, stop)
- 📊 Production analytics
- 🧹 Automated cleaning mode

## Tech Stack

- **Frontend:** HTML5, CSS3 (Claymorphism), JavaScript
- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Server:** Apache (XAMPP)

## Installation

1. Clone this repository:
```bash
   git clone https://github.com/yourusername/biofish-system.git
```

2. Import the database:
   - Create a MySQL database named `biofish_db`
   - Import `database/biofish.sql`

3. Configure database connection:
   - Copy `config/database.example.php` to `config/database.php`
   - Update your database credentials

4. Start your server:
   - XAMPP: Start Apache and MySQL
   - Navigate to `http://localhost/biofish`

5. Login with demo credentials:
   - Admin: `admin` / `password123`
   - Operator: `operator1` / `password123`

## Project Structure
```
biofish/
├── assets/
│   ├── css/          # Stylesheets (claymorphism design)
│   └── js/           # JavaScript files
├── config/           # Configuration files
├── includes/         # Reusable components
├── pages/            # Application pages
│   ├── auth/         # Authentication
│   ├── dashboard/    # Main dashboard
│   ├── batches/      # Batch management
│   ├── materials/    # Inventory management
│   └── feedback/     # Quality feedback
└── database/         # Database schema
```

## Contributing

This is a thesis/capstone project. Contributions are welcome!

## License

MIT License - see LICENSE file for details

## Authors

- Your Name - Initial work

## Acknowledgments

- Built for sustainable bioplastic production research
- Claymorphism UI design inspired by modern web trends

## Initialize Git and Push to GitHub
```
Run these commands in your terminal (from your biofish folder):
bash
# Initialize Git repository
git init

# Add all files
git add .

# Create first commit
git commit -m "Initial commit: BIO-FISH Claymorphism UI"

# Add your GitHub repository as remote
git remote add origin https://github.com/yourusername/biofish-system.git

# Push to GitHub
git branch -M main
git push -u origin main
Note: Replace yourusername with your actual GitHub username!
```

## Future Updates - How to Push Changes
```
Whenever you make changes to your code:
bash
# Check what files changed
git status

# Add all changes
git add .

# Or add specific files
git add path/to/file.php

# Commit with a message
git commit -m "Description of what you changed"

# Push to GitHub
git push

# See commit history
git log

# See what changed in files
git diff

# Pull latest changes from GitHub
git pull

# Throw away your local changes & go back to recent git push
git fetch origin
git reset --hard origin/main

## Exporting Database
# mkdir database Create database folder

# Export database structure (without data)
mysqldump -u root -p --no-data biofish_db > database/schema.sql

# Or export with sample data
mysqldump -u root -p biofish_db > database/biofish_sample.sql
```
