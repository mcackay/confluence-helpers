Confluence Helpers
========

Quick-n-dirty PHP scripts and hacks that work with Confluence to do various things.

## Confluence Login Screen Replacement
### With support for email address as 'username'

We have casual users, and they kept forgetting their username and creating new accounts. So we wanted people to log in using their **email address**.

The solution consists of two files, and an Apache RewriteRule.

* login.php <-- your form, made to look however you want
* login_action.php <-- "action" for the login.php <form>

The full instructions are at the top of *login_action.php*.