# openToDo::Web
### (c) 2024 Jeongwoo Kim
###### An open-source project of platform-independent, web-based personal Todos management system
###### License: MIT License

> This is a project in the continuation of the terminal-based openToDo.
> Based on php 7.4, MariaDB 10, phpMyAdmin 5.2.1

> Prerequisities
>> 1. Your own web server.
>> 2. Your own database.
>> 3. Your database should have two tables named like: 'Todos' and 'Users'
>> 4. Todos table should have those columns: 'user_id', 'title', 'details', 'date_info', 'priority', 'todo_id'
>> 5. Users table should have those columns: 'user_id', 'user_email', 'user_name', 'user_password'
>> 6. Primary key of Todos table is 'todo_id', primary key of Users table is 'user_id'
>> 7. Todos table has an 'user_id' column, which is a foreign key from Users table
>> 8. PDO-based database access should be done on the file names 'dbaccess.php'

### Version History
    0.0.1
        Initial commit