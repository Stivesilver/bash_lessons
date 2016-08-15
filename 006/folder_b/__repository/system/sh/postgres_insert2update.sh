#!/bin/bash

# add UPDATE scripts in addition to INSERT to make sure def tables are up to date
if [ -z "$1" ]
then
	echo "Usage $0 SQL_FILE_PATH"
	exit 1
fi

# tempfile
tmp_file_one=$(mktemp)
tmp_file_two=$(mktemp)
# get table name
table_name=$(cat $1 | awk '/^INSERT INTO/ {table = $3; print table; exit;}')
# get first insert into line part to avoid mess with nested sql
table_line=$(sed -n '/^INSERT INTO/{s/VALUES (.*$/VALUES/; s/(/\\(/; s/)/\\)/; p;q;}' "$1")
# get table primary key - assume it is first field in the table
table_key=$(sed -n '/^INSERT INTO/{s/^[^(]*(\([^ ]*\), .*/\1/; p;q;}' "$1")

#leave INSERT INTO only
sed -n '/^INSERT INTO '$table_name'/{p; :loop n; p; /^-- TOC entry/q; b loop}' "$1" | head -n -4 > "$tmp_file_one"

#add INSERT_END before each insert
cat "$tmp_file_one" | awk '/^'"$table_line"'/{print "INSERT_END\n"$0; next;} {print;}' > "$tmp_file_two"
cat "$tmp_file_two" > "$tmp_file_one"

#move first line to end
sed '1{H;d}; ${p;x;s/^\n//}' "$tmp_file_one" > "$tmp_file_two"
cat "$tmp_file_two" > "$tmp_file_one"

#basic inserto 2 update conversion
sed -n '/^INSERT INTO '$table_name'/{H; :loop n; H; /INSERT_END/{x; s/INSERT INTO/UPDATE/; s/(/SET (/; s/ VALUES / = /; p;}; b loop}' "$tmp_file_one" > "$tmp_file_two"
cat "$tmp_file_two" > "$tmp_file_one"

#final add WHERE statements
sed -n ':end /^UPDATE '$table_name'/{:loop N; /INSERT_END/{s/\([^=]*= (\)\([^ ]*\)\(,.*\)\(..INSERT_END\)/\1\2\3 WHERE '$table_key' = \2;/; p; n; b end;}; b loop} ' "$tmp_file_one" > "$tmp_file_two"
cat "$tmp_file_two" 
rm -f "$tmp_file_one"
rm -f "$tmp_file_two"
