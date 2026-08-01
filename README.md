# Medi Lanka - Hospital Management System 2026

![Status](https://img.shields.io/badge/Status-Production%20Ready-success)
![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue)
![Database](https://img.shields.io/badge/Database-MySQL%208.x-orange)
![UI Framework](https://img.shields.io/badge/UI-Bootstrap%205%20%7C%20Dark%20Eye--Care-purple)
![Security](https://img.shields.io/badge/Security-Prepared%20Statements%20%7C%20Secure%20Sessions-red)

An advanced, high-performance **Enterprise Healthcare Intelligence Platform** engineered for modern medical command centers. It features an ultra-modern **Eye-Care Dark Mode UI** to minimize physician eye strain during night shifts, coupled with real-time operational metrics and robust data security layers.

---

## ⚠️ IMPORTANT: Mandatory Server Requirement (XAMPP)

To run this system successfully, **XAMPP must be installed**, and both **Apache** and **MySQL** services **must be actively running** before accessing the platform in your browser. Without starting Apache and MySQL via the XAMPP Control Panel, the PHP scripts and database connections will fail.

---

## 🔑 Default Login Credentials

Use the following pre-configured administrative credentials to log directly into the command center portal:

- **Username / Staff ID:** `admin`
- **Password:** `admin123`

---

## 🚀 Key Features & Modules

- **Ultra-Modern 2026 UI/UX**: Clean, eye-care dark theme designed specifically to prevent eye fatigue during long hospital shifts.
- **Command Center Dashboard**: Real-time telemetry monitoring active patient influx, ICU bed occupancy rates, and on-duty physician rosters.
- **Secure Authentication**: Built with PHP Sessions and MySQLi Prepared Statements to mitigate SQL Injection and unauthorized access.
- **Patient & Ward Management**: Comprehensive tracking of patient diagnostic logs, room/ward allocations, and medical progression states.

---

## 🛠️ Technology Stack

| Layer | Technology Used |
| :--- | :--- |
| **Frontend** | HTML5, CSS3, JavaScript (ES6+), Bootstrap 5, Bootstrap Icons |
| **Typography** | Google Fonts (`Inter`, `Plus Jakarta Sans`) |
| **Backend** | PHP (Object-Oriented & Procedural Hybrid Architecture) |
| **Database** | MySQL (Managed via phpMyAdmin / XAMPP) |
| **Environment** | XAMPP (Apache HTTP Server & MySQL Database Server) |

---

## 📂 Project Directory Structure

```text
hospital_system/
│
├── index.php              # Secure Login Portal (Eye-Care Dark Theme)
├── dashboard.php          # Real-time Medical Command Center
├── db.php                 # Database Connection & Configuration Handler
├── logout.php             # Session Termination & Security Cleanup
└── assets/                # Stylesheets, Custom Scripts, and Media Files
