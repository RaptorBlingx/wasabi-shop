#!/bin/bash

SERVER_HOST="ecommercepro.orangepix.it"
SERVER_USER="wasabi"
SERVER_PATH="/var/www/vhosts/wasabi.i-dealsrl.com/httpdocs/"

THEME_DIR="themes/orange-theme-8/"

rsync -avzP --exclude 'node_modules' $THEME_DIR $SERVER_USER@$SERVER_HOST:$SERVER_PATH$THEME_DIR
