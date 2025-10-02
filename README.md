📘 EduAxis Study Centre Management System (Public Landing & Student Portal)

This is a HTML + PHP + MySQL based web application designed for students of a study centre.
The system allows students to register, book slots, make payments, download receipts, and manage their profiles.
________________________________________
📂 Project Structure
When unzipped correctly, the project should look like this:
STUDY CENTRE MANAGEMENT SYSTEM/
│── HOME PAGE.php
│── REGISTER_FORM.php
│── LOGIN_FORM.php
│── STUDENT_DASHBOARD.php
│── SLOT_BOOKING.php
│── BOOKING_CONFIRM.php
│── WAITLIST_CONFIRM.php ( If booking goes to waitlist) 
│── PAYMENT_PORTAL.php
│── SLOTS.php
│── PAYMENT.php
│── QR_CODE.php
│── UPDATE_PROFILE.php
│── DELETE_ACCOUNT.php
│── LOGOUT.php
│── EduAxis.sql
│
├── CSS/
├── IMGS/
├── INCLUDES/
└── TCPDF-main/
________________________________________
🔓 Unzip Required Folders
When you download this project, you’ll see the following folders are provided as ZIP files:
•	CSS.zip
•	IMGS.zip
•	INCLUDES.zip
•	TCPDF-main.zip
👉 Before running the project, please extract/unzip these files into the main project folder.
⚠️ If you don’t unzip them, the project will not load styles, database connection, or PDF features.
________________________________________
⚙️ Requirements
•	XAMPP  (Apache)
•	PHP 7.4+
•	MySQL 5.7
•	Any modern web browser
________________________________________
🗄 Database Setup
1.	Open phpMyAdmin (http://localhost/phpmyadmin).
2.	Create a database: eduaxis
3.	Import EduAxis.sql
4.	Done ✅
________________________________________
🔑 Database Config
Check INCLUDES/db.php and set credentials:
•	host = "localhost"
•	user = "root"
•	password = "" (set your own MySQL password if any)
•	db = "eduaxis"
________________________________________
🚀 Run the Project
1.	Copy the folder to htdocs (XAMPP).
2.	Start Apache.
3.	Visit in browser:
http://localhost/STUDY%20CENTRE%20MANAGEMENT%20SYSTEM/HOME%20PAGE.php
________________________________________
📌 Key Features
•	Student registration / login / logout
•	Slot booking & waitlist management
•	Payment integration
•	PDF receipts using TCPDF
•	QR Code support
•	Update profile / delete account
________________________________________
⚠️ Common Issues
•	Database not found → Import EduAxis.sql
•	Wrong login → Check student users in DB
•	Connection error → Update db.php
•	Blank page → Ensure PHP is running properly
•	PDF not working → Ensure TCPDF-main is extracted

