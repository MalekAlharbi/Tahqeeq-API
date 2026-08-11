# 🎯 Tahqeeq API (تحقيق) - Project & Task Management RESTful API

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Sanctum](https://img.shields.io/badge/Auth-Laravel%20Sanctum-red?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/docs/sanctum)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](LICENSE)

**Tahqeeq API** is a modern, high-performance RESTful API backend built for Kanban/Trello-style project and task management systems. Designed with clean architecture, enterprise security, dynamic position ordering, and role-based access control (RBAC).

---

## ✨ Key Features

- 🔐 **Authentication & Authorization**:
  - Token-based authentication using **Laravel Sanctum**.
  - Dual token transport support: `Authorization: Bearer <token>` or HTTP-only `token` cookie via custom middleware.
  - Role-Based Access Control (**RBAC**) with pivot roles: `owner`, `participant`, and `viewer`.

- 📋 **Kanban Project & Category Management**:
  - Projects management with multi-user collaboration.
  - Category lists with scoped position ordering (`position`) for smooth Kanban drag-and-drop integration.

- ✅ **Task Management & Position Reordering**:
  - Full CRUD operations on tasks.
  - Drag-and-drop reordering within the same category or across different category lists.
  - Task completion toggling and task assignment to project participants.

- 🏗️ **Clean Architecture & Best Practices**:
  - **Form Requests**: Decoupled validation logic from controllers.
  - **API Resources**: Clean data transformation and response formatting (`CategoryResource`, `TaskResource`).
  - **Standardized API Traits**: Consistent JSON envelope structure across all endpoints (`success`, `message`, `data`, `errors`).

---

## 🗄️ Database Schema & Architecture

```
User ◄───(Many-to-Many via user_projects with role)───► Project
                                                           │
                                                           ▼ (1-to-Many)
                                                       Category
                                                           │
                                                           ▼ (1-to-Many)
                                                         Task ◄───(Assigned To / Created By)─── User
```

### Pivot Roles (`user_projects`):
| Role | Permissions |
| :--- | :--- |
| **`owner`** | Full control: Create, Update, Delete Project, Manage Categories & Tasks |
| **`participant`** | Can create, update, reorder, and complete tasks & categories |
| **`viewer`** | Read-only access to projects, categories, and tasks |

---

## 🚀 API Endpoints Overview

### 🔑 Authentication
| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :---: |
| `POST` | `/api/register` | Register a new user | ❌ |
| `POST` | `/api/login` | Authenticate user & issue token / cookie | ❌ |
| `GET` | `/api/user` | Fetch authenticated user profile | ✅ |
| `PUT` | `/api/user/{id}` | Update authenticated user details | ✅ |
| `POST` | `/api/logout` | Revoke token and clear authentication cookie | ✅ |

### 📂 Projects
| Method | Endpoint | Description | Min Role |
| :--- | :--- | :--- | :---: |
| `GET` | `/api/projects` | List all projects for authenticated user | `viewer` |
| `POST` | `/api/projects` | Create a new project (caller becomes `owner`) | Authenticated |
| `PUT` | `/api/projects/{project}` | Update project details | `owner` |
| `DELETE` | `/api/projects/{project}` | Delete project | `owner` |

### 📁 Categories / Lists
| Method | Endpoint | Description | Min Role |
| :--- | :--- | :--- | :---: |
| `GET` | `/api/project/{project}` | Get all categories and tasks in a project | `viewer` |
| `POST` | `/api/category/{project}` | Create a category in project | `participant` |
| `PUT` | `/api/category/{category}` | Update category title / position | `participant` |
| `DELETE` | `/api/category/{category}` | Delete category and reorder remaining | `participant` |

### 🎯 Tasks
| Method | Endpoint | Description | Min Role |
| :--- | :--- | :--- | :---: |
| `GET` | `/api/category/{category}` | List all tasks in a category | `viewer` |
| `POST` | `/api/tasks/{category}` | Create a task in category | `participant` |
| `PUT` | `/api/tasks/title/{task}` | Update task title | `participant` |
| `PUT` | `/api/tasks/position/{task}` | Update task position / move category | `participant` |
| `PUT` | `/api/tasks/completed/{task}`| Toggle task completion state | `participant` |
| `DELETE` | `/api/tasks/{task}` | Delete task and adjust positions | `participant` |

---

## 🛠️ Local Installation & Setup

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/MalekAlharbi/Tahqeeq-API.git
   cd Tahqeeq-API
   ```

2. **Install PHP Dependencies**:
   ```bash
   composer install
   ```

3. **Configure Environment File**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Run Database Migrations & Seeders**:
   ```bash
   php artisan migrate
   ```

5. **Start Local Development Server**:
   ```bash
   php artisan serve
   ```
   The API will be available at `http://127.0.0.1:8000`.

---

## 🧪 Automated Testing

Run the automated test suite with PHPUnit / Artisan:
```bash
php artisan test
```

---

## 👨‍💻 Author & Maintainer

Developed by **Malek Alharbi**. Built with passion for clean code, robust backend engineering, and modern web APIs.
