# ⚙️ Campus Ticket System - Backend RESTful API

The backend RESTful API powering the Campus Ticket System. Built with **Laravel 11**, **PostgreSQL**, and **Laravel Sanctum**, containerized with **Docker** and deployed on **Render**.

📡 **Base URL:** [https://campus-ticket-api.onrender.com/api](https://campus-ticket-api.onrender.com/api)  
📑 **Interactive API Docs (Swagger):** [https://campus-ticket-api.onrender.com/api/documentation](https://campus-ticket-api.onrender.com/api/documentation)  
🌐 **Frontend Client:** [campus-ticket-web](https://github.com/ozlemsertkaya/campus-ticket-web)

---

## 🛠️ Architecture & Tech Stack

- **Framework:** Laravel 11 (PHP 8.2+)
- **Database:** PostgreSQL (Relational schema with strict foreign key constraints & sequences)
- **Authentication:** Laravel Sanctum (Bearer Token based)
- **Documentation:** L5-Swagger / OpenAPI 3.0 annotations
- **DevOps & Containerization:** Docker, Render Cloud Infrastructure

---

## 📌 Core API Endpoints

| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/register` | Register a new student/user | No |
| `POST` | `/api/login` | Authenticate user & return Bearer token | No |
| `GET` | `/api/tickets` | List tickets (with role-based filtering & pagination) | Yes |
| `POST` | `/api/tickets` | Submit a new ticket | Yes |
| `GET` | `/api/tickets/{id}` | Retrieve ticket details with relationships | Yes |
| `PUT` | `/api/tickets/{id}/status`| Update ticket lifecycle status | Yes (Agent) |
| `POST` | `/api/tickets/{id}/messages`| Add message/reply to a ticket conversation | Yes |

---

## 🚀 Local Development Setup

1. Clone repository:
   ```bash
   git clone [https://github.com/ozlemsertkaya/campus-ticket-api.git](https://github.com/ozlemsertkaya/campus-ticket-api.git)
   cd campus-ticket-api
