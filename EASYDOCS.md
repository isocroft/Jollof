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
                            [if:@heading == 'About Jollof']
                                <p>This is the caption</p>
                            [/if]
                        	<ul>
                        		[loop:@words]
                          			<li> [@words_value] </li>
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
                    'mobile_number' => 'mobile_number|required'
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

            // $json['result'] = array();

            return Response::redirect( '/admin');
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

### Example 5 - Data source read and writes

Using the _admin/index_ view in the **views** folder, open it up in a text editor or IDE and edit as below:

```html

    <!DOCTYPE html>
    <html data-x-path="[!asset(/offline.html)]" lang="en">
    <head>
        <title>=@framework &dash; =@title</title>
        <meta charset="utf-8">
        <meta name="csrftoken" content="=@csrftoken">

        <link rel="icon" type="text/x-icon" href="[!asset(/favicon.ico)]">

        <link rel="manifest" type="application/manifest+json" href="[!asset(/manifest.json)]">
        <link rel="stylesheet" type="text/css" href="[!asset(/css/bootstrap.min.css)]">
        <link rel="stylesheet" type="text/css" href="[!asset(/css/bootstrap-theme.min.css)]">

        <style type="text/css">

                /**
                 *  NORMALIZE CSS 
                 */

                article, aside, details, figcaption, figure, footer, header, hgroup, nav, section { display:block; }
                audio, canvas, video { display: inline-block; *display: inline; *zoom: 1; }
                audio:not([controls]), [hidden] { display:none; }

                /** Base Styles **/

                html { font-size: 100%; overflow-y: scroll; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
                body { margin: 0; font-size: 13px; line-height: 1.5; }
                body, button, input, select, textarea { font-family: sans-serif; color: #000; }

                /** IE Fixes **/

                img { border: 0; -ms-interpolation-mode: bicubic; }
                svg:not(:root) { overflow: hidden; }
                figure { margin: 0; }

                /** Links **/
                a:focus { outline: thin dotted; }
                a:hover, a:active { outline: 0; }

                /** Typography **/
                h1 { font-size: 2em; } /* fixes html5 bug */
                p { -webkit-hyphens: auto; -moz-hyphens: auto; -epub-hyphens: auto; hyphens: auto; }
                abbr[title] { border-bottom: 1px dotted; }
                b, strong, .strong { font-weight: bold; }
                dfn, em, .em { font-style: italic; }
                small, .small, sub, sup { font-size: 75%; }
                ins, .ins { background: #ff9; color: #000; text-decoration: none; }
                mark, .mark { background: #ff0; color: #000; font-style: italic; font-weight: bold; }
                hr { display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0; }
                pre, code, kbd, samp { font-family: monospace, serif; _font-family: 'courier new', monospace; font-size: 1em; }
                pre { white-space: pre; white-space: pre-wrap; word-wrap: break-word; }
                blockquote { margin: 1.5em 40px; }
                q { quotes: none; }
                q:before, q:after { content: ''; content: none; }
                ul, ol { margin: 1.5em 0; padding: 0; }
                dd { margin: 0; }
                nav ul, nav ol, .widget ol, .widget ul, .commentlist { list-style: none; list-style-image: none; margin: 0; }

                /* Position subscript and superscript content without affecting line-height: gist.github.com/413930 */
                sub, sup { line-height: 0; position: relative; vertical-align: baseline; }
                sup { top: -0.5em; }
                sub { bottom: -0.25em; bottom:1ex; }

                /** Forms **/

                form, fieldset, form ul, form ol, fieldset ol, fieldset ul { margin: 0; border: 0; }
                legend { border: 0; *margin-left: -7px; }
                button, input, select, textarea { font-size: 100%; margin: 0; vertical-align: baseline; *vertical-align: middle; }
                button, input { line-height: normal; }
                button, input[type="button"], input[type="reset"], input[type="submit"] { cursor: pointer; -webkit-appearance: button; *overflow: visible; }
                input[type="checkbox"], input[type="radio"] { box-sizing: border-box; padding: 0; }
                input[type="search"] { -webkit-appearance: textfield; -moz-box-sizing: content-box; -webkit-box-sizing: content-box; box-sizing: content-box; }
                input[type="search"]::-webkit-search-decoration { -webkit-appearance: none; }
                button::-moz-focus-inner, input::-moz-focus-inner { border: 0; padding: 0; }
                textarea { overflow: auto; vertical-align: top; }
                textarea:focus, textarea:active { outline:none; outline:0;  }

                /* Colors for form validity */
                input:invalid, textarea:invalid { background-color: #f0dddd; }

                /** Tables **/
                table { border-collapse: collapse; border-spacing: 0; }

                .clear-fx:before,
                .clear-fx:after{
                    content: "\0020";
                    height:0;
                    display:block;
                    overflow: hidden;
                    visibility:hidden;
                    line-height:0;
                }

                .clear-fx:after {
                    clear: both;
                }

                *+html .clear-fx { 
                    /* IE less than 8 (6/7) */
                    zoom: 1;
                } 

                html, body{
                    margin:0;
                    padding:0;
                    border:none;
                    min-width:100%;
                    /* using IE star hack for fallback */
                    *width:100%;
                    height:100%;
                    overflow:hidden !important;
                    position:relative !important;
                }

                .row-box{
                    padding:0 10px;
                }

                .main-row{
                    position:relative;
                    width:auto !important;
                    padding-left:0;
                    padding-right:0;
                    height:100%;
                }

                .aside-col{
                    float:left;
                    position:relative !important;
                    width:180px;
                    height:100%;
                } 

                .main-col{
                    position:relative !important;
                    width:auto !important;
                    overflow:hidden !important;
                    height:100%;
                    background-color:#f5f5f5;
                    color:#888888;
                    padding-left:10px;
                }

                .x-left-col{
                    background-color:#fefefe;
                    color:#d5d5d5;
                }

                .x-right-col{
                    background-color:#3cefa1;
                    color:#ffffff;
                }
        </style>
    </head>
    <body>

        <div class="row-box main-row clear-fx">
            <aside class="aside-col x-left-col">

            </aside>
            <main class="main-col x-right-col">
                <ul class="">
                    [loop:@user]
                        <li>
                            <label>
                                [@user_index]
                            </label>
                            <span>
                                [@user_value]
                            </span>
                        </li>
                    [/loop]
                </ul>
            </main>
        </div>

        <script type="text/javascript" src="[!asset(/js/browsengine.js)]"></script>
        <script type="text/javascript" src="[!asset(/js/manup.js)]"></script>
        <script type="text/javascript" src="[!asset(/js/jquery-1.10.2.js)]"></script>
        <script type="text/javascript" src="[!asset(/js/bootstrap.js)]"></script>
    </body>
    </html>

```

