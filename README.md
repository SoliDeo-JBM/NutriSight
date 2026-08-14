# NutriSight: School-Based Feeding Program (SBFP) Management System

This README provides a straightforward guide on what NutriSight is, what it does, and how you can run and test the system locally on your computer.

---

## What is NutriSight?
**NutriSight** is a web application built specifically for the **School-Based Feeding Program (SBFP)** of Marisol Bliss Elementary School. Its main purpose is to help teachers (Encoders) and school administrators efficiently manage student nutritional profiles, track feeding program participation, manage parent approvals, record daily attendance using QR codes, and generate health/nutrition progress reports.

---

## Current Available Functionalities

### 1. User Authentication & Roles
*   **Role-Based Access Control**: Secure login system with three distinct user levels: **Super Admin**, **Admin**, and **Encoder**.
*   **Automatic Dashboard Routing**: Upon logging in, users are automatically directed to their respective role-tailored dashboards.

### 2. Encoder (Adviser) Module
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

### 3. Admin & Super Admin Modules
*   **Admin Reports**: Aggregates nutritional data to display baseline vs. mid-program summary counts and individual student health progress.
*   **Account Management (Super Admin)**: Handles account creation and role assignments for admins and encoders.

---

## How to Run the System Locally

Follow these plain-English steps to set up and run NutriSight on your local machine:

### Prerequisites
Make sure you have the following installed on your computer:
*   PHP (version 8.2 or higher)
*   Composer
*   Node.js & npm
*   Git

### Step-by-Step Setup Guide

1. **Clone the Repository & Open Terminal**
   Open your terminal or command prompt inside the project folder (`/NutriSight`).

2. **Install PHP Dependencies**
   Run the following command to install all required backend packages:
   ```bash
   composer install
   ```

3. **Install JavaScript Dependencies**
   Run this command to install frontend packages:
   ```bash
   npm install
   ```

4. **Configure Environment Variables**
   Make sure you have your `.env` file set up with the correct database connection pointing to our central Supabase PostgreSQL database (or your local database).

5. **Run Database Migrations & Seeders**
   To set up the database tables and populate sample test accounts, run:
   ```bash
   php artisan migrate:fresh --seed
   ```
   *(Note: This sets up clean database tables with default user accounts).*

6. **Start the Local Development Server**
   Start Laravel's local server by running:
   ```bash
   php artisan serve
   ```
   This will output a local URL (usually `http://127.0.0.1:8000`). Open that link in your web browser.

7. **Log In and Test**
   You can log in using the pre-configured test accounts created by the seeder:
   *   **Encoder Account**: `encoder@nutrisight.test` | Password: `password`
   *   **Admin Account**: `admin@nutrisight.test` | Password: `password`
   *   **Super Admin Account**: `superadmin@nutrisight.test` | Password: `password`

---
If you encounter any issues or have questions while testing, feel free to reach out to the development team!
