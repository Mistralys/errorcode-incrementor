@echo off

cls 

echo -------------------------------------------------------
echo RUNNING PHPSTAN ANALYSIS
echo -------------------------------------------------------

echo.

call ../vendor/bin/phpstan analyse -c ./phpstan/config.neon -l 7 > ./phpstan/output.txt

start "" "phpstan/output.txt"
