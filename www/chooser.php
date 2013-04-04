<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Authentication</title>

        <link href="ext/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="ext/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">

        <style type="text/css">
            body {
                padding-top: 40px;
            }
            div.container {
                width: 300px;
            }
        </style>
    </head>
    <body>
        <div class="container well well-small">
            <form class="form" method="POST" action="verify.php">
                <fieldset>
                    <legend>Please choose an identity</legend>
                    <select name="user" class="input-block-level" autofocus>
                        <option value="admin">admin</option>
                        <option value="teacher">teacher</option>
                    </select>
                    <input class="btn btn-primary" type="submit" value="Continue"> 
                </fieldset>
            </form>
        </div>
    </body>
</html>
