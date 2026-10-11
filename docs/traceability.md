# Jajal Medical Case Management — Requirement Traceability Matrix

This document maps requirements directly to their concrete implementation files and verification test cases in the test suite.

---

## Traceability Mappings

### REQ-001: Admin can create a case
* **Requirement**: Authenticated Administrators can create surgical cases through both the web portal and the REST API. Creator ID is automatically attributed to the authenticated user on the server. Viewers are forbidden from creating cases.
* **Implementation**:
  - Model & Migration:
    - [MedicalCase.php](file:///c:/Users/nusra/Desktop/jajal-medical-case-management/app/Models/MedicalCase.php)
    - [2026_10_11_100000_create_cases_table.php](file:///c:/Users/nusra/Desktop/jajal-medical-case-management/database/migrations/2026_10_11_100000_create_cases_table.php)
  - Policy & Request:
    - [MedicalCasePolicy.php](file:///c:/Users/nusra/Desktop/jajal-medical-case-management/app/Policies/MedicalCasePolicy.php#L32-L37) (`create()` checks `$user->isAdmin()`)
    - [StoreMedicalCaseRequest.php](file:///c:/Users/nusra/Desktop/jajal-medical-case-management/app/Http/Requests/StoreMedicalCaseRequest.php)
  - Controllers:
    - [CaseController.php](file:///c:/Users/nusra/Desktop/jajal-medical-case-management/app/Http/Controllers/CaseController.php#L55-L66) (`store()` assigns `$validated['created_by'] = $request->user()->id`)
    - [CaseApiController.php](file:///c:/Users/nusra/Desktop/jajal-medical-case-management/app/Http/Controllers/Api/CaseApiController.php#L53-L69) (`store()` returns HTTP 201)
  - Views:
    - [resources/views/cases/form.blade.php](file:///c:/Users/nusra/Desktop/jajal-medical-case-management/resources/views/cases/form.blade.php)
* **Verification**:
  - Web Test: `Tests\Feature\MedicalCaseWebTest::test_admin_can_create_a_case`
  - Web Forbidden Test: `Tests\Feature\MedicalCaseWebTest::test_viewer_cannot_create_a_case_and_receives_403`
  - API Test: `Tests\Feature\MedicalCaseApiTest::test_admin_can_create_a_case_through_api`
  - API Forbidden Test: `Tests\Feature\MedicalCaseApiTest::test_viewer_cannot_create_or_update_cases_through_api`

---

### REQ-002: Case Number must be unique
* **Requirement**: Each clinical case must have a unique `case_number`. Attempting to register a duplicate case number must be rejected with validation errors (HTTP 422 in API). When updating an existing case, the uniqueness check must ignore the current case record.
* **Implementation**:
  - Database Schema:
    - [2026_10_11_100000_create_cases_table.php](file:///c:/Users/nusra/Desktop/jajal-medical-case-management/database/migrations/2026_10_11_100000_create_cases_table.php#L16) (`$table->string('case_number')->unique()`)
  - Form Requests:
    - [StoreMedicalCaseRequest.php](file:///c:/Users/nusra/Desktop/jajal-medical-case-management/app/Http/Requests/StoreMedicalCaseRequest.php#L26) (`'case_number' => ['required', 'string', 'max:50', 'unique:cases,case_number']`)
    - [UpdateMedicalCaseRequest.php](file:///c:/Users/nusra/Desktop/jajal-medical-case-management/app/Http/Requests/UpdateMedicalCaseRequest.php#L35-L39) (`Rule::unique('cases', 'case_number')->ignore($caseId)`)
  - Exception Handler:
    - [bootstrap/app.php](file:///c:/Users/nusra/Desktop/jajal-medical-case-management/bootstrap/app.php#L48-L56) (catches `ValidationException` and renders structured 422 JSON)
* **Verification**:
  - API Duplicate Test: `Tests\Feature\MedicalCaseApiTest::test_creating_case_with_duplicate_case_number_fails_with_422_through_api`
  - API Update Self-Ignore Test: `Tests\Feature\MedicalCaseApiTest::test_admin_can_update_a_case_through_api`
  - Web Update Self-Ignore Test: `Tests\Feature\MedicalCaseWebTest::test_admin_can_update_a_case`

---

### REQ-003: Only Admin can delete cases
* **Requirement**: Case deletion is strictly restricted to Administrators. A Viewer attempting to delete a case must be blocked server-side and receive an HTTP 403 Forbidden response without modifying the database.
* **Implementation**:
  - Policy:
    - [MedicalCasePolicy.php](file:///c:/Users/nusra/Desktop/jajal-medical-case-management/app/Policies/MedicalCasePolicy.php#L50-L55) (`delete()` returns `$user->isAdmin()`)
  - Controllers:
    - [CaseController.php](file:///c:/Users/nusra/Desktop/jajal-medical-case-management/app/Http/Controllers/CaseController.php#L107-L117) (`Gate::authorize('delete', $case)`)
    - [CaseApiController.php](file:///c:/Users/nusra/Desktop/jajal-medical-case-management/app/Http/Controllers/Api/CaseApiController.php#L125-L147) (`Gate::authorize('delete', $case)`)
  - Views:
    - [resources/views/cases/index.blade.php](file:///c:/Users/nusra/Desktop/jajal-medical-case-management/resources/views/cases/index.blade.php#L182-L191) (`@can('delete', $case)` conditionally displays delete button)
    - [resources/views/cases/show.blade.php](file:///c:/Users/nusra/Desktop/jajal-medical-case-management/resources/views/cases/show.blade.php#L42-L51) (`@can('delete', $case)` conditionally displays delete button)
* **Verification**:
  - Web Viewer Delete Blocked: `Tests\Feature\MedicalCaseWebTest::test_viewer_cannot_delete_a_case_and_receives_403`
  - Web Admin Delete Allowed: `Tests\Feature\MedicalCaseWebTest::test_admin_can_delete_a_case`
  - API Viewer Delete Blocked: `Tests\Feature\MedicalCaseApiTest::test_viewer_cannot_delete_a_case_and_receives_403`
  - API Admin Delete Allowed: `Tests\Feature\MedicalCaseApiTest::test_admin_can_delete_case_through_api`

---

### REQ-004: Guest access restriction and authentication
* **Requirement**: Unauthenticated guests cannot access protected case management pages or API endpoints. Invalid login attempts produce clear feedback, while valid credentials issue a secure web session or API token.
* **Implementation**:
  - Web Middleware & Routes:
    - [routes/web.php](file:///c:/Users/nusra/Desktop/jajal-medical-case-management/routes/web.php) (`Route::middleware('auth')`)
  - API Middleware & Routes:
    - [routes/api.php](file:///c:/Users/nusra/Desktop/jajal-medical-case-management/routes/api.php) (`Route::middleware('auth:sanctum')`)
  - Auth Controllers:
    - [AuthenticatedSessionController.php](file:///c:/Users/nusra/Desktop/jajal-medical-case-management/app/Http/Controllers/Auth/AuthenticatedSessionController.php)
    - [AuthController.php](file:///c:/Users/nusra/Desktop/jajal-medical-case-management/app/Http/Controllers/Api/AuthController.php)
* **Verification**:
  - Web Guest Blocked: `Tests\Feature\MedicalCaseWebTest::test_guest_cannot_access_protected_case_pages`
  - API Guest Blocked: `Tests\Feature\MedicalCaseApiTest::test_guest_cannot_access_protected_case_apis`
  - Valid Login: `Tests\Feature\MedicalCaseApiTest::test_valid_login_succeeds_and_issues_token`
  - Invalid Login: `Tests\Feature\MedicalCaseApiTest::test_invalid_login_is_rejected`
  - Token Revocation: `Tests\Feature\MedicalCaseApiTest::test_api_logout_revokes_token`

---

### REQ-005: Live Database Dashboard Metrics
* **Requirement**: The dashboard calculates real case counts from the database across allowed statuses (Total, Planning, In Review, Approved, Completed). Hardcoded statistics are prohibited.
* **Implementation**:
  - Controller:
    - [DashboardController.php](file:///c:/Users/nusra/Desktop/jajal-medical-case-management/app/Http/Controllers/DashboardController.php)
  - View:
    - [resources/views/dashboard.blade.php](file:///c:/Users/nusra/Desktop/jajal-medical-case-management/resources/views/dashboard.blade.php)
* **Verification**:
  - Web Count Test: `Tests\Feature\MedicalCaseWebTest::test_dashboard_displays_real_database_counts`
