# Translation helpers for Laravel
-----
This package provides you with an analysis of the translation keys used in your Laravel application.
The analysis helps identify missing and unused keys.

## Installation

```bash
composer require kinoritech/lost-in-translation --dev
```

## Replace the Laravel Translator

1. Comment/remove the laravel TranslationServiceProvider
2. Add the one provided by this package.
```php
// config/app.php

'providers' => [
	...
	Illuminate\Session\SessionServiceProvider::class,
    //Illuminate\Translation\TranslationServiceProvider::class,
	...
	/*
     * Package Service Providers...
    */
    \KinoriTech\LostInTranslation\Providers\ServiceProvider::class,
    ...
],
```
## Usage

From the command line, run `php artisan locale:scan` to get the result of missing and unused translations.

```bash
$ php artisan locale:scan
Preparing files
Looking in /Users/horacio/git/lost-in-translation-wrapper/app and /Users/horacio/git/lost-in-translation-wrapper/resources
Searching translationkeys in 51 files
 51/51 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%
Found 23 translations.
There are missing translations.
+----------------------------------------------+-------+-----------+---------+
| Key                                          | Group | Namespace | Missing |
+----------------------------------------------+-------+-----------+---------+
| failed                                       | auth  | *         | fr      |
| throttle                                     | auth  | *         | fr      |
| password                                     | auth  | *         | fr      |
| Email                                        | *     | *         | es, fr  |
| Password                                     | *     | *         | es, fr  |
| Confirm Password                             | *     | *         | es, fr  |
| Reset Password                               | *     | *         | es, fr  |
| Name                                         | *     | *         | es, fr  |
| Already registered?                          | *     | *         | es, fr  |
| Register                                     | *     | *         | es      |
| This is a secure area of the application     | *     | *         | es, fr  |
| Confirm                                      | *     | *         | es, fr  |
| Forgot your password? No problem             | *     | *         | es, fr  |
| Email Password Reset Link                    | *     | *         | es, fr  |
| Thanks for signing up! Before getting st ... | *     | *         | es, fr  |
| A new verification link has been sent to ... | *     | *         | es, fr  |
| Resend Verification Email                    | *     | *         | es, fr  |
| Log Out                                      | *     | *         | es, fr  |
| Remember me                                  | *     | *         | es, fr  |
| Forgot your password?                        | *     | *         | es, fr  |
| Log in                                       | *     | *         | es, fr  |
| Whoops! Something went wrong                 | *     | *         | es, fr  |
| Dashboard                                    | *     | *         | es, fr  |
+----------------------------------------------+-------+-----------+---------+
There are unused translations.
*.*.I love programming.
*.auth.invalid

```

The `--table` (`-T`) option can be passed to get the complete list of keys:

