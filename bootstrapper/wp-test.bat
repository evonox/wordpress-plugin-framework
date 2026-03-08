@echo off
FOR /F "usebackq delims=" %%A IN (`docker ps -q  --filter "name=tests-cli"`) DO (
    set "VYSTUP=%%A"
    goto :done
)
:done

if "%~1"=="" (
    set "arg=%~1"
    set "arg=%arg:\=/%"
    docker exec -it %VYSTUP% /var/www/html/wp-content/plugins/src/vendor/bin/phpunit -c wp-content/plugins/src/phpunit.xml.dist wp-content/plugins/src/%arg%
) else (
    docker exec -it %VYSTUP% /var/www/html/wp-content/plugins/src/vendor/bin/phpunit -c wp-content/plugins/src/phpunit.xml.dist
)
