![alt XOOPS CMS](https://xoops.org/images/logoXoops4GithubRepository.png)
## Jobs module for  [XOOPS CMS 2.5.8+](https://xoops.org)
[![XOOPS CMS Module](https://img.shields.io/badge/XOOPS%20CMS-Module-blue.svg)](https://xoops.org)
[![Software License](https://img.shields.io/badge/license-GPL-brightgreen.svg?style=flat)](http://www.gnu.org/licenses/gpl-2.0.html)

**Jobs** for [XOOPS CMS](https://xoops.org) is a a module to post jobs and resumes

Current and upcoming "next generation" versions of XOOPS CMS are being crafted on GitHub at: https://github.com/XOOPS

## Security Improvements

This module has been reviewed for security vulnerabilities and the following improvements have been made:

*   **SQL Injection:** The `addlisting.php` and `addresume.php` files were vulnerable to SQL injection. They have been refactored to use a data access layer with prepared statements, which prevents this vulnerability.
*   **Cross-Site Scripting (XSS):** The templates were not escaping user-provided data, which could lead to XSS attacks. The templates have been updated to escape all output by default.
*   **Cross-Site Request Forgery (CSRF):** The `deljob.php` file was vulnerable to CSRF attacks. It has been updated to use a CSRF token to prevent this vulnerability.
*   **File Uploads:** The file upload mechanism has been improved by adding `.htaccess` files to the `resumes/` and `photo/` directories to prevent the execution of PHP scripts.

## Testing

This module now has a PHPUnit test suite. To run the tests, you will need to:

1.  Install the dependencies using Composer: `composer install`
2.  Create a `tests/bootstrap.php` file with the correct path to your XOOPS installation. A `tests/bootstrap.php.dist` file is provided as a template.
3.  Run the tests using PHPUnit: `vendor/bin/phpunit`

## Refactoring and Modernization

The following refactoring and modernization has been done:

*   **Composer:** The module now uses Composer to manage dependencies.
*   **PSR-4 Autoloader:** The module now uses a PSR-4 autoloader provided by Composer, which replaces the old custom autoloader.
*   **Data Access Layer:** A data access layer has been introduced to separate the business logic from the database access code.
*   **Code Style:** The code has been formatted according to the PSR-12 standard. You can check the code style by running `vendor/bin/php-cs-fixer fix --dry-run`.

### Recommendations for Future Improvements

*   **Use HTML Purifier:** The `MyTextSanitizer::displayTarea()` method is used to sanitize HTML input. While this provides some protection, it is recommended to use a more robust library like [HTML Purifier](http://htmlpurifier.org/) to prevent XSS attacks.
*   **Store Uploaded Files Outside of the Web Root:** The file upload mechanism has been improved, but for maximum security, it is recommended to store uploaded files outside of the web root.
*   **Complete the Test Suite:** The test suite currently only covers the data access layer. It should be extended to cover all the functionality of the module.

## Database Improvements

The database schema has been improved to use the InnoDB storage engine and to add foreign key constraints. To apply these improvements, you will need to:

1.  Log in to the XOOPS admin area.
2.  Go to the "Jobs" module.
3.  Click on the "Upgrade Database" link in the admin menu.
