# EasyDocs for Jollof PHP Framework

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

  public function index($models){

      $words = array('Jollof', 'Make', 'Sense!');
      $heading = 'About Jollof';
      return Response::view('example/start', array(
                'title' => 'Example',
                'words' => $words,
                'heading' => $heading
      ));
  }

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

Next, (still in the **configs** folder - in the root), move down the _env.php_ file and add the last line to the **app_auth** config section (array).

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

In the view created in the first example (example/start), add an inline script tag to the head as below:

```html
<head>
 .
 .
 .

<script type="text/javascript">
     var t = 'Jollof PHP';
     console.log(t);
</script>
</head>
```

Finally, serve the view in the browser as before using '/home'. Check the view source from the browser and notice CSP 'nonce=' values attached to the inline script tag.       

### Example 3 - Register and use a GIT webhook for pushing new code to your hosted website

Assuming you have uploaded a small website built with __Jollof__ (see Example 1) to a host provider (e.g Digital Ocean, Hostgator, Bluehost, WhoGoHost) , go to the __GitHub repository__ _dashboard_ for your/the small website (under the **Settings** tab) and click _Webhooks_. Then, enter the below URL into the webhook endpoint (make sure you have SSL setup).

Choose a domain e.g [https://www.example.com]

https://www.exapmle.com/webhook/git-payload/{gitaccountname}/{gitprojectname}

Save all changes to the GitHub **Settings** tab and you are good to go.

### Example 4 - Creating a user

Using the route **[/account/signup/@mode/]** which has already been setup in the routes *[setip.php]* file and also the  _todo-app.sql_ file in the root, follow the steps below:

- Run the following command (take note of the database name you entered into the cli)

```bash

    $ php jollof make:env

```

- Create a MySQL database and name it the same as in the command above

- Import the **todo-app.sql** file into the already created database 

- Open up the **Account** controller file and edit the _signup_ method like so

```php

  public function signup($models){

            $inputs = Request::input()->getFields(); /* get POST params */

            $validInputs = Validator::check( $inputs, /* validate POST params */
                array(
                    'email' => "email|required",
                    'password' => "password|required|bounds:8",
                    'first_name' => 'name|required',
                    'last_name' => 'name|required',
                    'mobile' => 'mobile_number|required'
                )
            );

            $json = array('status' => 'ok', 'result' => NULL); /* setting up resposne JSON */

            switch($this->params['mode']) {
                 case 'create':
                     if(Validator::hasErrors()){
                          $json['status'] = 'error';
                          $json['result'] = Validator::getErrors();
                     }else{

                        $json['result'] = Auth::register( $validInputs );
                     }
                 break;
            }

            if($json['status'] == 'ok'){
                if(isset($validInputs['auto-login'])
                   && $validInputs['auto-login'] === 'true'){
                        unset($validInputs['password'])
                        Auth::auto($json['result'], $validInputs);
                }
            }    

            $json['result'] = array();

            return Response::json( $json );
  }

```

- Also, edit the _signin_ method like so

```php

    public function signin($models){

            $inputs = Request::input()->getFields();

            $validInputs = Validator::check( $inputs,
                array(
                    'email' => "email|required",
                    'password' => "password|required"
                )
            );

            $json = array( 'status' => 'ok', 'result' => NULL );

            switch($this->params['provider']) {
                 case 'oauth-facebook':
                     # code...
                 break;
                 case 'oauth-instagram':
                     # code...
                 break;
                 case 'email':
                    if(Validator::hasErrors()){
                        $json['status'] = 'error';
                        $json['result'] = Validator::getErrors();
                    }else{

                        $json['result'] = Auth::login( $validInputs );
                    }
                 break;
             }

             return Response::json( $json );

    }

```

- Okay now, edit the _logout_ method like so

```php

  public function logout($models){

        Auth::logout();

        return Response::view('index', array('framework' => 'Jollof', 'title' => 'PHP MVC Framework'));
  }

```

- Finally, serve the register view in the browser as before using '/account/register'