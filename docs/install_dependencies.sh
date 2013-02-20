#!/bin/sh

rm -rf www/ext
mkdir -p www/ext/bootstrap

# Bootstrap
curl -o www/ext/bootstrap.zip http://twitter.github.com/bootstrap/assets/bootstrap.zip
(cd www/ext/ && unzip bootstrap.zip && rm bootstrap.zip)

