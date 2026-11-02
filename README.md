# Student Registration System

The Student Registration System is a website for managing student information. Users can create an account, log in, and add, view, search, edit, or delete student records.

This project was developed for the **BWP501 – Web Programming 2** course.

## Live Website

[Open the Student Registration System](https://student-registration-system-hw.wuaze.com/)

## Features

- User registration, login, and logout.
- Add, view, edit, and delete student records.
- Upload an optional profile picture for each student.
- Search by student name, email, or phone number.
- Sort records by name or creation date.
- View 10 student records per page (pagination).
- Validate forms without reloading the page.
- Responsive layout for desktop and mobile screens.

## Technologies

- **HTML5 and CSS3**    - page structure and styling.
- **JavaScript**        - form checks and page interactions.
- **Bootstrap 5**       - layout and responsive components.
- **Bootstrap Icons**   - icons used in the website.
- **PHP**               - server side code.
- **MySQL**             - stores user accounts and student records.
- **PDO**               - connects PHP to MySQL and runs database queries.

## Project Structure

```text
Web_Programming_Two_HW/
|── assets/
|   |── css/
|   |   |── style.css
|   |── js/
|       |── script.js
|── authentication/
|   |── login.html
|   |── login.php
|   |── logout.php
|   |── register.html
|   |── register.php
|── database/
|   |── database.sql
|   |── db.php
|── students_crud/
|   |── add_student.php
|   |── delete_student.php
|   |── update_student.php
|   |── view_students.php
|── uploads/
|── index.php
|── README.md
```

The `uploads` folder stores student profile pictures.

## Database

The project uses a MySQL database named `student_registration_system_db`. You can create the database and its tables by importing `database/database.sql` through phpMyAdmin or run the sql of the `database.sql` in the sql editor in the phpMyAdmin.

The database contains two tables:

- **users**    _ stores: usernames, email addresses, password (hashed), and account creation dates.
- **students** _ stores: student names, email addresses, phone numbers, addresses, profile picture paths (in the uploads folder), and creation dates.

## How to run the Project Locally

You need a local server such as XAMPP or WAMP, PHP with the PDO MySQL extension enabled, and MySQL.

1. Install XAMPP or WAMP, then start Apache and MySQL.
2. Copy the project folder into the server's web directory. In XAMPP it is the `htdocs` folder.
3. Open phpMyAdmin and import `database/database.sql`.
4. Check the database settings in `database/db.php`. The local copy should use your local MySQL host, database name, username, and password. Make sure the database name is `student_registration_system_db`.
5. Make sure the `uploads` folder exists.
6. Open the registration page in your browser:

   ```text
   http://localhost/Web_Programming_Two_HW/authentication/register.html
   ```

7. Create an account and log in.

## Hosted Website

The website is hosted on InfinityFree:

[https://student-registration-system-hw.wuaze.com/]

Note that: the hosted website has its own database settings from the InfinityFree control panel.

## How to Use the Website

1. Open the registration page and create an account, pay attention to use a unique user name and a unique email,
   the password must be at least eight characters long and contain an uppercase letter, a lowercase letter, a number, and a special character.
2. Log in with your username or email address and password.
3. Select **Add Student** to enter a student's information. You can add a profile picture (it is optional), all other inputs are required.
4. Open **View Students** to see the saved students records.
5. In the same **View Students** page use the search field to find a student by name, email, or phone number.
6. Use the sorting options to change the order of the records. The list shows 10 records per page.
7. Select the edit option to change a student's information.
8. Select the delete option and confirm to remove a record.
9. Select **Logout** when you are finished.


## How the Main Features Work

- **Login and registration:** PHP checks the submitted information. Passwords are stored as hashes, not as plain text, and PHP sessions keep track of logged-in users.
- **Student records:** PHP and PDO handle adding, viewing, editing, and deleting records in the database. Prepared statements are used for database queries.
- **Search and sorting:** The student list can be searched by name, email, or phone number, and sorted by name or creation date.
- **Pagination:** The student list displays 10 records at a time.
- **Form submission:** JavaScript uses the Fetch API for registration, login, adding a student, and updating a student. This lets the website show the result without reloading the page.
- **Profile pictures:** Uploaded JPG, JPEG, PNG, and GIF files are saved in the `uploads` folder. Their paths are stored in the students table.

## Group Members

- shahd_211590_C5
- sara_305271_C4
- safa_318405_C4 
- esraa_238163_C4
- nadira_285703_C2

---
