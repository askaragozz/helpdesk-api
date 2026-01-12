# Helpdesk API (Laravel)

A backend-only Helpdesk / Ticketing System built with **Laravel** and **PostgreSQL**.  
This project is designed as a pure REST API to be consumed by external clients (e.g. WordPress, admin panels, or frontend apps).

---

## 🎯 Project Goals

- Provide a clean, scalable **API-only backend**
- Demonstrate backend fundamentals:
  - RESTful API design
  - Authentication & authorization
  - Relational data modeling
  - Role-based access control
- Serve as a **portfolio project** for backend-focused roles

---

## 🛠 Tech Stack

- **PHP 8+**
- **Laravel (API-only)**
- **PostgreSQL**
- **Laravel Sanctum** (token-based authentication)
- Git for version control

---

## 📐 Architecture Decisions

- **API-only**  
  Web routes, Blade views, and frontend tooling removed.
- **Token-based authentication**  
  Laravel Sanctum is used for stateless API authentication.
- **PostgreSQL**  
  Chosen for relational integrity and production-grade usage.
- **Separation of concerns**  
  Controllers, services, and domain logic kept isolated.

---

## 🧩 Core Domain (Planned)

### User
- id
- name
- email
- password
- role (`admin | agent | customer`)

### Ticket
- id
- subject
- description
- status (`open | pending | resolved | closed`)
- priority (`low | medium | high`)
- customer_id
- assigned_agent_id

### Message
- id
- ticket_id
- sender_id
- body
- timestamps

---

## 🔐 Authentication (Planned Endpoints)

- `POST /api/auth/register`
- `POST /api/auth/login`
- `GET /api/auth/me`
- `POST /api/auth/logout`

---

## ⚙️ Setup (Local)

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
