#!/bin/bash

if [ -z "$1" ]
then
	echo "Usage $0 SQL_FILE_PATH"
	exit 1
fi

if [ ! -e "$1" ] 
then
	echo "File does not exist"
	exit 1
fi

tmp_file_out=$(mktemp)        #file for temp modified script
cat $1 | awk '{print}; /^CREATE TABLE/ {table = $3; script = ""}; !/^CREATE|^);|CONSTRAINT|nextval/ {sub(/^    /,"",$0);sub(/,$/,"",$0); script = script"ALTER TABLE "table" ADD COLUMN "$0";\n"}; /);/ {if (table != "") print script; script=""; table="";};' 
rm -f "$tmp_file_out"
