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
* MySQL (may also work with PostgreSQL)
* Cron
* Working mail setup on the webserver

## Installation

* Clone this repo to the right location or copy/transfer it there
* Edit config.php to reflect your situation
* Import db.sql into your empty database
* Manually add an entry to the users table for your admin account, the password should be a PHP password_hash. I should probably automate this somehow.
* Setup a daily cronjob for cron.php

## License

This project is licensed under the AGPL license - see the [LICENSE](LICENSE) file for details

## Acknowledgements

Tabby uses the bubblegum bootstrap theme by hackerthemes.com, licensed under the MIT license and based on Bootstrap 4. This theme includes Font Awesome, which contains files under the CC BY 4.0, SIL OFL and MIT License.
