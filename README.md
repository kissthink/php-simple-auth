# SimpleAuth Authentication
This project is a completely separate Simple Authentication library that lives 
outside your application and is controlled through a PHP API.

The API can be used to retrieve the userId of the user successfully 
authenticated.

To use from your application:

    <?php
        require_once "/path/to/php-simple-auth/lib/SimpleAuth.php";

	    $auth = new SimpleAuth();
	    try { 
            $userId = $auth->authenticate();
            echo $userId;
        } catch (SimpleAuthException $e) {
            die($e->getMessage());
        }
    ?>

To configure this application run the included configuration script:

    $ docs/configure.sh

This copies the `config/users.json.example` to `config/users.json` if the
`config/users.json` file does not yet exists and outputs a sample Apache
configuration snippet that you need to add to the Apache configuration
snippet directory.

To install Bootstrap you can use the included `docs/install_dependencies.sh`
script.

That's all! The library will take care of the redirects required and verifying 
the username/password provided by the user.

# API
The API has also the `logout()` method to log the user out of the application.
In addition it is possible to force authentication, i.e.: force the user to
type their username and password again. This can be done by calling the method
`forceAuthn(TRUE)` on the `SimpleAuth` object.

# License
Licensed under the Apache License, Version 2.0;

   http://www.apache.org/licenses/LICENSE-2.0

