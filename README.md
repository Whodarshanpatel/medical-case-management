# Medical Case Management System

> Practical Assignment Submission: Software Developer Intern — Jajal Medical Services Pvt. Ltd.  
> Developed & Maintained by **[Darshan Patel](https://github.com/Whodarshanpatel)**

A production-ready surgical case management platform developed with **Laravel 11, PHP 8.2+, MySQL, Blade, Laravel Breeze, and Laravel Sanctum**. 

The system centralizes surgical planning workflows, implant tracking, clinical peer evaluations, and server-enforced role access controls for clinical administrators and medical reviewers.

---

## 1. Technical Stack

- **Core Framework**: Laravel 11.x on PHP 8.2+
- **Database Engine**: MySQL 8.0 (production & development) | In-memory SQLite (isolated test runtime)
- **User Interface**: Laravel Blade, Tailwind CSS, Alpine.js, Vite
- **Session Auth**: Laravel Breeze (cookie & session-based web authentication)
- **API Token Auth**: Laravel Sanctum (stateful Bearer tokens)
- **Automated Testing**: PHPUnit (47 comprehensive feature/unit tests, 163 assertions)
- **Source Control**: Git & GitHub

---

## 2. Core Capabilities & System Design

1. **Server-Enforced Role-Based Access Control (RBAC)**:
   - **Admin**: Full authority to create, inspect, update, advance status, and remove case files.
   - **Viewer**: Read-only access across all cases. Any state-mutating requests trigger an HTTP `403 Forbidden` response.
   - Governed centrally using Laravel policies (`App\Policies\MedicalCasePolicy`).

2. **Clinical Data Architecture**:
   - `case_number`: Unique business index (e.g., `CAS-10001`).
   - `patient_reference`: Pseudonymized patient identifier (e.g., `PT-9011`).
   - `surgeon_name`: Lead operating surgeon.
   - `implant_type`: Specified prosthesis or surgical hardware.
   - `status`: Workflow progression (`Draft`, `Planning`, `In Review`, `Approved`, `Completed`, `Cancelled`).
   - `priority`: Urgency classification (`Low`, `Medium`, `High`).
   - `surgery_date`: Scheduled procedure date (optional).
   - `created_by`: Foreign key (`users.id`), locked to the authenticated user on creation.

3. **Core Web Interfaces**:
   - **Authentication Portal**: Login/logout interface with real-time validation and seeded demo credentials.
   - **Executive Dashboard**: Live metrics tracking Total, Planning, In-Review, Approved, and Completed cases alongside recent activity.
   - **Case Registry**: Paginated records table (10 items/page) with surgeon/case number search and status filtering.
   - **Unified Case Form**: Form handling creation and updates, complete with old input preservation and duplicate case validation.
   - **Case Review**: In-depth clinical file overview displaying audit metadata and role-tailored action buttons.

4. **RESTful API (Sanctum Secured)**:
   - `POST /api/login` — Public authentication endpoint; returns an API Bearer token.
   - `POST /api/logout` — Revokes the active session token.
   - `GET /api/cases` — Paginated case list with search and filter parameters.
   - `POST /api/cases` — Case creation (Admin restricted).
   - `GET /api/cases/{id}` — Single case retrieval.
   - `PUT /api/cases/{id}` — Case record updates (Admin restricted).
   - `DELETE /api/cases/{id}` — Case deletion (Admin restricted).
   - Structured JSON response envelope: `{"success": bool, "message": string, "data": mixed}`.

---

## 3. Demo Credentials

Seed data includes two default accounts for evaluation:

| Role | Email | Password | Access Scope |
|------|-------|----------|--------------|
| **Admin** | `admin@jajalmedical.com` | `AdminPassword123!` | Full read, write, update, and delete access |
| **Viewer** | `viewer@jajalmedical.com` | `ViewerPassword123!` | Read-only access to case lists and file details |

*Note: Provided strictly for evaluation in local environments.*

---

## 4. Environment Requirements

Ensure your machine meets the following minimum requirements:

- **PHP** >= 8.2 (Required extensions: `curl`, `fileinfo`, `mbstring`, `openssl`, `pdo_mysql`, `pdo_sqlite`, `sqlite3`, `zip`)
- **Composer** >= 2.x
- **Node.js** >= 18.x & **npm**
- **MySQL** >= 8.0 (default port 3306)
- **Git**

---

## 5. Local Installation & Setup

### Step 1: Clone the Repository
```bash
git clone [https://github.com/Whodarshanpatel/medical-case-management.git](https://github.com/Whodarshanpatel/medical-case-management.git)
cd medical-case-management
