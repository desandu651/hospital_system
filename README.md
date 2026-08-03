 🏥 Medi Lanka - Enterprise Hospital Management System (HMS) v2026

![Status](https://img.shields.io/badge/Status-Production%20Ready-success)
![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue)
![Database](https://img.shields.io/badge/Database-MySQL%208.x-orange)
![UI Framework](https://img.shields.io/badge/UI-Bootstrap%205%20%7C%20Dark%20Eye--Care-purple)
![Security](https://img.shields.io/badge/Security-Prepared%20Statements%20%7C%20Secure%20Sessions-red)

An advanced, high-performance **Enterprise Healthcare Intelligence Platform** engineered for modern medical command centers. It features an ultra-modern **Eye-Care Dark Mode UI** for login and high-contrast, clean professional light layouts for clinical modules to maximize efficiency during intensive hospital shifts.

---

## 📸 System Screenshots & Module Gallery

### 1. Secure Authentication Portal
| <img width="1920" height="1140" alt="login_screenshot" src="https://github.com/user-attachments/assets/e75986b6-974a-4cc2-bc4c-c14aadc3238e" />
 

### 2. Executive Command Center & Dashboard Analytics

| ![Dashboard Overview]<img width="1920" height="1198" alt="dashboard_overview" src="https://github.com/user-attachments/assets/e4014e6a-04b8-4d01-ba61-2ebd51ba1e2c" />
 |<img width="1920" height="1198" alt="dashboard_analytics" src="https://github.com/user-attachments/assets/2733cb91-e2df-40ad-a5c2-aa8753fc1678" />
 
### 3. Clinical Management Modules
| Patients Directory | Doctors Staff Directory |
| :---: | :---: |
| ![Pation Directory](<img width="1920" height="1198" alt="patients_directory" src="https://github.com/user-attachments/assets/230f533a-fa67-4dae-a02c-fef3a55e052d" />
) |
 | ![Doctors Staff]<img width="1920" height="1198" alt="doctors_staff" src="https://github.com/user-attachments/assets/e922fc1b-a3e3-4341-ade6-5643c3cbd876" />
 |

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
