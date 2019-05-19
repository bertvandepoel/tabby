# Tabby - A friendly tool to manage debt

Tabby is a tool I made for myself due to lack of practical and unannoying tools to manage debt and remind people about it. Since existing solutions require accounts for people to even see how much you owe, or have other annoying requirements or missing features, I created Tabby with the idea that it would serve all of my needs and be as enjoyable for my debtors as possible (I was mostly just tired of reminding people to pay back their meals). 

While initially developing Tabby as mostly a quick little script, I somehow managed to develop it in full. So since it works surprisingly well and has a bunch of features, I ended up FOSSing it. It's something I very much believe in ideologically, and I think it might be useful to others.

## Features 

* An installation is a private instance owned by an admin
* The admin can approve or deny account registrations
* Accounts are only required to register debt, debtors don't require any form of account
* While an interface is available for debtors, they can also get all the information required to repay someone through email
* Track debt based on activities
* Track credit separate from whether a specific debt was repaid or not (so people with open credit are just fine)
* Reminds users to check their bank account and then ask Tabby to send reminders
* Probably GDPR-compliant, I guess?
* Adorable logo
* Probably more things, not too sure what to write here

## Requirements

* PHP 7.2 or up, mostly works fine with PHP 5.5.9 except for email functionality
* MySQL or PostgreSQL
* Cron
* Working mail setup on the webserver

## Installation

### Manual installation

* Clone this repo to the right location or copy/transfer it there
* Edit config.php to reflect your situation
* Import db.mysql.sql or db.pgsql.sql into your empty database
* Manually add an entry to the users table for your admin account, the password should be a PHP password_hash. I should probably automate this somehow.
* Setup a daily cronjob for cron.php

### Docker

There is also a docker container available at [vierbergenlars/tabby](https://hub.docker.com/r/vierbergenlars/tabby).
You can configure tabby with these environment variables.

| Environment variable | Default | Description |
| -------------------- | ------- | ----------- |
| TABBY_DB_DSN         | -       | [Data Source Name for PDO](https://www.php.net/manual/en/pdo.construct.php) database connection |
| TABBY_DB_USER        | ''      | Database username (optional, can also be part of the DSN for some database drivers) |
| TABBY_DB_PASSWORD    | ''      | Database password (optional, can also be part of the DSN for some database drivers) |
| TABBY_BASE_PATH      | '/'     | The path where tabby is installed under. End with a slash, just put a slash if there are no subfolders involved |
| TABBY_DOMAIN         | -       | Domain name that tabby wil run under (e.g.: example.com) |
| TABBY_PROTOCOL       | https   | Protocol that will be used (http or https) |
| TABBY_APPLICATION_EMAIL | noreply@$TABBY_DOMAIN | Email address where tabby will send emails from |
| TABBY_ADMIN_EMAIL    | root@$TABBY_DOMAIN | Email address where tabby will send notifications of new users to |
| TABBY_REMIND_DAYS    | 5       | The number of days before a user is reminded to check his bank account and remind any remaining debtor |
| TABBY_SMTP_SERVER    | -       | SMTP server where tabby will send its emails to |
| TABBY_SMTP_USER      | ''      | Username to log in to the SMTP server (optional) |
| TABBY_SMTP_PASSWORD  | ''      | Password to log in to the SMTP server (optional) |

Keep in mind that you will need a database (MySQL or PostgreSQL) for tabby to store its data in.
The database schema is automatically created the first time tabby starts up.

## License

This project is licensed under the AGPL license - see the [LICENSE](LICENSE) file for details

## Acknowledgements

Tabby uses the bubblegum bootstrap theme by hackerthemes.com, licensed under the MIT license. Bootstrap 4 includes Font Awesome, which contains files under the CC BY 4.0, SIL OFL and MIT License.
