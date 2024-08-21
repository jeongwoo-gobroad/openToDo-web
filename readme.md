# openToDo::Web
### (c) 2024 Jeongwoo Kim
## An open-source project of platform-independent, web-based personal Todos management system
###### License: MIT License

> This is a project in the continuation of the terminal-based openToDo.
> Based on php 7.4, MariaDB 10, phpMyAdmin 5.2.1.

Please feel free to visit demo website: http://jeongwoo-kim-web.myds.me/openToDo_web/

> Prerequisities
>> 1. Your own web server.
>> 2. Your own database.
>> 3. PDO-based database access should be done on the file names 'dbaccess.php'
>> 4. Please refer to the attached [.pptx] file for the database schema.
>> 5. Interaction between your web server and your database server via localhost, or vice versa.

### Version History
##### Current version: 0.5.0

    0.0.1
        - Initial commit


    0.1.0
        - Added shareBoard Feature
        - Improved design
        - shareBoard add, create, join, list, view feature implemented.


    0.2.0
        - shareBoard edit, delete, admin privilege feature implemented.
        - The basic validation logic of the personal todo management system has been modularized into functions and converted into an API.
        - Please refer to APIs.md if you are interested.

    
    0.2.1
        - shareBoard admin privilege feature: board title edit, board deletion features has been implemented.
        - minor typo fix


    0.2.2
        - minor bug fix


    0.2.3
        - The basic logics of the shareBoard todo management system has been modularized into functions and converted into a set of APIs.
        - bug fix for shareBoard creating feature


    0.3.2
        - Threads feature implemented(Creating threads, Leaving a comment)
        - Thread editing / deleting, Thread comment editing / deleting feature will be implemented.

    
    0.5.0
        - The thread feature has been fully implemented.
        - Supported thread related features: creating a thread, leaving a comment, editing a thread, deleting a thread, editing a comment, deleting a comment
        - HTML textarea implemented.