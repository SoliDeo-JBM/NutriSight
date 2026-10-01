# NutriSight: School-Based Feeding Program Management System

NutriSight is a Laravel application for managing the School-Based Feeding Program (SBFP) of Marisol Bliss Elementary School. It supports learner records, nutritional measurements, parent approval workflows, feeding attendance, QR scanning, school-year assignments, and downloadable program reports.

---

## Features

### Authentication and roles
*   **Role-Based Access Control**: Secure login system with three distinct user levels: **Super Admin**, **Admin**, and **Encoder**.
*   **Automatic Dashboard Routing**: Upon logging in, users are automatically directed to their respective role-tailored dashboards.

### Encoder (Adviser) module
*   **Advisory Student Lists**: A complete master table of all students under an adviser's section. Includes names (formatted as Last Name, First Name, Extension, Middle Name), birthdate, sex, weight (kg), height (cm), BMI, color-coded BMI categories (*Severely Wasted, Wasted, Normal, Overweight, Obese*), guardian contact details, and soft-delete archiving.
*   **Add Advisory Student Form**: A dedicated form where teachers can encode new students. Entering weight and height automatically calculates the student's BMI and nutritional status. Guardian email is optional.
*   **Advisory SBFP Lists**: Automatically filters students who are eligible for the feeding program (specifically those evaluated as *Wasted* or *Severely Wasted*, or explicitly approved by parents).
    *   **Parent Approval Manager**: Interactive radio buttons allowing teachers to mark students as Approved or Disapproved. Disapproving a student prompts for a reason (*Unwilling* or *Underlying medical condition* with a text note) and instantly removes them from the active SBFP list.
    *   **Portrait ID QR Code Printing**: Generates individual ID-sized portrait QR codes or a paginated **Letter / A4 batch sheet (9 IDs per page)** ready for printing and cutting.
*   **Attendance Dashboard & Calendar**: 
    *   Features an interactive monthly calendar with **Prev / Today / Next** buttons and **Month & Year dropdown selectors**.
    *   Active feeding days where QR scans occurred light up in a distinct green color with a live indicator for today's date.
    *   A vertical-scrolling daily roster beside the calendar lets teachers manually update student attendance (*Present, Absent, Tardy*).
*   **Dashboard Analytics**: Summary stat cards and a live **Chart.js** line graph tracking attendance frequency over the last 7 days.

### Admin and Super Admin modules
*   **School-Year Management**: Manage active and historical school years and scope assignments and report settings by year.
*   **SBFP Reports**: Generate attendance, consolidated period, and assessment reports.
*   **Report Exports**: Download reports as Excel, Word, or PDF documents.
*   **Report Settings**: Configure school and DepEd logos and the Project Development Officer name for each school year.
*   **Account Management**: Create and manage Admin and Encoder accounts, roles, and advisory assignments.
*   **Audit Logs**: Review important system changes.

### Encoder report access
*   **Advisory Attendance Report**: Review monthly attendance reports limited to the encoder's assigned grade and section.
*   **Advisory Assessment Report**: Review attendance and nutrition progress for the assigned advisory.

---

## How to Run the System Locally

The commands below are suitable for a new local checkout. They do not reset or delete existing database records.

### Prerequisites
Make sure you have the following installed on your computer:
*   PHP 8.3 or higher
*   Composer
*   Node.js and npm
*   Git
*   PostgreSQL or another database supported by Laravel

### Step-by-Step Setup Guide

1. **Open the project**
   Open a terminal inside the project folder.

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Configure environment variables**
   Copy `.env.example` to `.env` if needed, then configure the application key, database, mail, queue, and storage settings.

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Apply database migrations**
   Use the non-destructive migration command for an existing database:
   ```bash
   php artisan migrate --force
   ```

   To add local demo accounts and data, run the seeder separately only when appropriate:
   ```bash
   php artisan db:seed
   ```

5. **Build frontend assets**
   ```bash
   npm run build
   ```

6. **Start the local development environment**
   ```bash
   composer run dev
   ```

   Alternatively, run the server and Vite separately:
   ```bash
   php artisan serve
   npm run dev
   ```

   Open the URL shown by Laravel, usually `http://127.0.0.1:8000`.

7. **Log in**
   When the database seeder has been run, use one of these local demo accounts:
   *   **Encoder Account**: `encoder@nutrisight.test` | Password: `password`
   *   **Admin Account**: `admin@nutrisight.test` | Password: `password`
   *   **Super Admin Account**: `superadmin@nutrisight.test` | Password: `password`

## Testing

Run the Laravel test suite with:

```bash
php artisan test
```

---
If you encounter any issues or have questions while testing, feel free to reach out to the development team!
