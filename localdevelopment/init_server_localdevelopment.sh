#!/bin/bash -x

DIR=$(cd $(dirname $0); pwd)

## DB create 
mysql -u root -pstepmaildev -h mysqlserver -e "create database trialuser_stepmail_db;"
mysql -u root -pstepmaildev  -h mysqlserver trialuser_stepmail_db < $DIR/localdevelopment_trialuser_stepmail_db.sql

