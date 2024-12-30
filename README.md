# API Documentation

## Installation

`docker-compose up -d --build`

`docker exec -it phonebook_php composer install`

`docker exec -it phonebook_php php bin/console doctrine:schema:create`

`docker exec -it phonebook_php php bin/console doctrine:migrations:migrate`

`docker exec -it phonebook_mariadb mysql -u root -p'root' -e "CREATE DATABASE phonebook_test; GRANT ALL PRIVILEGES ON phonebook_test.* TO 'phonebook'@'%' IDENTIFIED BY 'secretpassword'; FLUSH PRIVILEGES;"
`

## Endpoints

| Endpoint          | Method(s) | Auth Required | Content-Type | URL                          |
|-------------------|-----------|---------------|--------------|------------------------------|
| Login endpoint    | POST      | NO            | json         | `/api/login_check`           |
| Register endpoint | POST      | NO            | json         | `/api/register`              |
| Get contacts list | GET       | Bearer token  | json         | `/api/phonebook`             |
| Add new contact   | POST      | Bearer token  | json         | `/api/add-contact`           |
| Update contact    | PUT       | Bearer token  | json         | `/api/add-contact`           |
| Delete contact    | DELETE    | Bearer token  | json         | `/api/add-contact`           |
| Share contact     | POST      | Bearer token  | json         | `/api/share-contact`         |
| Unshare contact   | DELETE    | Bearer token  | json         | `/api/share-contact`         |

### Login/Register
    Method: POST
    `{
        "username": "admin",
        "password": "password"
    }`
### Contacts management

#### Contacts list
    Endpoint: /api/phonebook
    Method: GET
    Authorization: Bearer token
    Info: Returns a list of contacts

#### Contact management
    Endpoint: /api/add-contact
    Method: POST
    Authorization: Bearer token
    Info: Adds a new contact
    `{
        "name": "John Doe",
        "phone": "+1234567890"
    }`

    Method: PUT
    Authorization: Bearer token
    Info: Updates an existing contact
    `{
        "id": 1,
        "name": "John Doe",
        "phone": "+1234567890"
    }`

    Method: DELETE
    Authorization: Bearer token
    Info: Deletes an existing contact
    `{
        "id": 1
    }`

#### Contact sharing

Contacts can be shared with other users by provided email. If email is not found in database, a new user will not be created.

    Endpoint: /api/share-contact
    Method: POST
    Authorization: Bearer token
    Info: Shares a contact with an email
    `{  
        "id": 1,
        "email": "IzH8P@example.com"
    }`
    
    Method: DELETE
    Authorization: Bearer token
    Info: Unshares a contact with an email
    `{  
        "id": 1,
        "email": "IzH8P@example.com"
    }`
