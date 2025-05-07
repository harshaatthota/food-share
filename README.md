# 🍱 Food Share Project

Food Share is a web-based platform designed to facilitate food donations from restaurants and donors to people in need. The platform connects restaurants, volunteers, and donors in a simple and efficient way.

## 📦 Setup Instructions

Follow the steps below to set up the project on your local machine:

### 1. Install XAMPP

If you haven't already, [download and install XAMPP](https://www.apachefriends.org/index.html) for your operating system. This will install Apache server, PHP, and MySQL.

### 2. Move Project Folder

After installing XAMPP:

- Go to the directory where XAMPP is installed (usually `C:\xampp` on Windows).
- Open the `htdocs` folder.
- Place the entire `food-share` project folder inside the `htdocs` directory.

### 3. Start Apache and MySQL

- Open the XAMPP Control Panel.
- Start **Apache** and **MySQL**.

### 4. Create the Database

1. Open your browser and go to [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
2. Click on **Import**.
3. Upload the SQL files provided in the project (`food_share db`& `db events`) to create the required database, events and tables.
4. Make sure the database name matches the one used in your project code (`food_share`).

### 5. Insert Admin Login Manually

> ⚠️ There is no registration page for admins on the website, so admin login credentials need to be added manually.

1. Go to the **users** table in the `food_share` database via phpMyAdmin.
2. Click on **Insert**.
3. Add a default admin user. Example:
   - `username`: `admin`
   - `password`: `admin123`
   - *(You can set your own username and password)*

### 6. Open the Web Application

Now, open your browser and visit:http://localhost/foodshare/index.php

This will load the homepage of the Food Share project.

## ✅ Features

- Real-Time Food Donation & Booking System
- Role-Based User Dashboards
- Secure Registration with Unique ID System
- Profile Management with Media Upload
- OTP-based booking confirmation system
- Integrated Complaint System
- Basic chatbot

## 🤝 Contribution

If you'd like to contribute or improve this project, feel free to fork the repository and submit a pull request.

---

## 📧 Contact

If you face any issues or have suggestions, feel free to contact:

**HarshaVardhan Atthota**  
📧 harshavardhanatthota@gmail.com  




