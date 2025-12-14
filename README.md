# 🍽 AI Food Recipe Backend

**AI-powered backend** to generate personalized recipes and dish images using AI. Built with **Laravel, PostgreSQL, and OpenAI**.

[![PHP](https://img.shields.io/badge/PHP-8.x-blue)](https://www.php.net/)  
[![Laravel](https://img.shields.io/badge/Laravel-10-red)](https://laravel.com/)  
[![OpenAI](https://img.shields.io/badge/OpenAI-GPT4-orange)](https://openai.com/)  
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15-blue)](https://www.postgresql.org/)

---

## ✨ Features

- 🔑 **User Authentication:** Register & login  
- 🥗 Generate recipes by meal type, ingredients, and calorie limits  
- 🤖 **AI-powered:** Recipe generation with **GPT-4 / GPT-4 Turbo**  
- 🖼 **AI-generated images:** Dish visuals via **gpt-image-1 / DALL·E**  
- 🗂 RESTful API with clean, modular backend  
- 💾 Stores user and recipe data in PostgreSQL  

---

## ⚡ Quick Start

1. **Clone Repo**  

```bash
git clone https://github.com/natashamiskimovska/ai-food-recipe-back-end.git
cd ai-food-recipe-back-end
```
2. **Install Dependencies**  

```bash
composer install
```
3. **Configure Environment**
```bash
cp .env.example .env
# Add database credentials & OpenAI API key
php artisan key:generate
php artisan migrate
```
4. **Start Server**
```bash
php artisan serve
```

✅ Backend runs at http://localhost:8000

---

📬 API Endpoints
| Method | Endpoint           | Description                |
| ------ | ------------------ | -------------------------- |
| POST   | `/register`        | Register new user          |
| POST   | `/login`           | Login user                 |
| POST   | `/generate-recipe` | Generate AI recipe & image |
| GET    | `/recipes`         | List all recipes           |
| GET    | `/recipes/{id}`    | Get recipe details         |


📝 Demo Request Example
POST /generate-recipe

Request Body (JSON):
```bash
{
  "meal_type": "lunch",
  "ingredients": ["chicken", "broccoli", "rice"],
  "calories_limit": 600
}
```

Sample Response:
```bash
{
  "recipe": {
    "title": "Chicken & Broccoli Rice Bowl",
    "ingredients": [
      "200g chicken breast",
      "100g broccoli",
      "150g cooked rice",
      "1 tbsp olive oil",
      "Salt & pepper"
    ],
    "instructions": "1. Cook the chicken in olive oil until golden. 2. Steam the broccoli. 3. Mix with rice and season to taste.",
    "calories": 580
  },
  "image_url": "https://example.com/generated-dish-image.png"
}
```
---

🤝 **Contributing**

- Fork the repo
- Create a branch
- Submit a pull request
