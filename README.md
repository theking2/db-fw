# projects application and api
## configuration
Most important database configuration in /classes/DB/Database.php

## discovery
Visiting /discover witll get a all the tables and views in the configured database and create php files in /discover/src. these should be moved to the /classes folder in the proper namespace. If new entities or view are added make sure to add them to api/index.php to enable access

## api
The api is accessible using a redirect to /api/index.php when accessing a non existing file or folder. 