```bash
$ php artisan locale:scan -T
Preparing files
Looking in /Users/horacio/git/lost-in-translation-wrapper/app and /Users/horacio/git/lost-in-translation-wrapper/resources
Searching translationkeys in 51 files
 51/51 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%
Found 23 translations.
+----------------------------------------------+-------+-----------+----+----+----+------------------------------------------------------------------------------------------------------------+
| Key                                          | Group | Namespace | en | es | fr | File                                                                                                       |
+----------------------------------------------+-------+-----------+----+----+----+------------------------------------------------------------------------------------------------------------+
| failed                                       | auth  | *         | ✔  | ✔  | —  | /Users/horacio/git/lost-in-translation-wrapper/app/Http/Requests/Auth/LoginRequest.php                     |
| throttle                                     | auth  | *         | ✔  | ✔  | —  | /Users/horacio/git/lost-in-translation-wrapper/app/Http/Requests/Auth/LoginRequest.php                     |
| password                                     | auth  | *         | ✔  | ✔  | —  | /Users/horacio/git/lost-in-translation-wrapper/app/Http/Controllers/Auth/ConfirmablePasswordController.php |
| Email                                        | *     | *         | ✔  | —  | —  | /Users/horacio/git/lost-in-translation-wrapper/resources/views/auth/login.blade.php                        |
| Password                                     | *     | *         | ✔  | —  | —  | /Users/horacio/git/lost-in-translation-wrapper/resources/views/auth/login.blade.php                        |
| Confirm Password                             | *     | *         | ✔  | —  | —  | /Users/horacio/git/lost-in-translation-wrapper/resources/views/auth/register.blade.php                     |
| Reset Password                               | *     | *         | ✔  | —  | —  | /Users/horacio/git/lost-in-translation-wrapper/resources/views/auth/reset-password.blade.php               |
| Name                                         | *     | *         | ✔  | —  | —  | /Users/horacio/git/lost-in-translation-wrapper/resources/views/auth/register.blade.php                     |
| Already registered?                          | *     | *         | ✔  | —  | —  | /Users/horacio/git/lost-in-translation-wrapper/resources/views/auth/register.blade.php                     |
| Register                                     | *     | *         | ✔  | —  | ✔  | /Users/horacio/git/lost-in-translation-wrapper/resources/views/auth/register.blade.php                     |
| This is a secure area of the application     | *     | *         | ✔  | —  | —  | /Users/horacio/git/lost-in-translation-wrapper/resources/views/auth/confirm-password.blade.php             |
| Confirm                                      | *     | *         | ✔  | —  | —  | /Users/horacio/git/lost-in-translation-wrapper/resources/views/auth/confirm-password.blade.php             |
| Forgot your password? No problem             | *     | *         | ✔  | —  | —  | /Users/horacio/git/lost-in-translation-wrapper/resources/views/auth/forgot-password.blade.php              |
| Email Password Reset Link                    | *     | *         | ✔  | —  | —  | /Users/horacio/git/lost-in-translation-wrapper/resources/views/auth/forgot-password.blade.php              |
| Thanks for signing up! Before getting st ... | *     | *         | ✔  | —  | —  | /Users/horacio/git/lost-in-translation-wrapper/resources/views/auth/verify-email.blade.php                 |
| A new verification link has been sent to ... | *     | *         | ✔  | —  | —  | /Users/horacio/git/lost-in-translation-wrapper/resources/views/auth/verify-email.blade.php                 |
| Resend Verification Email                    | *     | *         | ✔  | —  | —  | /Users/horacio/git/lost-in-translation-wrapper/resources/views/auth/verify-email.blade.php                 |
| Log Out                                      | *     | *         | ✔  | —  | —  | /Users/horacio/git/lost-in-translation-wrapper/resources/views/layouts/navigation.blade.php                |
| Remember me                                  | *     | *         | ✔  | —  | —  | /Users/horacio/git/lost-in-translation-wrapper/resources/views/auth/login.blade.php                        |
| Forgot your password?                        | *     | *         | ✔  | —  | —  | /Users/horacio/git/lost-in-translation-wrapper/resources/views/auth/login.blade.php                        |
| Log in                                       | *     | *         | ✔  | —  | —  | /Users/horacio/git/lost-in-translation-wrapper/resources/views/auth/login.blade.php                        |
| Whoops! Something went wrong                 | *     | *         | ✔  | —  | —  | /Users/horacio/git/lost-in-translation-wrapper/resources/views/components/auth-validation-errors.blade.php |
| Dashboard                                    | *     | *         | ✔  | —  | —  | /Users/horacio/git/lost-in-translation-wrapper/resources/views/layouts/navigation.blade.php                |
+----------------------------------------------+-------+-----------+----+----+----+------------------------------------------------------------------------------------------------------------+

...
```

## CI / Automated builds

The command returns an error exit code (1) if there are missing translations.
This can be used in CI or automated builds to stop the build.

Another use would be as a pre-commit or pre-push hook to prevent missing
translations to be committed.
