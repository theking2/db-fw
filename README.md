# Projects application and api
## Configuration
Database configuration in /classes/DB/Database.php

## Discovery
Visiting /discover will get all the tables and views in the configured database and create php files in /discover/src. these should be moved to the /classes folder in the proper namespace. If new entities or views are added make sure to add them to api/index.php to enable access

## API
The api is accessible using a redirect to /api/index.php, parameter . With the `.htaccess` when accessing a non existing file or folder it will redirect the path to `/api/index.php``

Request methods allowed: GET, POST, PUT, DELETE, OPTIONS
 
The api has the following endpoints:
GET: `/api/index.php/<endpoint>[/\<id>]`
* returns a list of all entries in the database or a single object
 
GET: `/api/index.php/\<endpoint>?\<query>`
* query is a key=value pair wildcards allowed, e.g. `?Name=foo` or `?Name=foo&Age=42` or `?Name=foo*`
* returns a list of all entries in the database or a single object
* Payload ignored

POST: `/api/index.php/<endpoint>[/\<id>]`
* creates a new entry in the database
* Payload should contain complete object
 
PUT: `/api/index.php/<endpoint>`
* updates an existing entry in the database
* Payload can contain a partial object

DELETE: `/api/index.php/<endpoint>[/\<id>]`
* deletes an entry from the database
* payload ignored
 
Payload: JSON
Response: JSON array or JSON object or JSON object or error message

