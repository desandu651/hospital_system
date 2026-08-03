# 🏥 Medi Lanka - Enterprise Hospital Management System (HMS) v2026

![Status](https://img.shields.io/badge/Status-Production%20Ready-success)
![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue)
![Database](https://img.shields.io/badge/Database-MySQL%208.x-orange)
![UI Framework](https://img.shields.io/badge/UI-Bootstrap%205%20%7C%20Dark%20Eye--Care-purple)
![Security](https://img.shields.io/badge/Security-Prepared%20Statements%20%7C%20Secure%20Sessions-red)

An advanced, high-performance **Enterprise Healthcare Intelligence Platform** engineered for modern medical command centers. It features an ultra-modern **Eye-Care Dark Mode UI** for login and high-contrast, clean professional light layouts for clinical modules to maximize efficiency during intensive hospital shifts.

---

## 📸 System Screenshots & Module Gallery

*(Note: Ensure your actual image files are saved inside the `assets/images/` folder with matching filenames)*

### 1. Secure Authentication Portal
| Secure Login Interface |
| :---: |
| ![Login Portal](assets/images/login_screenshot.png) |

### 2. Executive Command Center & Dashboard Analytics
| Dashboard Overview & Quick Metrics | Financial Analytics & Calendar |
| :---: | :---: |
| ![Dashboard Overview](assets/images/dashboard_overview.png) | ![Dashboard Analytics](assets/images/dashboard_analytics.png) |

| Recent Appointments & System Audit Logs |
| :---: |
| ![Dashboard Activity](assets/images/dashboard_activity.png) |

### 3. Clinical Management Modules
| Patients Directory | Doctors Staff Directory |
| :---: | :---: |
| ![Patients Directory](assets/images/patients_directory.png) | ![Doctors Staff](assets/images/doctors_staff.png) |

| Appointments Queue | Doctor Working Schedules |
| :---: | :---: |
| ![Appointments Queue](assets/images/appointments.png) | ![Doctor Schedules](assets/images/schedules.png) |

| Clinical Prescriptions |
| :---: |
| ![Clinical Prescriptions](assets/images/prescriptions.png) |

### 4. Administration & Finance
| Patient Billing & Invoices |
| :---: |
| ![Billing & Invoices](assets/images/billing.png) |

---

## 🚀 Key Features & Modules

- **Ultra-Modern 2026 UI/UX**: Eye-care dark theme login paired with a clean, high-visibility medical command dashboard.
- **Real-time Telemetry & Analytics**: Live tracking of monthly revenue, patient influx, appointment statuses, and dynamic calendar scheduling.
- **Comprehensive Clinical Directory**: Complete management modules for Patients, Specialist Doctors, Clinical Schedules, and Prescriptions.
- **Billing & Invoice Automation**: Streamlined tracking of patient service fees, payment states (Paid, Unpaid, Partial), and financial logs.
- **Secure Authentication**: Built with PHP Sessions and MySQLi Prepared Statements to prevent SQL Injection and unauthorized access.

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
├── index.php                # Secure Login Portal (Eye-Care Dark Theme)
├── dashboard.php            # Real-time Medical Command Center Dashboard
├── manage_patients.php      # Patient Directory Management
├── manage_doctors.php       # Clinical Doctors Staff Registry
├── manage_appointments.php  # Appointment Queues & Booking Control
├── doctor_schedules.php     # Doctor Working Schedules & Time Slots
├── manage_prescriptions.php # Issued Medical Prescriptions Handler
├── billing.php              # Patient Billing & Invoices Ledger
├── db.php                   # Database Connection & Configuration Handler
├── logout.php               # Session Termination & Security Cleanup
└── assets/                  # Stylesheets, Custom Scripts, and Media Files
    └── images/              # System Screenshots Gallery
