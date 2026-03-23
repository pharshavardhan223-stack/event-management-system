# 🎟️ Event Management & Ticket Booking System

## 📌 Project Description
This is a web-based Event Management System that allows users to explore events, book tickets, and manage bookings. Admin users can create, update, and delete events.

---

## 🚀 Features

### 👤 User Features
- User Registration & Login
- Browse Events
- Book Tickets
- View My Bookings
- Cancel Tickets

### 🛠️ Admin Features
- Create Events
- Edit Events
- Delete Events
- View All Bookings

---

## 🧱 Technologies Used

- Frontend: HTML, CSS
- Backend: PHP
- Database: MySQL
- Server: XAMPP

---

## 📂 Project Structure
event_project/
│
├── db.php
├── login.php
├── register.php
├── dashboard.php
├── view_events.php
├── create_event.php
├── edit_event.php
├── delete_event.php
├── book_ticket.php
├── my_bookings.php
├── cancel_booking.php
├── style.css
└── uploads/

---

## ⚙️ Setup Instructions

1. Install XAMPP
2. Start Apache & MySQL
3. Move project to:
4. Open phpMyAdmin
5. Create database: `event_platform`
6. Import tables (users, events, bookings)
7. Run project:

---

## 🗄️ Database Tables

### Users Table
- id
- name
- email
- password
- role

### Events Table
- id
- title
- description
- date
- location
- price
- image

### Bookings Table
- id
- user_id
- event_id
- tickets
- section_numbers
- total_price
- payment_status

---

## 🎯 Future Improvements

- Online Payment Integration
- Email Notifications
- Mobile App Version
- Advanced UI (React)

---

## 👨‍💻 Author

**Harsha Vardhan**  
B.Tech Student | Data Analyst | Developer

---