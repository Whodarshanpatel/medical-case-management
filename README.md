# Jajal Medical Case Management Web Application

> Practical Assignment Submission for Software Developer Intern — Jajal Medical Services Pvt. Ltd.

A Medical Case Management Web Application built with **PHP, Laravel, MySQL, Laravel Blade, Laravel Breeze, and Laravel Sanctum**.

This application allows surgical case tracking, clinical reviews, implant specifications, and role-based workflows for healthcare administrators and reviewers.

---

## 1. Technology Stack

- **Backend Framework**: PHP 8.2+ & Laravel 11.x
- **Database**: MySQL 8.0 (production/local development) & SQLite in-memory (automated testing)
- **Frontend**: Laravel Blade, Tailwind CSS, Alpine.js, Vite
- **Web Authentication**: Laravel Breeze (Session & Cookie-based)
- **API Token Authentication**: Laravel Sanctum (Bearer Token)
- **Testing Suite**: PHPUnit (47 automated feature & unit tests, 163 assertions)
- **Version Control**: Git & GitHub

---

## 2. Key Features & Architecture

1. **Role-Based Authorization (Server-Enforced)**:
   - **Admin**: Full permissions to view, create, edit, change status, and delete case records.
   - **Viewer**: Read-only permissions to view cases and case details. Write operations are blocked with HTTP 403 server-side.
   - Centralized permissions managed via `App\Policies\MedicalCasePolicy`.
2. **Clinical Data Model**:
   - `case_number` (unique index, formatted e.g. `CAS-10001`)
   - `patient_reference` (anonymized dummy patient code, e.g. `PT-9011`)
   - `surgeon_name` (operating surgeon)
   - `implant_type` (implant system / prosthesis)
   - `status` (`Draft`, `Planning`, `In Review`, `Approved`, `Completed`, `Cancelled`)
   - `priority` (`Low`, `Medium`, `High`)
   - `surgery_date` (optional date)
   - `created_by` (foreign key referencing `users.id`, automatically set to the authenticated user on the server)
3. **Five Required Web Screens**:
   - **Login/Logout**: Clinical portal authentication with feedback and demo credential quick-reference.
   - **Dashboard**: Live database counts for Total, Planning, In Review, Approved, and Completed cases, plus recent cases.
   - **Case List**: Paginated table (10 per page), search by Case Number or Surgeon, status filter, preserved query parameters.
   - **Create / Edit Form**: Unified Blade form with field validation, old input preservation, and duplicate case number protection.
   - **Case Details**: Detailed clinical record, audit provenance (creator & timestamps), and role-conditional action buttons.
4. **Seven REST API Endpoints with Sanctum**:
   - `POST /api/login` (Public — issues Bearer token)
   - `POST /api/logout` (Protected — revokes Bearer token)
   - `GET /api/cases` (Protected — paginated list, search & status filter)
   - `POST /api/cases` (Protected — Admin only, creates case)
   - `GET /api/cases/{id}` (Protected — single case details or 404)
   - `PUT /api/cases/{id}` (Protected — Admin only, updates case)
   - `DELETE /api/cases/{id}` (Protected — Admin only, deletes case)
   - Consistent JSON envelope: `{"success": true|false, "message": "...", "data": ...}`

---

## 3. Demo Credentials

The database seeder pre-configures two demo accounts:

| Role | Email | Password | Permissions |
|------|-------|----------|-------------|
| **Admin** | `admin@jajalmedical.com` | `AdminPassword123!` | View, Create, Edit, Status Change, Delete |
| **Viewer** | `viewer@jajalmedical.com` | `ViewerPassword123!` | View case list and case details only (Read-Only) |

*Note: Pure dummy credentials for local review. Never use in production.*

---

## 4. Prerequisites

Before installing the application, ensure your environment has:

- **PHP** >= 8.2 with extensions: `curl`, `fileinfo`, `mbstring`, `openssl`, `pdo_mysql`, `pdo_sqlite`, `sqlite3`, `zip`
- **Composer** >= 2.x
- **Node.js** >= 18.x & **npm**
- **MySQL Server** >= 8.0 (running on port 3306)
- **Git**

---

## 5. Local Setup Instructions

Follow these steps to reproduce and run the application locally:

### Step 1: Clone the Repository
```bash
git clone https://github.com/nusrat-k-d/jajal-medical-case-management.git
cd jajal-medical-case-management
```

### Step 2: Install PHP & Node Dependencies
```bash
composer install
npm install
```

