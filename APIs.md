# openToDo::Web [API Page]
### (c) 2024 Jeongwoo Kim
## An open-source project of platform-independent, web-based personal Todos management system
###### License: MIT License

> Basic validation APIs of the personal todo management system
>> 1. checkIfLoggedIn()
>>
>>      This function checks if the user has already been logged in, using $_SESSION datas.
>>      DOES NOT return any values, just redirects. 
>> 2. doesEmailAlreadyExists()
>>      
>>      This function checks if the registration datas that user typed in are valid.
>>      RETURNS: Boolean Values
>> 3. isValidLogin()
>>
>>      This function checks if the attempt to login is constructed with valid informations.
>>      RETURNS: Boolean Values