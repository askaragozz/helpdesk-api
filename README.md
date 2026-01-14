# Helpdesk API (Laravel)

A backend-only **Helpdesk / Ticketing System API** built with **Laravel** and **PostgreSQL**.  
Designed as a **pure REST API** to be consumed by external clients such as **WordPress**, admin panels, or frontend applications.

This project focuses on **clean backend architecture, role-based access control, and real-world ticketing workflows**.

---

## 🎯 Project Goals

- Build a **production-style API-only backend**
- Demonstrate strong backend fundamentals:
  - RESTful API design
  - Authentication & authorization
  - Role-based access control (RBAC)
  - Relational data modeling
  - Clean response shaping with API Resources
- Serve as a **portfolio project** for backend-focused roles

---

## 🛠 Tech Stack

- **PHP 8.3**
- **Laravel (API-only)**
- **PostgreSQL**
- **Laravel Sanctum** (token-based authentication)
- **Docker & Docker Compose**
- Git

---

## 📐 Architecture Decisions

- **API-only Laravel**
  - No Blade views or web routes
  - Designed for external consumption
- **Token-based authentication**
  - Stateless API using Laravel Sanctum
- **PostgreSQL**
  - Strong relational integrity and production-grade DB
- **Policy-based authorization**
  - Laravel Policies used for fine-grained permissions
- **API Resources**
  - Consistent, controlled JSON responses
  - No model leakage to clients

---

## 🧩 Core Domain

### User
- `id`
- `name`
- `email`
- `role` (`admin | agent | requester`)
- authentication tokens

### Ticket
- `id`
- `title`
- `description`
- `status` (`open | in_progress | resolved | closed`)
- `priority` (`low | medium | high`)
- `created_by`
- `assignee_id`
- timestamps

### Ticket Comment
- `id`
- `ticket_id`
- `user_id`
- `body`
- `visibility` (`public | internal`)
- timestamps

---

## 🔐 Authentication

This API uses **Laravel Sanctum** with **Bearer tokens**.

### Login
POST /api/v1/auth/login

Response:
```json
{
  "token": "plain-text-token"
}
```

### Using the token
Include the token in all authenticated requests:

Authorization: Bearer <token>  
Accept: application/json

---

## 👥 Roles & Permissions

| Role       | Capabilities |
|-----------|--------------|
| requester | Create tickets, view own tickets, add **public** comments |
| agent     | View assigned tickets, change status, add **internal & public** comments |
| admin     | Full access to all tickets, comments, and internal notes |

---

## 🔄 Ticket Lifecycle

open → in_progress → resolved → closed

---

## 📡 API Endpoints

### Authentication
- POST   /api/v1/auth/register
- POST   /api/v1/auth/login
- GET    /api/v1/auth/me
- POST   /api/v1/auth/logout

### Tickets
- GET    /api/v1/tickets
- POST   /api/v1/tickets
- GET    /api/v1/tickets/{id}
- PATCH  /api/v1/tickets/{id}/status

### Ticket Comments
- GET    /api/v1/tickets/{id}/comments
- POST   /api/v1/tickets/{id}/comments

---

## ⚙️ Local Setup (Without Docker)

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

---

## 🐳 Docker Setup (Recommended)

### Services
- Laravel API (PHP 8.3)
- PostgreSQL 16

### Environment
```env
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=helpdesk
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

### Run
```bash
docker compose up -d
docker compose exec app php artisan migrate
```

---

## 🚧 Future Improvements

- Feature tests (ticket lifecycle & permissions)
- OpenAPI / Swagger documentation
- Soft deletes for tickets
- SLA timestamps
- Status history tracking

---

## 📄 License

This project is open-source and intended for **educational and portfolio use**.
