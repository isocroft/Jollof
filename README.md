# Jelloff 

This is a very lighweight PHP framework built to cater to very busy backend-developers who have very little time to deliver on the job. It is also very configurable as most of the boilerplate code you need to get up and running on a serious project has already been baked in. Web development has never been this easy!

## Setting Up

The philosophy of **Jelloff** is very simple. _Routes_ are very tightly knitted to the _Controllers_ that service them. For every route, the first part of the URI and/or pathname is the Controller class name and the second part of the URI and/or pathname is the Controller class method. 

>e.g. /confirmation/proceed - Confirmation::proceed()

Views and Routes can be easily setup using the console command

```bash
	$ php jelloff make:view site/index
```

```bash 
	$ php jelloff make:route /confirmation/proceed POST
```

As well as Controllers and Models

```bash
	$ php jelloff make:model Comment
```

```bash 
	$ php jelloff make:controller Account Settings
```

_Mobicent Nigeria Ltd_
