# Installation Guide

This document provides instructions on how to install and set up the "library-email-system" project on both Ubuntu and Windows operating systems.

## Prerequisites

Before you begin, ensure you have the following installed on your system:

*   Git
*   PHP (with necessary extensions like OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, PCNTL, Soap, Sockets, Fileinfo, GD, Mcrypt, MySQLnd, Zip, Intl)
*   Composer
*   Node.js and npm
*   A database server (e.g., MySQL, PostgreSQL, SQLite)

## Installation Steps

Follow the steps below to install the project:

1.  **Clone the repository:**

    Open a terminal or command prompt and run the following command to clone the project repository:
```
bash
    git clone <repository_url>
    cd library-email-system
    
```
*Replace `<repository_url>` with the actual URL of your project repository.*

2.  **Install PHP Dependencies (Composer):**

    Navigate to the project's root directory in your terminal and run Composer to install the required PHP packages:

    *   **Ubuntu:**
```
bash
        composer install
        
```
If you don't have Composer installed globally, you might need to download it first:
```
bash
        sudo apt update
        sudo apt install composer
        composer install
        
```
*   **Windows:**

        If you don't have Composer installed globally, download and run the Composer installer from [https://getcomposer.org/download/](https://getcomposer.org/download/).

        Open your project directory in a terminal or command prompt and run:
```
bash
        composer install
        
```
3.  **Install JavaScript Dependencies (NPM):**

    Stay in the project's root directory and use npm to install the front-end dependencies and compile the assets:

    *   **Ubuntu & Windows:**
```
bash
        npm install
        npm run dev
        
```
`npm run dev` compiles the assets for development. You can use `npm run watch` during development to automatically recompile assets when files change, or `npm run prod` to compile assets for production.

4.  **Set up Environment File:**

    Copy the example environment file and generate an application key:

    *   **Ubuntu:**
```
bash
        cp .env.example .env
        php artisan key:generate
        
```
*   **Windows:**
```
bash
        copy .env.example .env
        php artisan key:generate
        
```
Now, open the newly created `.env` file in a text editor. Configure your database connection details (`DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) and any other necessary environment variables.

5.  **Run Database Migrations and Seeders:**

    Execute the following command to create the necessary database tables and populate them with initial data (like default tags and users):
```
bash
    php artisan migrate --seed
    
```
6.  **Serve the Application:**

    You can serve the application using the built-in PHP development server or configure a web server like Apache or Nginx.

    *   **Using the PHP Development Server:**
```
bash
        php artisan serve
        
```
This will typically make the application available at `http://127.0.0.1:8000`.

    *   **Using Apache or Nginx:**

        Configure your web server to point the document root to the `public` directory within your project. Refer to your web server's documentation for specific configuration instructions.

Once these steps are completed, your "library-email-system" should be installed and ready to use.
