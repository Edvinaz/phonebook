# API Documentation

This document provides an overview of the available API endpoints in this project.

## Endpoints

| Endpoint          | Method(s) | Auth Required | Content-Type | URL                          |
|-------------------|-----------|---------------|--------------|------------------------------|
| Login endpoint    | POST      | NO            | ANY          | `/api/login_check`           |
| Register endpoint | POST      | NO            | ANY          | `/api/register`              |
| Get contacts list | GET       | Bearer token  | ANY          | `/api/phonebook`             |
| Add new contact   | POST      | Bearer token  | ANY          | `/api/add-contact`           |
| Update contact    | PUT       | Bearer token  | ANY          | `/api/add-contact`           |
| Delete contact    | DELETE    | Bearer token  | ANY          | `/api/add-contact`           |
| Share contact     | POST      | Bearer token  | ANY          | `/api/share-contact`         |
| Unshare contact   | DELETE    | Bearer token  | ANY          | `/api/share-contact`         |


Feel free to extend or modify this documentation as the API evolves!
