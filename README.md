# pmail


A simple PHP project for form handling and email functionality, with testing via Mailhog.


## Prerequisites
- macOS (for Homebrew and Valet compatibility).
- Basic terminal knowledge.
- Ensure you have administrator access for installations.


## Getting Started


Follow these steps in order to set up and run the project locally. We'll use Laravel Valet for serving the site and Mailhog for email testing.


### Step 1: Install Homebrew (Package Manager)
If Homebrew isn't installed:
1. Open your Terminal app.
2. Run: `/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"`.
3. Follow on-screen prompts. Restart Terminal after installation.
4. Verify: Run `brew --version`. If it fails, check your PATH or reinstall.


### Step 2: Install PHP and Composer
1. Run: `brew install php composer`.
2. Verify PHP: `php -v` (should show a version like 8.x).
3. Verify Composer: `composer --version`.
4. If issues arise (e.g., version conflicts), specify versions like `brew install php@8.2`.


### Step 3: Install and Configure Laravel Valet
1. Run: `composer global require laravel/valet`.
2. Add Composer's global bin to your PATH:
   - Open or create `~/.zshrc` (or `~/.bash_profile` for Bash) with a text editor.
   - Add: `export PATH="$PATH:$HOME/.composer/vendor/bin"`.
   - Save and run `source ~/.zshrc` to apply.
3. Run: `valet install`.
4. Verify: Run `valet --version`. If errors occur, check your PHP installation or run `valet diagnose`.


### Step 4: Install and Start Mailhog for Email Testing
1. Run: `brew install mailhog`.
2. Start Mailhog as a service: `brew services start mailhog`.
3. Verify: Open a browser and go to `http://localhost:8025`. You should see the Mailhog interface. If not, check if port 8025 is in use (e.g., via `lsof -i :8025`) or restart your computer.
4. Stop/restart if needed: `brew services stop mailhog` or `brew services restart mailhog`.


### Step 5: Configure the Project
1. Navigate to your project directory: `cd /path/to/your/pmail/project` (replace with your actual path).
2. Link the project with Valet: `valet link pmail` (this creates a local domain like `pmail.test`).
3. Install dependencies: `composer install`. If errors occur, ensure Composer is updated (`composer self-update`).
4. Create a `.env` file in the project root (copy from `.env.example` if available, or create manually) with your email credentials:
```
USERNAME=your_email_username
PASSWORD=your_email_password
```
- Note: These are for SMTP if using real email; for Mailhog testing, they're optional.
5. Configure PHP to use Mailhog for email testing:
- Find the correct `php.ini` file: Run `php --ini` to locate it (e.g., `/usr/local/etc/php/8.2/php.ini` or Valet's config).
- Edit `php.ini` (use `sudo` if needed) and add or update:
  ```
  sendmail_path = /usr/local/bin/mailhog sendmail -t
  ```
  - This routes PHP's `mail()` function to Mailhog. The `-t` flag is crucial for reading email recipients correctly.
- Save and restart Valet: `valet restart`.
6. (Optional) For database integration (e.g., PostgreSQL), see the "Database Integration" section below.


### Step 6: Access and Test the Project
1. Open a browser and go to `http://pmail.test` (or your linked domain).
2. Test email sending: Use the "Send an Email" form. Sent emails should appear in Mailhog at `http://localhost:8025`.
3. If the site doesn't load, check Valet status (`valet links`) or domain resolution.


## Project Structure
- `index.php`: Initial form for user information.
- `send_email.php`: Processes forms and shows email form for adults.
- `email.php`: Handles email sending logic.
- `success.php`: Page shown after successful email send.
- `failure.php`: Page shown if email sending fails.
- `register.php`: New page for user registration (optional, for receiving emails).
- `header.php`: Reusable header module for navigation.
- Other files: CSS, scripts, and dependencies.


## Database Integration (Optional)
To add a database like PostgreSQL for storing data (e.g., user registrations):
1. Install PostgreSQL: `brew install postgresql` and start it: `brew services start postgresql`.
2. Create a database and tables via `psql` (see project documentation or external guides).
3. Use PHP's PDO in your scripts to connect and query (example in project notes).


## Troubleshooting
- **Emails Not Appearing in Mailhog**: Verify `sendmail_path` in `php.ini`, ensure Mailhog is running (`brew services list`), and check PHP error logs (e.g., add `error_reporting(E_ALL);` to scripts). Test with a simple `mail()` script.
- **Valet Issues**: Run `valet restart` or `valet diagnose`. Check for port conflicts or reinstall PHP.
- **Permission Errors**: Use `sudo` for file edits or check ownership (`chown -R youruser:staff .` in the project folder).
- **General Errors**: Enable PHP debugging by adding `error_reporting(E_ALL); ini_set('display_errors', 1);` at the top of PHP files temporarily. Review server logs via Valet or your terminal.
- **Database Problems**: Ensure Postgres extensions are enabled in PHP and check connection credentials.


For more help, consult official docs for Valet, Mailhog, or PostgreSQL. If issues persist, share error messages from logs.