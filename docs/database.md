# Jajal Medical Case Management — Database Schema Documentation

## 1. Schema Sketch & Entity-Relationship Diagram

```
+-----------------------------------------------------------+
|                           USERS                           |
+-----------------------------------------------------------+
| id                     : BIGINT UNSIGNED (PK, Auto-inc)   |
| name                   : VARCHAR(255)                     |
| email                  : VARCHAR(255) (UNIQUE)            |
| email_verified_at      : TIMESTAMP (NULLABLE)             |
| password               : VARCHAR(255)                     |
| role                   : VARCHAR(255) [Admin, Viewer]     |
| remember_token         : VARCHAR(100) (NULLABLE)          |
| created_at             : TIMESTAMP (NULLABLE)             |
| updated_at             : TIMESTAMP (NULLABLE)             |
+-----------------------------------------------------------+
                             |
                             | 1 (User / Creator)
                             |
                             | N (Cases created)
                             v
+-----------------------------------------------------------+
|                           CASES                           |
+-----------------------------------------------------------+
| id                     : BIGINT UNSIGNED (PK, Auto-inc)   |
| case_number            : VARCHAR(255) (UNIQUE, INDEXED)   |
| patient_reference      : VARCHAR(255)                     |
| surgeon_name           : VARCHAR(255)                     |
| implant_type           : VARCHAR(255)                     |
| status                 : VARCHAR(255) (INDEXED)           |
| priority               : VARCHAR(255)                     |
| surgery_date           : DATE (NULLABLE)                  |
| created_by             : BIGINT UNSIGNED (FK -> users.id) |
| created_at             : TIMESTAMP (NULLABLE)             |
| updated_at             : TIMESTAMP (NULLABLE)             |
+-----------------------------------------------------------+
```

---

## 2. Table Definitions

### 2.1 Table: `users`

Represents system users with authentication credentials and role assignments.

| Column | Data Type | Constraints & Indexes | Description |
|--------|-----------|-----------------------|-------------|
| `id` | BIGINT UNSIGNED | Primary Key, Auto Increment | Unique user identifier |
| `name` | VARCHAR(255) | NOT NULL | User full name |
| `email` | VARCHAR(255) | NOT NULL, UNIQUE Index | Login and notification email |
| `email_verified_at` | TIMESTAMP | NULLABLE | Timestamp of email verification |
| `password` | VARCHAR(255) | NOT NULL | Bcrypt/Argon2 hashed password |
| `role` | VARCHAR(255) | NOT NULL, DEFAULT `'Viewer'` | User authorization role (`Admin` or `Viewer`) |
| `remember_token` | VARCHAR(100) | NULLABLE | Remember-me authentication token |
| `created_at` | TIMESTAMP | NULLABLE | Record creation timestamp |
| `updated_at` | TIMESTAMP | NULLABLE | Record last update timestamp |

### 2.2 Table: `cases`

Represents surgical planning and implant medical cases.

| Column | Data Type | Constraints & Indexes | Description |
|--------|-----------|-----------------------|-------------|
| `id` | BIGINT UNSIGNED | Primary Key, Auto Increment | Unique medical case ID |
| `case_number` | VARCHAR(255) | NOT NULL, UNIQUE Index | Unique case tracking code (e.g., `CAS-10001`) |
| `patient_reference` | VARCHAR(255) | NOT NULL | Anonymized dummy patient reference (e.g., `PT-9011`) |
| `surgeon_name` | VARCHAR(255) | NOT NULL | Name of operating surgeon |
| `implant_type` | VARCHAR(255) | NOT NULL | Required implant system or prosthesis |
| `status` | VARCHAR(255) | NOT NULL, Index | Case status (`Draft`, `Planning`, `In Review`, `Approved`, `Completed`, `Cancelled`) |
| `priority` | VARCHAR(255) | NOT NULL | Priority level (`Low`, `Medium`, `High`) |
| `surgery_date` | DATE | NULLABLE | Scheduled or completed surgery date |
| `created_by` | BIGINT UNSIGNED | NOT NULL, Foreign Key (`users.id`), CASCADE on delete | ID of the authenticated user who created the record |
| `created_at` | TIMESTAMP | NULLABLE | Record creation timestamp |
| `updated_at` | TIMESTAMP | NULLABLE | Record last update timestamp |

### 2.3 System & Supporting Tables

- **`personal_access_tokens`**: Managed by Laravel Sanctum for API token issuance and revocation.
- **`sessions`**: Database session driver storage.
- **`password_reset_tokens`**: Secure password reset tokens.
- **`cache` / `jobs`**: Database caching and job queue tables.

---

## 3. Allowed Domain Values

### 3.1 Status Values
- `Draft`
- `Planning`
- `In Review`
- `Approved`
- `Completed`
- `Cancelled`

### 3.2 Priority Values
- `Low`
- `Medium`
- `High`

### 3.3 Roles
- `Admin`: Full permissions (View, Create, Edit, Status Change, Delete)
- `Viewer`: Read-only access (View case list and case details)

---

## 4. Eloquent Model Relationships

- **`User` Model (`app/Models/User.php`)**:
  - `cases()`: `HasMany(MedicalCase::class, 'created_by')`
  - Helper methods: `isAdmin(): bool`, `isViewer(): bool`

- **`MedicalCase` Model (`app/Models/MedicalCase.php`)**:
  - `creator()`: `BelongsTo(User::class, 'created_by')`
  - Fillable attributes: `case_number`, `patient_reference`, `surgeon_name`, `implant_type`, `status`, `priority`, `surgery_date`, `created_by`
  - Automatic cast: `surgery_date => date:Y-m-d`