Open up the _Admin_ controller from the **controllers** folder and edit as follows:

```php

    public function index($models){

        $user = Auth::user(); /* user from session */

        if(!is_array($user)){

            $user = array();
        }

        return Response::view('admin/index', array('user' => $user, 'framework' => Jollof', 'title' => 'PHP MVC Framework'));
    }

```

Next, create a route and a controller together (at the same time) by running the below commands:

```bash

    $ php jollof make:route /tasks GET --controller

```

```bash

    $ php jollof make:route /tasks/create/@type POST

```

Then, open up the _Tasks_ controller from the **controllers** folder and edit the index file as follows:

```php

    public function index($models){

        $user = Auth::user();
        
        $resultset = TodoList::fetchWith(Todo::class, array(
                                                    'user_id' => array('=', $user['id']), 
                                                    'project_id' => array('=', '45a2cd23f08bbd6477d2ff89715cba32de')
                                            )
                     );

        return Response::json(array(
                            'status' => 'ok', 
                            'result' = array(
                                'todos' => $resultset
                            )
                )); 
    }

    public function create($models){

         $input = Request::input()->getFields(); /* get post params - 'name' */

         $type = $this->params['type'];

         $user = Auth::user(); /* user from session */

         if(!is_array($user)){

                return Response::json(array(
                                            'status' => 'error', 
                                            'result' => 'not logged in'
                                        )
                        );
         }

         $project = Project::whereBy(array(
                                    'name' => array('=', 'personal'),
                                     array('id', 'mode')
                    );

         $list = array();

         switch($type){
            case "list": /* for route -> 'tasks/create/list' */

                $list = TodoList::create(array(
                                    'name' => $input['name'], 
                                    'user_id' => $user['id']
                                )
                        );
            break;
         }

         return Response::json(array('status' => 'ok', 'result' => $list));

    }

```

Afterwards, open up the _setup.php_ file in the **routes** folder (at the bottom of the file)...

```php

    Router::bind('/tasks', array('models' => array('TodoList', 'Todo'), 'params' => array()));

    Router::bind('/tasks/create/@type', array('models' => array('Project', TodoList'), 'params' => array('type' => '/^list$/'), 'verb' => 'post'));

```

Fianlly, move in the **configs** folder (in the root), scroll down the _env.php_ file and add the last 3 lines to the **app_auth** config section (array).

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
                     '/home',
                     '/tasks',
                     '/tasks/create/@type',
                )
        ),
        
        .
        .
        .        
``` 

```php 

         $resultset = TodoList::fetchWithOrder(Todo::class, array('user_id' => array('=', '1e253fc4672bcdd61369aab8c1534bd1f08c'), 'project_id' => array('=', '45a2cd23f08bbd6477d2ff89715cba32de')), array('created_at'));   
            
            /* The above code will retrieve all [todos] created by a given user (logged user) for a given project (logged user) */

            /* SELECT * FROM `tbl_todos_list` LEFT JOIN `tbl_todos` ON `tbl_todos_list`.`id` = `tbl_todos`.`list_id` WHERE `user_id` = '1e253fc4672bcdd61369aab8c1534bd1f08c' AND `project_id` = '45a2cd23f08bbd6477d2ff89715cba32de' ORDER BY `created_at` ASC */



         $resultset = User::fetchWith(TodoList::class, array('email' => 'user@example.com'));
            
            /* The above code fetches the list of all [todo-lists] for all projects created by a given user (logged user - email) */

            /* SELECT * FROM `tbl_users` LEFT JOIN `tbl_todos_list` ON `tbl_users`.`id` = `tbl_todos_list`.`user_id` WHERE `email` = 'user@example.com' */


         $wheres = array_pluck($resultset, 'project_id', 'id');

         $resultset = Project::whereByOr(array_flatten($wheres), array('id', name'));

```

