# AI Companion for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/schmosbyy/ai-companion.svg?style=flat-square)](https://packagist.org/packages/schmosbyy/ai-companion)  
[![Total Downloads](https://img.shields.io/packagist/dt/schmosbyy/ai-companion.svg?style=flat-square)](https://packagist.org/packages/schmosbyy/ai-companion)

## Overview

**AI Companion** is a Laravel package that enables **natural language to SQL conversion**, executes queries **securely**, and intelligently determines whether to display results as an **HTML table** or **Chart.js visualization**.

This package is designed for developers who want **AI-powered data querying** within their Laravel applications while ensuring **security** and **ease of use**.

---

## Features

✅ Convert **natural language** into **SQL queries** using a local LLM (via Ollama)  
✅ Validate SQL queries to **prevent SQL injection**  
✅ Automatically select between **HTML tables** and **Chart.js graphs** for the best visualization  
✅ Provides a user-friendly **/ai-home** endpoint for executing queries  
✅ Supports **configurable AI models**, API endpoints, and execution timeout

---

## Installation

### Step 1: Install via Composer

```bash
composer require schmosbyy/ai-companion
```

### Step 2: Configure (Optional)

The package works out of the box with **default configurations**, but if you want to modify them, publish the config file:

```bash
php artisan vendor:publish --tag=ai-config
```

This will create the file:  
📄 `config/ai.php`

---

## Usage

### 1️⃣ Start the Local LLM Server

AI Companion requires **Ollama** running as an API server.  
Run the following command to **serve the LLM**:

```bash
ollama serve
```

Ensure that the required model is **downloaded** and available.  
To check available models, run:

```bash
ollama list
```

If the model is missing, download it with:

```bash
ollama pull qwen2.5-coder:14b
```

---

### 2️⃣ Use the AI Companion

Visit:
```
http://your-app.test/ai-home
```

Here, you can enter **natural language queries**, and the AI will:
1. **Convert it into SQL**
2. **Validate the SQL for security**
3. **Run the query safely**
4. **Determine the best visualization** (table or graph)

---

### 3️⃣ Customizing Configurations

By default, the package uses:

```php
return [
    'model' => env('AI_MODEL', 'qwen2.5-coder:14b'),
    'api_url' => env('AI_API_URL', 'http://127.0.0.1:11435/api/generate'),
    'timeout' => env('AI_TIMEOUT', 600),
];
```

If you want to **change the AI model** or **API URL**, update your `.env`:

```env
AI_MODEL=my-custom-llm
AI_API_URL=http://localhost:1234/api/generate
AI_TIMEOUT=300
```

---

## Security

🚨 **SQL Injection Protection**
- The package **only allows SELECT queries**
- It **strictly validates** the query before execution
- Malicious queries (INSERT, DELETE, DROP, etc.) **are blocked**

⚠️ **Additional Best Practices**
- Ensure your **database permissions** restrict unwanted changes
- Always **validate user inputs** before running AI-powered queries

---

## License

This project is licensed under the **Prosperity Public License 3.0.0**.  
🔹 **Non-commercial use is allowed freely**  
🔹 **Commercial use requires explicit permission**

Read the full [LICENSE](LICENSE) file for details.

---

## Credits & Attribution

This package is inspired by:
- Laravel's built-in **Query Builder**
- **Ollama** for local AI inference

🚀 **Happy Querying!**