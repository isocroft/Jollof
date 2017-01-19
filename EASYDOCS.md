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
This command will drop a _Home.php_ file inside the __routes__ folder.

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

Finally, move to the browser and navigate to the route url '/home' from the root folder.


### Example 2 - Simple CSP (Content Security Policy) setup

Firstly, move into the __config__ folder and then into the _env.php_. Open it up and scroll to the <q>app_security</q> section. and modify as below.

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

```js
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

> Jollof supports security response headers on-the-fly. So, you don't have to do too much. CSP hashes will be supported in a later version - v1.0.0 perharps.