### Step 3: Configure Environment
Copy `.env.example` to `.env`:
```bash
cp .env.example .env
```
Generate an application key:
```bash
php artisan key:generate
```

Configure your MySQL database settings in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jajal_medical
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

### Step 4: Create Database, Run Migrations & Seed Data
Create the MySQL database if it doesn't already exist:
```sql
CREATE DATABASE IF NOT EXISTS jajal_medical;
```

Run migrations and database seeders:
```bash
php artisan migrate:fresh --seed
```
*This will create all schema tables and populate the 2 demo accounts and 25 realistic dummy cases.*

### Step 5: Build Frontend Assets
```bash
npm run build
```

### Step 6: Start Local Development Server
```bash
php artisan serve
```
The application will be live at: **`http://127.0.0.1:8000`**

---

## 6. Running Automated Tests

The test suite uses an **isolated in-memory SQLite database** configured in `phpunit.xml`, ensuring tests execute safely and rapidly without modifying your MySQL development records.

To run the complete test suite:
```bash
php artisan test
```

To run specific test classes:
```bash
php artisan test --filter=MedicalCaseWebTest
php artisan test --filter=MedicalCaseApiTest
```

**Test Suite Results**:
```
Tests:    47 passed (163 assertions)
Duration: ~4.5s
```

All 6 required assignment test scenarios are implemented and passing:
1. `test_valid_login_succeeds_and_issues_token` (Req 1)
2. `test_invalid_login_is_rejected` (Req 2)
3. `test_guest_cannot_access_protected_case_pages` & `test_guest_cannot_access_protected_case_apis` (Req 3)
4. `test_admin_can_create_a_case` & `test_admin_can_create_a_case_through_api` (Req 4)
5. `test_creating_case_with_duplicate_case_number_fails_with_422_through_api` (Req 5)
6. `test_viewer_cannot_delete_a_case_and_receives_403` (Req 6)

---

## 7. REST API Documentation & Usage

All protected endpoints expect a Bearer token in the `Authorization` header:
```http
Authorization: Bearer <your_token_here>
Accept: application/json
```

A complete Postman / Thunder Client collection is provided in:
`docs/jajal_medical_api_collection.json`

### Endpoints Overview

| Method | Endpoint | Access | Description | Status Code |
|--------|----------|--------|-------------|-------------|
| `POST` | `/api/login` | Public | Submit `{ "email", "password" }`, receive token | 200 / 401 |
| `POST` | `/api/logout` | Protected | Revoke active token | 200 |
| `GET` | `/api/cases` | Admin & Viewer | Paginated cases (`?page=1&per_page=10&status=Planning&search=Dr`) | 200 |
| `POST` | `/api/cases` | Admin only | Create case. Creator auto-assigned from token | 201 / 403 / 422 |
| `GET` | `/api/cases/{id}` | Admin & Viewer | Retrieve single case details | 200 / 404 |
| `PUT` | `/api/cases/{id}` | Admin only | Update case. Unique rule ignores current record | 200 / 403 / 404 / 422 |
| `DELETE` | `/api/cases/{id}` | Admin only | Delete case record | 200 / 403 / 404 |

### Example API Request (Login)
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"admin@jajalmedical.com","password":"AdminPassword123!"}'
```

### Example API Response
```json
{
  "success": true,
  "message": "Authenticated successfully.",
  "data": {
    "token": "1|p3xO2L78mN...plainTextToken",
    "token_type": "Bearer",
    "user": {
      "id": 1,
      "name": "Dr. Eleanor Vance (Admin)",
      "email": "admin@jajalmedical.com",
      "role": "Admin"
    }
  }
}
```

---

## 8. Documentation Artifacts

- **[Database Documentation](docs/database.md)**: Database schema, tables, columns, indexes, and relationships.
- **[Requirement Traceability](docs/traceability.md)**: Mapping of assignment requirements to implementation and tests.
- **[Development Progress](docs/progress.md)**: Development milestones and verification history.
- **[API Collection](docs/jajal_medical_api_collection.json)**: Importable Postman or Thunder Client API collection.

---

## 9. Assumptions and Known Limitations

1. **Dummy Data Only**: In compliance with healthcare confidentiality standards, all patient identifiers are synthetic strings (e.g., `PT-9011`), and no real Protected Health Information (PHI) is processed or stored.
2. **Single Server Session**: Web session driver defaults to `database`. For horizontal multi-server scaling, a centralized Redis store can be plugged in via `.env`.
3. **Audit Immutability**: The `created_by` field is strictly immutable upon creation and is always extracted from the authenticated session/token to prevent privilege escalation.
