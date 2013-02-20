#!/bin/sh
APP_NAME="php-simple-auth"

INSTALL_DIR=`pwd`

# copy default users file
(
if [ ! -f config/users.json ]
then
    cp config/users.json.example config/users.json
fi
)

# HTTPD configuration
echo "***********************"
echo "* HTTPD Configuration *"
echo "***********************"
echo "---- cut ----"
cat docs/apache.conf | sed "s|/PATH/TO/APP|${INSTALL_DIR}|g" | sed "s|APPNAME|${APP_NAME}|g"
echo "---- cut ----"
