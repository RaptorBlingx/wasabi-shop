#!/bin/bash

SERVER_HOST="ecommercepro.orangepix.it"
SERVER_USER="wasabi"
SERVER_PATH="/var/www/vhosts/wasabi.i-dealsrl.com/httpdocs/"

THEME_DIR="themes/orange-theme-8/"
CSS_DIR=$THEME_DIR"assets/css/"
JS_DIR=$THEME_DIR"assets/js/"

CSS_FILES=("theme.css" "theme.css.map")
JS_FILES=("theme.js")

# upload css files
for file1 in "${CSS_FILES[@]}"
do
    FILE_PATH1=$CSS_DIR$file1
    scp $FILE_PATH1 $SERVER_USER@$SERVER_HOST:$SERVER_PATH$FILE_PATH1
done

# upload js files
for file2 in "${JS_FILES[@]}"
do
    FILE_PATH2=$JS_DIR$file2
    scp $FILE_PATH2 $SERVER_USER@$SERVER_HOST:$SERVER_PATH$FILE_PATH2
done

