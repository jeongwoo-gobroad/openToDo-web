# openToDo::Web [Methods Page]
### (c) 2024 Jeongwoo Kim
## An open-source project of platform-independent, web-based personal Todos management system
###### License: MIT License

> Basic validation Methods of the personal todo management system
> 1. checkIfLoggedIn()
>>
>>      This function checks if the user has already been logged in, using $_SESSION datas.
>>      RETURNS: DOES NOT return any values, just redirects.

> 2. doesEmailAlreadyExists()
>>      
>>      This function checks if the registration datas that user typed in are valid.
>>      RETURNS: Boolean Values

> 3. isValidLogin()
>>
>>      This function checks if the attempt to login is constructed with valid informations.
>>      RETURNS: Boolean Values


> Basic Methods for the shareBoard todo management system
> 1. checkGetBoardDataExists()
>>
>>      This function checks if $_GET has an appropriate board id data.
>>      RETURNS: Boolean Values

> 2. checkGetTodoExists()
>>
>>      This function checks if $_GET has an appropriate todo id data.
>>      RETURNS: Boolean Values

> 3. checkBoardAccessPermission($pdo_object, $shareBoard_id)
>>
>>      This function checks if the session user has the permission to access the board.
>>      RETURNS: Boolean Values

> 4. getShareboardTitle($pdo_object, $shareBoard_id)
>>      
>>      This function returns shareBoard title in string format.
>>      RETURNS: String, or if failed, redirection to the main page occurs.

> 5. checkUserAbleToAlter($pdo_object, $todo_id)
>>
>>      This function checks if the user is able to alter the given todo_id datas.
>>      RETURNS: Boolean Values

> 6. doesTitleAlreadyExists($title, $pass, $pdo_object)
>>
>>      This function checks if the board title is an unique title.
>>      This feature will be deprecated, since titles don't need to be unique.
>>      RETURNS: Boolean Values

> 7. checkUserIsAdmin($pdo_object, $shareBoard_id)
>>
>>      This function checks if the session user is the admin of the given shareBoard.
>>      RETURNS: Boolean Values


> Basic Methods for the thread management system
> 1. checkGetThreadExists()
>>
>>      This function checks if $_GET has an appropriate thread id data.
>>      RETURNS: Boolean Values

> 2. checkThreadPermission($pdo, $shareBoard_id)
>>
>>      This function checks if the session user has the permission to access the thread.
>>      RETURNS: Boolean Values

> 3. getThreadTitle($pdo, $thread_id)
>>
>>      This function returns thread title in string format.
>>      RETURNS: String, or if failed, redirection to the main page occurs.

> 4. checkUserIsAdminOfTheThread($pdo, $thread_id)
>>      
>>      This function returns shareBoard title in string format.
>>      RETURNS: String, or if failed, redirection to the main page occurs.

> 5. getThreadDetails($pdo, $thread_id)
>>
>>      This function returns thread details in string format.
>>      RETURNS: String, or if failed, redirection to the main page occurs.

> 6. getThreadRecent($pdo, $thread_id)
>>
>>      This function returns the value when the thread was last updated in string format.
>>      RETURNS: String, or if failed, redirection to the main page occurs.

> 7. getCommentOwner($pdo, $comment_id)
>>
>>      This function returns the user_id of the given comment_id.
>>      RETURNS: Integer, or if failed, redirection to the main page occurs.

> 8. getCommentDetails($pdo, $comment_id)
>>
>>      This function returns the detailed content of the given comment_id.
>>      RETURNS: String, or if failed, redirection to the main page occurs.