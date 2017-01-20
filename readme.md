# Task Management Laravel Project

## DB Configuration 
* Create a MySQL DB, 
    * db_name: ```task_management``` 
    * db_pass: ``` null```
    * // if you want to change DB configuration you should do it on ```.env``` file
* Run ```php artisan migrate```
* Run ```php artisan db:seed --class=UsersTableSeeder```

### Create a (hidden file) file and name it ```.env``` 
* Add this content into the file
``` 
DB_CONNECTION=mysql
DB_HOST=your_db_host
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```
**do not set empty spaces after '=' sign**
## Mailgun Configuration 
### Create a new account to https://www.mailgun.com/ 
* append these lines to ```.env``` file
```
MAIL_DRIVER=mailgun
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=postmaster@sandbox...
MAIL_PASSWORD=1e9b46fd3c6b7...
MAIL_ENCRYPTION=tls
```
