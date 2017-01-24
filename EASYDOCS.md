# EasyDocs for Jollof

This is a temporary location for accessing a very simple <q>How to</q> on using Jollof to develop apps.

# Run-Through Examples

Below are some examples that can help you dig in in minutes.

### Example 1 - Simple Page

>Here, we will be build a simple page with Jollof.

Let's open the terminal or console window and create a view file by running the command below. This command will create an __example__ folder with a _start.view_ file inside it.

```bash

	$ php jollof make:view example/start

```

Move into the __views__ folder (in the root) and into the __example__ folder, open up the _start.view_ file and add the markup below into the file.

```html
                  <!DOCTYPE html>
                    <html>
                    	<head>
                      		<title> =@title </title>
                    	</head>
                    	<body>
                        	<h1>=@heading</h1>
                        	<ul>
                        		[loop:@words]
                          			<li> @words_value </li>
                        		[/loop]
                        	</ul>
                    	</body>
                    </html>
```

Next, Let's create a simple GET route by running the command below. This command will add an entry into the _setup.php_ file inside the __routes__ folder.

```bash

    $ php jollof make:route /home GET

```

Let's now create a simple controller file by running the command below.
This command will drop a _Home.php_ file inside the __controller__ folder.

> NOTE: the name of the controller file <strong>MUST</strong> be the same as the first part of the route url.

```bash

    $ php jollof make:controller home

```

Move into the __controllers__ folder (in the root), open up the _Home.php_ file and add the code below into the file (within the _index_ method).

```php

      $words = ['Jollof', 'Make', 'Sense!'];
      $heading = 'About Jollof';
      return Response::view('example/start', [
                'title' => 'Example',
                'words' => $words,
                'heading' => $heading
      ]);
```

Next, move into the **configs** folder (in the root), open up the _env.php_ file and add the last line to the **app_auth** config section (array).

```php
        "app_auth" => array(
                .
                .
                .

                'guest_routes' => array( # These routes can be accessed only if the user is not logged in (guest).
                     '/',
                     '/account/login/',
                     '/account/register/',
                     '/account/signup/@mode/',
                     '/account/signin/@provider/',
                     '/home'
                )
        ),
        
        .
        .
        .        
``` 
Finally, move to the browser and load the route <q>/home</q> to view the page or view.

### Example 2 - Content-Security-Policy (CSP) Activation

Move into the **configs** folder (in the root), open up the _env.php_ file and edit the settings under **app_security** config section (array) to what you have below.
                
```php
  .
  .
  .

  "app_security" => array(

            'strict_mode' => FALSE, #options: (FALSE, TRUE) ;

            'csp' => TRUE, // #options: (FALSE, TRUE, array(...)) ; Content-Security-Policy

            'hpkp' => FALSE, // #options: (FALSE, TRUE, array(...))

            'cspro' => FALSE, // #options: (FALSE, TRUE, array(...)) ;Content-Security-Policy-Reporting-Only:

            'noncify-inline-source' => TRUE // Generates a nonce value for each <script> and <style> tag code in your views

  )

  .
  .

```

In the view you created in the first example (example/start), add an inline script tag to the head as below:

```html
<head>
 .
 .
 .

<script type="text/javascript">
     var t = 'Jollof';
     console.log(t);
</script>
</head>
```

Finally, serve the view in the browser as before using '/home'. Check the view source from the browser and notice CSP 'nonce=' values attached to the inline script tag.       
