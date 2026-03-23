# 🎟️ EventVault — Event Management & Ticket Booking System

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)
![XAMPP](https://img.shields.io/badge/Server-XAMPP-FB7A24?style=flat&logo=apache&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=flat)

> A full-featured web-based Event Management & Ticket Booking System with a luxury dark UI, QR code tickets, admin analytics, and printable tickets.

---

## 📸 Features Overview

### 👤 User Features
- ✅ User Registration & Login (with hashed passwords)
- 🗓️ Browse & Explore Events
- 👁️ Event Detail Page (full info before booking)
- 🎟️ Book Tickets (with live price calculator)
- 📱 My Tickets — view all bookings with QR codes
- 🖨️ Print / Download Ticket as PDF
- ❌ Cancel Bookings

### 🛠️ Admin Features
- ⚙️ Admin Dashboard with live stats (Events, Bookings, Users, Revenue)
- 🎤 Create Events (with image upload)
- ✏️ Edit Events
- 🗑️ Delete Events (with confirmation page)
- 🎟️ View All Bookings (with status)
- 👥 Manage Users (with booking count per user)
- 📈 Analytics Dashboard (charts: bookings per event, revenue, monthly trend, booking status)
- 🔐 Secret Admin Registration Page

### 🔒 Security
- Passwords hashed with `password_hash()` / `password_verify()`
- Session-based authentication on all protected pages
- Admin role check on all admin pages
- Booking cancellation restricted to ticket owner only

---

## 🧱 Tech Stack

| Layer | Technology |
|---|---|
| Frontend | HTML5, CSS3 (custom dark theme) |
| Backend | PHP 8.2 |
| Database | MySQL |
| Server | XAMPP (Apache) |
| Charts | Chart.js 4.4 |
| QR Code | qrcodejs (CDN) |
| Fonts | Playfair Display + DM Sans (Google Fonts) |

---

## 📂 Project Structure

```
event_project/
│
├── index.php               # Landing / Home page
├── login.php               # User login
├── register.php            # User registration
├── logout.php              # Session destroy
├── dashboard.php           # User dashboard
│
├── view_events.php         # Browse all events
├── event_detail.php        # Full event detail page ✨
├── book_ticket.php         # Ticket booking form
├── my_bookings.php         # User's tickets with QR codes
├── print_ticket.php        # Printable ticket with QR ✨
├── cancel_booking.php      # Cancel a booking
│
├── create_event.php        # Admin: create event
├── edit_event.php          # Admin: edit event
├── delete_event.php        # Admin: delete event
│
├── admin.php               # Admin panel dashboard
├── admin_booking.php       # Admin: all bookings table
├── admin_users.php         # Admin: all users table
├── admin_analytics.php     # Admin: analytics charts ✨
├── admin_register.php      # Secret admin setup (delete after use)
│
├── db.php                  # Database connection
├── test_db.php             # DB connection tester
├── style.css               # Global dark theme styles
└── uploads/                # Event banner images
```

---

## ⚙️ Setup Instructions

### 1. Install XAMPP
Download and install [XAMPP](https://www.apachefriends.org/) for Windows.

### 2. Start Services
Open XAMPP Control Panel → Start **Apache** and **MySQL**.

### 3. Clone / Copy Project
```bash
git clone https://github.com/pharshavardhan223-stack/event-management-system.git
```
Move the folder to:
```
C:\xampp\htdocs\event_project\
```

### 4. Create Database
Open [phpMyAdmin](http://localhost/phpmyadmin) → Create database: `event_platform`

### 5. Import Tables
Run these SQL queries in phpMyAdmin → SQL tab:

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    location VARCHAR(200),
    price DECIMAL(10,2) DEFAULT 0,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    tickets INT DEFAULT 1,
    section_numbers VARCHAR(255),
    total_price DECIMAL(10,2),
    payment_status ENUM('Paid', 'Cancelled') DEFAULT 'Paid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (event_id) REFERENCES events(id)
);
```

### 6. Configure Database Connection
Open `db.php` and update your credentials:
```php
$conn = mysqli_connect("localhost", "root", "", "event_platform");
```

### 7. Run the Project
Open your browser and go to:
```
http://localhost/event_project/
```

### 8. Create Admin Account
Go to:
```
http://localhost/event_project/admin_register.php
```
Enter the secret key, fill in your details, and create your admin account.

> ⚠️ **Delete `admin_register.php`** from your project folder after creating your admin account!

---

## 🗄️ Database Tables

### `users`
| Column | Type | Description |
|---|---|---|
| id | INT | Primary key |
| name | VARCHAR | Full name |
| email | VARCHAR | Unique email |
| password | VARCHAR | Hashed password |
| role | ENUM | `user` or `admin` |
| created_at | TIMESTAMP | Registration time |

### `events`
| Column | Type | Description |
|---|---|---|
| id | INT | Primary key |
| title | VARCHAR | Event name |
| description | TEXT | Event details |
| date | DATE | Event date |
| location | VARCHAR | Venue |
| price | DECIMAL | Ticket price (₹) |
| image | VARCHAR | Banner filename |

### `bookings`
| Column | Type | Description |
|---|---|---|
| id | INT | Primary key |
| user_id | INT | FK → users |
| event_id | INT | FK → events |
| tickets | INT | Number of tickets |
| section_numbers | VARCHAR | Auto-generated seat codes |
| total_price | DECIMAL | Tickets × price |
| payment_status | ENUM | `Paid` or `Cancelled` |
| created_at | TIMESTAMP | Booking time |

---

## 📈 Analytics Dashboard

The admin analytics page (`admin_analytics.php`) includes:
- 📊 **Bar Chart** — Bookings per event
- 🍩 **Doughnut Chart** — Confirmed vs Cancelled ratio
- 📈 **Line Chart** — Monthly booking trend
- 📉 **Horizontal Bar** — Revenue per event
- 🏆 **Leaderboard** — Top events by bookings

---

## 🎯 Future Improvements

- [ ] Online Payment Integration (Razorpay / Stripe)
- [ ] Email Confirmation after booking (PHPMailer)
- [ ] Search & Filter on Events page
- [ ] Mobile App Version
- [ ] Event Categories & Tags
- [ ] Waitlist for sold-out events

---

## 👨‍💻 Author

**Harsha Vardhan**
B.Tech Student | Data Analyst | Developer

---
