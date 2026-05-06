# Car Dealership (XAMPP + MySQL)

This project uses MySQL on `localhost` (via XAMPP) and a provided SQL schema file to create the database tables.

## 1) Set up MySQL on localhost
1. Use the MySQL that comes with XAMPP (so localhost defaults match).
2. MySQL connection defaults for XAMPP are typically:
   - Host: `127.0.0.1` (or `localhost`)
   - Port: `3306`
   - User: `root`
   - Password: *(usually blank/empty by default)*
3. The schema expects the database name: `car_dealership`.
4. You must be able to access phpMyAdmin from the browser at `http://localhost/phpmyadmin` (this requires MySQL to be running).

## 2) Start XAMPP services
5. Open XAMPP Control Panel.
6. Click **Start** for **Apache**.
7. Click **Start** for **MySQL**.

## 3) Import the database schema (phpMyAdmin)
8. Open a browser and go to: `http://localhost/phpmyadmin`
9. Create/select the database `car_dealership` (so it’s selected before importing).
10. Click the **Import** tab, select `car_dealership_schema.sql`, then click **Go**.
11. Confirm the tables (`Vehicle`, `User`, `Purchase`, etc.) appear under the `car_dealership` database.

## 4) Keep project synced to htdocs (optional)
If you edit outside `C:\xampp\htdocs`, run this from the project root:

`powershell -ExecutionPolicy Bypass -File .\scripts\sync-to-xampp.ps1`

This mirrors the project into:

`C:\xampp\htdocs\Spring-2026-CPSC-332-Project`

Then open:

`http://localhost/Spring-2026-CPSC-332-Project/`

