# 🏠 Homeowner Names Parser

A Laravel application for parsing homeowner names from CSV files into structured data. 📊

## ✨ Features

- 📤 Upload CSV files containing homeowner names
- 🔍 Parse single and compound names (e.g., "Mr John Smith", "Mr and Mrs Smith")
- 🏷️ Extract titles, first names, and last names
- 👑 Handle various title formats (Mr, Mrs, Ms, Dr, Prof, Mister)
- ✅ Validate input data and log errors
- 📋 Display parsed results in a user-friendly table

## 🛠️ Requirements

- 🐘 PHP 8.3+
- 🚀 Laravel 12.x
- 📦 Node.js (for frontend assets)

## 🚀 Usage

1. Start the development server:
   ```bash
   composer run dev
   ```
2. 🌐 Navigate to `http://localhost:8000`
3. 📁 Upload a CSV file with homeowner names
4. 👀 View the parsed results

## 📄 CSV Format

The CSV file should have a header row with `homeowner` column:

```csv
homeowner,
Mr John Smith,
Mrs Jane Smith,
Dr & Mrs Joe Bloggs,
```

## 🧪 Testing

Run the test suite:

```bash
php artisan test
```

## 🏗️ Architecture

- **🎯 Controller**: Handles HTTP requests and responses
- **⚙️ Service**: Contains business logic for parsing names
- **✅ Form Request**: Validates file uploads
- **🎨 Blade Template**: Provides the user interface

## 🤖 AI Usage
* 💬 Used Claude Code for FE - I did this for speed, not something that I have done previously so decided to try
* 🚁 GitHub Copilot used via PHPStorm
* ⚡ Laravel Boost installed because I am currently experimenting with this

## 🚧 Enhancements
If I had more time I would:
  - 📖 Add pagination for large datasets
  - 🛡️ Improve error handling and user feedback
  - 🔬 Add more comprehensive tests
  - 🔐 Implement user authentication for secure access

## 🏆 Why use Laravel?
* ⚡ Speeds up development time
* 🎯 It is the way that I am used to working
* 🧱 Makes it easier to write SOLID code
