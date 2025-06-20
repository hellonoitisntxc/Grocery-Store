# 🛒 Online Grocery Store Web Application

A full-stack grocery store prototype built using **HTML/CSS/JavaScript**, **React**, **PHP**, and **MySQL**. This system allows users to browse grocery products, register and log in securely, and place orders. It also provides a RESTful API endpoint for administrative use.

## 🚀 Features

### ✅ User-Facing Functionality
- **Product Browsing**: Dynamic dropdown-based category browsing with product image and price display.
- **Registration**: React-based form with real-time client-side validation and secure server-side processing.
- **Login with CAPTCHA**: CAPTCHA validation to prevent bots, plus secure password authentication.
- **Order Placement**: Authenticated users can place orders with automatic data entry into the database.

### 🔐 Security
- **SQL Injection Prevention**: All DB interactions use prepared statements.
- **Password Hashing**: Secure password handling using `password_hash()` and `password_verify()`.
- **XSS Protection**: React's default encoding and careful backend sanitization.
- **CAPTCHA**: Prevents automated login attempts.
- **Session Handling**: Secure PHP sessions with session ID regeneration.

### 🧑‍💼 Admin / Manager Tools
- **RESTful API**: View order details via `GET /backend/api/getOrder.php?id=ORDER_ID`.

## 🧱 Tech Stack

- **Frontend**: HTML5, CSS3, JavaScript, React (Registration UI)
- **Backend**: PHP (API, login, register, order handling)
- **Database**: MySQL
- **Dev Environment**: XAMPP (Apache + MySQL)

## 🗃️ Database Schema

Included in [`sql/grocery_schema.sql`](sql/grocery_schema.sql):
- `users` — Stores registered user info with unique email constraint.
- `products` — Categorized grocery items with names, prices, and images.
- `orders` — Records of user-product transactions.

## 🖥️ Project Structure

grocery-store/
├── backend/
│ ├── api/
│ │ ├── getOrder.php
│ │ └── getProductsByCategory.php
│ ├── db.php
│ ├── login.php
│ ├── register.php
│ ├── order.php
│ └── logout.php
├── frontend/
│ ├── public/
│ │ ├── assets/ # Images (e.g., broccoli.jpg, beef.jpg)
│ │ ├── index.html # Main product browsing page
│ │ ├── login.html
│ │ └── register.html
│ └── react-src/ # React files for registration
├── sql/
│ └── grocery_schema.sql

## 🔄 Working Procedure Overview

1. **Browse Products**: Load `index.html`, select a category, view products and prices dynamically.
2. **Register**: Go to `register.html`, use the React form with live validation.
3. **Login**: Visit `login.html`, solve CAPTCHA, login securely.
4. **Place Order**: While logged in, select a product and click "Order".
5. **Logout**: Click "Logout" to end session.
6. **Manager View**: Use the REST API:  
GET http://localhost/grocery-store/backend/api/getOrder.php?id=5


## 🛠️ Suggested Improvements

- 🛒 Add a **Shopping Cart** for multi-item orders.
- 👨‍💼 Build a **Web Admin Panel** to manage users, products, and orders.
- 💳 Integrate a **Payment Gateway** (Stripe, PayPal).
- 🔒 Improve **Security** (CSRF protection, rate limiting, HTTPS).
- 🤖 Replace CAPTCHA with **Google reCAPTCHA** or server-generated dynamic images.
- 👤 Add user features: password reset, profile editing, order history.
- 🎨 Use **Bootstrap** or **Tailwind CSS** for better UI/UX and responsiveness.


## 📄 License

This project is for educational purposes and does not include actual payment or delivery systems.
