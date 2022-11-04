## 🚀 Deployment Guide
- Run `php artisan db:seed --class=SeederMe`
- Add these missing `env` variables
- Attach a PDF doc if needed


## 🗃️ Migrations
- 2022_04_01_12340_create_table_a.php
- 2022_04_02_010719_alter_column_a_table_name.php => `table_b` table


## 📦 Packages
- Installed package A: https://link-to-package.com

## 📝  Changelog
**Summary**
- Added new QR scanner client
- Removed unused dependencies
- Improved database query on some module

## 📸 Attach screenshots if needed

<hr>

## ☑️ Coding Standard Checklist

- [ ] Follow the team's standard conventions -- consistency will get us far
- [ ] Don't comment it out. Just remove (or I'll smite you 🌩️)
- [ ] Keep it simple stupid. Simpler is always better. Reduce complexity as much as possible
- [ ] Code is DRY (Don't Repeat Yourself) -- but keep complexity to a minimum
- [ ] Be consistent. If you do something a certain way, do all similar things in the same way.
- [ ] Use explanatory variables and choose descriptive and unambiguous names
- [ ] Keep configurable data at high levels. Avoid magic numbers
- [ ] Use dependency injection and service providers
- [ ] Always try to explain yourself in code thinking of leaving a comment. Don't be redundant
- [ ] Use comments as explanation of intent or clarification of code or as warning of consequences
- [ ] Separate concepts vertically. Declare variables close to their usage. Related code should show vertically dense
- [ ] Objects should be small, do one thing, and hide internal data structure
- [ ] Unit/feature tests should exist! They should be repeatable, independent, and readable
