# ruhh.2
Ruhh is a mental soothing website. It allows users to writes posts regarding mental challenges and publish them for the public.  which offers users to talk to AI powered therapist. But it is much more than that. Ruhh offers mood tracking which are based on the private notes. Ruhh spreads awareness on mental conditions through its blogs section. 

4.2 System Requirements  

Operating System: Windows 10 or above   

RAM: 8 GB minimum - 16 GB recommended for AI chatbot  

Storage: 10 GB free disk space 

Browser: Google Chrome or FIREFOX 

Internet: Required for initial installation/ to run icons 

4.3 User Manual – Website Setup and Execution 

Prerequisites 

Before running the website, ensure the following software is installed: 

XAMPP (Apache and MySQL services) 

Web browser (Google Chrome, Mozilla Firefox, Microsoft Edge, etc.) 

Internet connection (required for email verification through PHPMailer) 

Step 1: Download the Project 

Download the project files from the GitHub repository. 

Extract the downloaded ZIP folder if necessary. 

 

Step 2: Place the Project in XAMPP 

Open the XAMPP installation directory. 

Navigate to the htdocs folder. 

Copy the project folder (ruhh.2) into the htdocs directory. 

Example: 

C:\xampp\htdocs\ruhh. 

 

 

 

 

 

Step 3: Start Apache and MySQL 

Open the XAMPP Control Panel. 

Click Start for Apache. 

Click Start for MySQL. 

Ensure both services show a green status indicator. 

 

 

Step 4: Create the Database 

Open a browser and visit: 

http://localhost/phpmyadmin 

Click New from the left panel. 

Enter the database name (e.g., ruhh.2). 

Click Create. 

 

Step 5: Import Database Tables 

Select the newly created database. 

Click the Import tab. 

Choose the provided SQL file (users_db.sql). 

Click Go. 

Wait until the import process completes successfully. 

 

Step 6: Configure Database Connection 

Open the config.php file. 

Verify the database credentials: 

Host: localhost 

Username: root 

Password: (leave blank for default XAMPP installation) 

Database Name: users_db 

Save the file after verification. 

 

Step 7: Configure PHPMailer 

Open the PHP file containing PHPMailer settings. 

Enter the sender email address. 

Enter the Gmail App Password. 

Save the changes. 

 

 

 

 

 

Step 8: Launch the Website 

Open a web browser. 

Enter the following URL: 

http://localhost/ruhh 

Press Enter. 

The website's homepage will be displayed. 

 

User Registration 

Click the Sign-Up button. 

Enter the required information. 

Submit the registration form. 

Verify the account through the email verification link. 

 

User Login 

Click the Login button. 

Enter the registered email address and password. 

Click Login. 

The user dashboard will be displayed after successful authentication. 

 

Troubleshooting 

Ensure Apache and MySQL services are running. 

Verify that the database has been imported successfully. 

Check database credentials in config.php. 

Confirm PHPMailer email credentials are correctly configured. 

Ensure an active internet connection is available for email services. 
