# Diglin_Username #

Magento module which allows your customers to use a username and not only the email address as identifier

## Features

- Login with a username and/or email, it can be done from frontend during checkout or getting access to the customer account
- Save a username from frontend (register account or checkout process) or from backend by editing a customer account
- Check that the username doesn't already exists
- Allow you to deactivate temporary customer account from Customer Management page (bonus functionality from version > Magento 1.4.x). The user will be blocked if the option in the customer backend is set to no.
- The default templates override some customer and checkout views to adapt display for login pages, checkout process and account edition in frontend. If you have a customized template, please check the layout file username.xml and compare with your template to use or adapt to your situation.
- When you have already customers in your system and you do a first install of this plugin, a username will be generated for each customer based on a part of his email and a unique id. (e.g. email address is "developer@localhost.com" -> username is "developer1235467")
- Configurable options to define what kind of username to support: only letters, only digits, both or default (digits, letters and special characters '-_')
- Configurable options to set the maximum and minium string length
- Display Username of each customer in the Customer Management Grid
- Allow or not the customer to edit the username in My Account in frontend
- Compatible and tested with Magento version >=1.4.2 until 1.7.x
- NEW - support username when a customer wants to retrieve his forgotten password thanks to the "Forgotten Password" form
- NEW - support username into the template of the persistent module

## Installation

### Via Magento Connect
- You can install the current stable version via [MagentoConnect](http://www.magentocommerce.com/magento-connect/username-support-login-register-checkout-by-diglin.html)

### Via modman
- Install [modman](https://github.com/colinmollenhour/modman)
- Use the command from your Magento installation folder: `modman clone https://github.com/diglin/Diglin_Username.git`

### Manually
- You can copy the files from the folders of this repository to the same folders of your installation

## Documentation

- Please, configure the module go to the backend and follow the menu System > Configuration > Diglin > Username
- You can put the username into your email template, you can put the following string {{var customer.username}} in the email templates: account_new.html and account_new_confirmation.html
- If you have a 404 error page, try to login/logout and go back to the configuration page. Or save again the Administrator role in System > Permissions > Role
- IMPORTANT NOTE: check if you want to have the customer account global or per website, see in System > Configuration > Customers > Customer Configuration > Account Sharing Options
  If set to "Per website", the username will be unique per each website
  If set to "Global", the username will be unique for the whole website

## Important

If you have an important quantity of customers in your database, please try this module on a development environment first. All old customers will get a random username based on their email address. The process may be long during the installation.
It's important to know if you create account from the backend, check which Website where you want to save the account

## Deinstall

The module install some data and changes in your database. Deinstalling the module will make some trouble cause of those data. You will need to remove those information by following the procedure below.

#### Via MAgeTrashApp

An additional module called MageTrashApp has been installed with this module to help you to uninstall this module in a clean way. If it is not installed, please install it from [MageTrashApp](https://github.com/magento-hackathon/MageTrashApp)
If it is installed, go to your backend menu System > Configuration > Advanced > MageTrashApp, then click on the tab "Extension Installed", select the drop down option "Uninstall" of the module Diglin_Username and press "Save Config" button to uninstall
If you use this module, you don't need to make any queries in your database as explained below in case of manually uninstallation.

#### Via Magento Connect or manually

- If you used Magento Connect, you may use the deinstall process of the Magento Connect Backend page view of your Magento installation.
- Otherwise remove the files following the hierarchy of the folders of this repository
- Then get access to your database and do the followings queries:
  Do the following sql query in your database after to have done a backup, please check the table name with your database:

`DELETE FROM eav_attribute WHERE attribute_code LIKE '%username%';`
`ALTER TABLE sales_flat_quote DROP COLUMN 'customer_username';`
`ALTER TABLE sales_flat_order DROP COLUMN 'customer_username';`

## Author

* Sylvain Rayé
* http://www.sylvainraye.com/
* [@sylvainraye](https://twitter.com/sylvainraye)
* [Follow me on github!](https://github.com/diglin)

## Donation

[Invite me for a drink](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Y66QHLU5VX5BC)

## Change Log
- V 2.0: 
   - add support for the "forgot password" form
   - fix bugs with checkout as guest
   - implement best practices for install process
   - check if same username are not used in different websites when the shop owner change the configuration of the "Account Sharing Options"
   - add conditional templates for the persistent module
   - Update the license in PHP file
