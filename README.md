# pmail

A simple PHP project with form handling and email functionality.

## Getting Started

### Using Laravel Valet + Mailhog

1. **Install Homebrew** (if not already installed)
   - Open Terminal
   - Run: `/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"`

2. **Install PHP and Composer**
   - Run: `brew install php composer`

3. **Install Laravel Valet**
   - Run: `composer global require laravel/valet`
   - Add Composer's global bin directory to your PATH in ~/.zshrc or ~/.bash_profile:
     ```
     export PATH=" $PATH:$HOME/.composer/vendor/bin"
     ```
   - Run: `valet install`

4. **Install Mailhog for Email Testing**
   - Run: `brew install mailhog`
   - Start Mailhog: `brew services start mailhog`
   - Access Mailhog interface at: http://localhost:8025

5. **Configure Project**
   - Navigate to your project directory: `cd /path/to/your/project`
   - Run: `valet link project-name`
   - Run: `composer install`
   - Create a `.env` file in your project root with:
     ```
     USERNAME=your_email_username
     PASSWORD=your_email_password
     ```
   - Configure PHP to use Mailhog by editing your php.ini:
     ```
     sendmail_path = /usr/local/bin/mailhog sendmail test@example.org
     ```

6. **Access Your Project**
   - Open your browser and navigate to http://project-name.test
   - Emails sent by the application will be captured by Mailhog

## Project Structure

- `index.php` - Initial form for user information
- `send_email.php` - Processes the form and displays email form for adults
- `email.php` - Handles email sending
- `success.php` - Success page after email is sent
- `failure.php` - Error page if email fails to send

## Troubleshooting

- If emails aren't sending, check your SMTP settings or Mailhog configuration
- For permission issues with Valet, try running `valet restart`
- For MAMP issues, check the Apache and PHP error logs in the MAMP interface