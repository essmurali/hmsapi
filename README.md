# REST API - Hospital Management System - Lumen Framework

The REST API developed in Lumen framework to represent different hospitals and group of clinicians within those hospitals. The API is developed with **JWT token based authentication** to validate incoming requests. 

For JWT Token based authentication in Lumen added a dependancy **tymon/jwt-auth**.

To start the development environment run the command
**php -S localhost:8000 -t public**

To create the tables "users" and "groups", run the artisan command
**php artisan migrate**

To dump sample records into the database as a start, run the seeder commands
**php artisan make:seeder GroupsSeeder**

**php artisan make:seeder UsersTableSeeder**

## API Endpoints
The created API endpoints are 


**POST**
>> localhost:8000/api/login?email=murali@gmail.com&password=123456

It returns "access_token" for validation of subsequent requests.


**GET**
>> localhost:8000/api/groups

It returns JSON response
{
    "status": "success",
    "result": [
        {
            "Hospital A": [
                {
                    "Stomach": [
                        "Crohn's Disease",
                        "Ulcerative Colitis"
                    ]
                }
            ]
        },
        {
            "Hospital B": [
                "Gaming addiction"
            ]
        },
        {
            "Hospital C": [
                "Heart"
            ]
        }
    ]
}


**DELETE**
>> localhost:8000/api/groups/{groupid}

Returns 200 response and the passed groupid gets deleted


**PUT**
>> localhost:8000/api/groups/1222?name=Chettinad&parent_name=Hospital B

Validate the group name and update the details


**POST**
>> localhost:8000/api/groups/create?name=Heart&parent_name=Cardiology

Returns 201, if parent_name is null set it as main group (Hospital) otherwise added the clinician group under the main group




