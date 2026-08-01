# Medi Lanka - Enterprise Hospital Management System (HMS) v2026

![Status](https://img.shields.io/badge/Status-Production%20Ready-success)
![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue)
![Database](https://img.shields.io/badge/Database-MySQL%208.x-orange)
![UI Framework](https://img.shields.io/badge/UI-Bootstrap%205%20%7C%20Dark%20Eye--Care-purple)
![Security](https://img.shields.io/badge/Security-Prepared%20Statements%20%7C%20Secure%20Sessions-red)

An advanced, high-performance **Enterprise Healthcare Intelligence Platform** engineered for modern medical command centers. It features an ultra-modern **Eye-Care Dark Mode UI** to minimize physician eye strain during night shifts, coupled with real-time operational metrics and robust data security layers.

---

## 🏛️ System Architecture & Core Modules

- **Command Center Dashboard**: Real-time telemetry monitoring active patient influx, ICU bed occupancy rates, on-duty physician rosters, and critical emergency alerts.
- **Patient Registry & Admissions**: Comprehensive tracking of patient diagnostic logs, room/ward allocations, and medical progression states (Critical, Stable, Recovering).
- **Secure Authentication & Access Control**: Built with PHP Sessions and PDO/MySQLi Prepared Statements to mitigate SQL Injection and unauthorized privilege escalations.
- **Responsive Dark UI/UX**: Designed using advanced CSS variables, glassmorphic layout principles, and optimized typography (`Inter` & `Plus Jakarta Sans`) for seamless multi-device deployment.

---

## 🛠️ Technology Stack

| Layer | Technology Used |
| :--- | :--- |
| **Frontend** | HTML5, CSS3, JavaScript (ES6+), Bootstrap 5, Bootstrap Icons |
| **Typography** | Google Fonts (`Inter`, `Plus Jakarta Sans`) |
| **Backend** | PHP (Object-Oriented & Procedural Hybrid Architecture) |
| **Database** | MySQL (Managed via phpMyAdmin) |
| **Environment** | XAMPP (Apache HTTP Server) |

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
