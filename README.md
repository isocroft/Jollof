# Jollof

<img src="jollof.png"></img>

This is a very lighweight PHP framework built to cater to very busy backend-developers who have very little time to deliver on the job. It is also very configurable as most of the boilerplate code you need to get up and running on a serious project has already been baked in. Web development has never been this easy!

The aim of the framework is to make it very easy to develop applications with little or no friction as regards the ever changing landscape of web development.

## Quick Start

> It's important to note that *Jollof* requires no <q>Installation</q> so to speak to start using it. In the event that one wishes to use it to develop an ap

1. Firstly, spin up  terminal or console window and run the command below

```bash

$ php jollof docs

```
2. Next, click the blue <q>GO TO APP</q> button at the top right corner of the page that loads in the (your default) browser.

## Setting Up

The philosophy of **Jollof** is very simple. _Routes_ are very tightly knitted to the _Controllers_ that service them. For every route, the first part of the URI and/or path is the **Controller** class name and the second part of the URI and/or path is the **Controller** class method or the **action**. This is exactly similar to what you have for default routes in **ASP.NET** MVC.

> For more on getting up and running, see the [easydocs mark down file](https://github.com/isocroft/Jollof/blob/master/EASYDOCS.md).

All you need to setup can be found in the **documentation** folder(s) of the project starter folder.

## Contributing

_If you wish to contribute to this project, you can take a peep at the [contributing mark down file](https://raw.githubusercontent.com/isocroft/Jollof/master/CONTRIBUTING.md) or the [release notes text file](https://github.com/isocroft/Jollof/blob/master/release_notes.txt) to get guidance. Plus, any issues and pull requests should be filed on the [Jollof](https://github.com/isocroft/Jollof/) repository._

## Credits

_Thanks to **Micheal Akpobome**, **Shuaib Afegbua**, **Abraham Yusuf**, **Dimgba Kalu**, **Stephen Igwue** and **Kabir Idris** for their individual and collective efforts in this project. Jollof would not be the way it is right now without you guys._

## Minimum Requirements (Running Jollof)

>Before you attempt to startup Jollof, please ensure you machine meets the below requirements

* Have the PHP executable (php.exe, php.dmg) file path exposed in the environmental variable _PATH_

* Have PHP v5.3.8 and above installed and also enable all PHP extension below marked as **required**

	1. Have the PHP *mb_string* extension enabled (required - built-in)
	2. Have the PHP *openssl* extension enabled (required - built-in)
	3. Have the PHP *pdo_mysql* extension enabled (required - built-in)
	4. Have the PHP *pdo_pgsql* extension enabled (optional - built-in)
	5. Have the PHP *pdo_sqlite* extension enabled (optional - built-in)
	6. Have the PHP *pdo_mssql* extension enabled (optional - built-in)
	7. Have the PHP *sockets* extension enabled (required - built-in)
	8. Have the PHP *zip* extension enabled (required - built-in)
	9. Have the PHP *curl* extension enabled (required - built-in)
	10. Have the PHP *zlib* extension enabled (required - built-in)
	11. Have the PHP *mongodb* extension installed and enabled (optional - pecl)
	12. Have the PHP *memcached* extension installed and enabled (optional - pecl)
	13. Have the PHP *gd* extension enabled (required - built-in)
	14. Have the PHP *v8js* extension installed and enabled (optional - pecl)

* Have Composer v1.0.0 and above Installed

* Have Npm v4.2.0 and above Installed


## Trademarks

_Trademark(s) for this PHP framework are a joint effort of [Furppa](http://www.furppa.com.ng) and [WeCode](http://www.wecode.ng)_

## License

_The Jollof PHP framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT) and maintained by Mobicent, Ltd_
