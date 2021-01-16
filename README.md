# Tabby - A friendly tool to manage debt

Tabby is a tool I made for myself due to lack of practical and unannoying tools to manage debt and remind people about it. Since existing solutions require accounts for people to even see how much you owe, or have other annoying requirements or missing features, I created Tabby with the idea that it would serve all of my needs and be as enjoyable for my debtors as possible (I was mostly just tired of reminding people to pay back their meals). 

While initially developing Tabby as mostly a quick little script, I somehow managed to develop it in full. So since it works surprisingly well and has a bunch of features, I ended up FOSSing it. It's something I very much believe in ideologically, and I think it might be useful to others. From time to time, I still add new features to Tabby to make it more convenient for me or if a friend (or a user on GitHub) requests a specific feature that also makes sense to me.

## Table of Contents
  * [Features](#features)
  * [Screenshots](#screenshots)
     * [Landing page](#landing-page)
     * [Installation form](#installation-form)
     * [Overview of debt (or credit) of all your contacts](#overview-of-debt-or-credit-of-all-your-contacts)
     * [Overview of debt by activity](#overview-of-debt-by-activity)
     * [Reminder page](#reminder-page)
  * [Requirements](#requirements)
  * [Installation](#installation)
     * [Released version](#released-version)
     * [Git version](#git-version)
  * [Upgrading](#upgrading)
  * [Changelog](#changelog)
  * [License](#license)
  * [Acknowledgements](#acknowledgements)

## Features 

* An installation is a private instance owned by an admin
* The admin can approve or deny account registrations
* Accounts are only required to register debt, debtors don't require any form of account
* While an interface is available for debtors, they can also get all the information required to repay someone through email
* Track debt based on activities
* Track credit separate from whether a specific debt was repaid or not (so people with open credit are just fine)
* Reminds users to check their bank account and then ask Tabby to send reminders
* In spirit of the GDPR, as little information is required as possible.
* Adorable logo
* ... and more (I'm probably forgetting to mention some neat stuff, so check out the screenshots below for sure!)

## Screenshots

### Landing page
This page is displayed when a user is not logged in. It features some basic information about Tabby, as well as a login form, link to a registration form, and an easy way to request a token link to check your debt without an account.
![screenshot landing page](/screenshots/screenshot_1_landing.png?raw=true)

### Installation form
Since version 1.1, Tabby has a simple installation form where you enter your database credentials, email preferences and application settings. The database tables as well as the configuration file are created automatically.
![screenshot installation form](/screenshots/screenshot_2_install.png?raw=true)

### Overview of debt (or credit) of all your contacts
This is the page you see after logging in to Tabby. It gives an overview of what each of your contacts owes you and from what. Tabby displays which debts are (fully or partially) unpaid, as well as credits. A total is displayed at the bottom of each contact's box. You can easily enter wire transfers or cash you reveived through the small forms. Buttons are available for most actions you may need to perform.
![screenshot overview of debt by people](/screenshots/screenshot_3_people.png?raw=true)

### Overview of debt by activity
This page gives an overview of each activity you've added to Tabby. For those who haven't fully repaid their debt for that specific activity, the number is marked with a colour. You can also easily add extra contacts for a specific activity or change the numbers if you've made a mistake.
![screenshot overview of debt by people](/screenshots/screenshot_4_activities.png?raw=true)

### Reminder page
It's super easy to send a reminder with Tabby. You can pick whether you want to email everyone with debt or just a specific person. You can also add an optional message to make the reminder a bit more personal.
![screenshot reminder page](/screenshots/screenshot_5_reminder.png?raw=true)

## Requirements

* PHP 7.2 or up, mostly works fine with PHP 5.5.9 except for email functionality
* MySQL or PostgreSQL
* Working mail setup on the webserver
* Cron is advised but webcron fallback is available

## Installation

### Released version

* Download the [latest release](https://github.com/bertvandepoel/tabby/releases/latest) from GitHub releases.
* Unpack and upload the file to your server or hosting space.
* Visit the corresponding URL, Tabby will automatically display the installation form.
* Enter the database credentials (create them if you don't have them yet).
* Enter email and application settings.
* After confirming installation, the configuration will be written to a file. 
  * If no write permissions are available, the contents of the configuration file are displayed. Create config.php locally with those contents and upload it to the correct folder.
  * When you're not using webcron, correctly install a cronjob using the displayed example as basis.
  * If not using Apache or if mod_rewrite and/or .htaccess aren't available, you may need to configure correct mapping to index.php and redirecting of the changelog.
* You can now start using your Tabby installation. Log in with your account, then add people to register debt from activities for them. 

### Git version

Keep in mind that code may be committed to git that isn't ready for a full release.

* Clone this repo to the right location or copy/transfer it there.
* Visit the corresponding URL, Tabby will automatically display the installation form.
* Enter the database credentials (create them if you don't have them yet).
* Enter email and application settings.
* After confirming installation, the configuration will be written to a file. 
  * If no write permissions are available, the contents of the configuration file are displayed. Create config.php locally with those contents and upload it to the correct folder.
  * When you're not using webcron, correctly install a cronjob using the displayed example as basis.
  * If not using Apache or if mod_rewrite and/or .htaccess aren't available, you may need to configure correct mapping to index.php and redirecting of the changelog.
* You can now start using your Tabby installation. Log in with your account, then add people to register debt from activities for them.

## Upgrading

If you are using git, pull the latest version and then checkout the tag of the version you're upgrading to. If you are using releases, simply download the right files and overwrite your current directory (or move over config.php). When all the files are in place, visit upgrade.php or run it from the command line to perform database schema upgrade (if required). Follow any supplementary instructions upgrade.php displays.

## Changelog

A simplified changelog is available in the [changelog.txt](changelog.txt) file.

## License

This project is licensed under the AGPL license - see the [LICENSE](LICENSE) file for details.

## Acknowledgements

Tabby uses the bubblegum bootstrap theme by hackerthemes.com, licensed under the MIT license and based on Bootstrap 4. This theme includes Font Awesome, which contains files under the CC BY 4.0, SIL OFL and MIT License.
