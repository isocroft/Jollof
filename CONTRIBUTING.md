# Jollof Code Contribution

We completely welcome your intent to contributing to the Jollof PHP Framework. We are very open about the development of the Jollof Framework. Before sending in a pull request, please ensure you read through the details below. It is a list of all relevant code requests information.

## Current Code Requests 

- Set a flag in the session which ensures that Jollof only include the 'Link' HTTP/2 header only once for each unique client user-agent (browser) [system/base/components/Response.php]
- Check to see if the 'previousRoute' session data is the same as the current route path. if so, stop Joolof from writing to the session for 'previousRoute' [system/base/providers/Core/App.php]
- Finding an intelligent way (given the current codebase) to tie only specific middleware functions to specific routes [system/base/components/System.php]
- Enhance the current query builder with 'join' function and rewrite and arrange database query builder class files better.
- Implement the PHP DOM Renderer for ReactJS server-side rendering (https://reactjs/react-php-v8js)
- Adding the command for make:component and/or redesigning the manner in which custom components will be created.
- Implement a <q>Config</q> core component for managing app-wide configurations at runtime.
- Implement a <q>jollof</q> command for dynamically generating user-defined project documentation via a documentation generating Composer package.  
- Implement a mature but simple Database Migrations system for Jollof
- Implement a <q>jollof</q> command for listing out routes currently created
- Implement [Faker.ng](https://faker.abujadevmeetup.com/) command via the exposed API
- Fixes for Bugs and Errors (very welcome)

## Addendum

If you feel that there are more ways to develop Jollof further or include code for functionality that will bring ease to developers and/or extend the cuurent offerings of Jollof framework, then, send a [mail](mailto:isocroft@gmail.com) and the community will look into it and open up a discussion with you.
