# PJ Collection - E-Commerce Clothing Store

A complete full-stack E-commerce website built with PHP and MySQL. It features a customer storefront and a comprehensive Admin Panel for managing products, orders, and users.

## 🚀 Features

### Customer Side
- **Responsive Design:** Works on mobile and desktop.
- **Shopping Cart:** Add, update, and remove items.
- **User Accounts:** Registration, Login, and Order History.
- **Carousel Slider:** Custom-built fading hero slider.
- **Checkout System:** Place orders with shipping details.

### Admin Panel
- **Dashboard:** View total sales, orders, and products.
- **Product Management:** Add, Edit, and Delete products (with image gallery support).
- **Order Management:** View order details and update status (Processing/Shipped/Delivered).
- **Customer Insights:** View registered users and their total spending.
- **Profile Management:** Update admin credentials and profile picture.

## 🛠️ Installation / How to Run

1. **Clone the repo** or download the ZIP.
2. Move the folder to your `htdocs` folder (XAMPP) or `www` folder (WAMP).
3. **Database Setup:**
   - Open phpMyAdmin.
   - Create a new database named `mi_clothing_db`.
   - Import the `mi_clothing_db.sql` file located in the root folder of this project.
4. **Configuration:**
   - Check `config/db.php` to ensure database credentials match your local setup.
5. **Run the site:**
   - Website: `http://localhost/E-commerce/`
   - Admin Panel: `http://localhost/E-commerce/admin/login.php`
   - **Admin Login:** Username: `hashir` | Password: `admin123`

## 💻 Tech Stack
- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP (Native)
- **Database:** MySQL