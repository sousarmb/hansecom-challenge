# Hi! This is the readme file for the Hansecom challenge
Here i will detail how to setup the project, its architecture, application and current issues.
When you open the application for the first time pay no attention to the styling, very little concern was given to it.

## How to setup the project
After cloning this repository run the docker command `docker-compose up --build -d` in project's root directory. 

This will build the images and instantiate the necessary containers. Migrations and other setups are handled in scripts or in the application itself. After this no more steps need to be taken.

Some environment files are only in the repository only for a matter of convenience, under real circunstances they would be *.gitignore*'d (... talking about `.env.*` files).

## Access the project
Access the frontend [here](http://127.0.0.1:8080). To check for email sent from the application use [MailPit](http://127.0.0.1:8025) web interface.

## Use it!
Create an account using the `/register` endpoint, then `/login` and start requesting quotes. When you are done just `/logout`

## The architecture
This project has the following components:
- the web frontend;
- the quotes service;
- a worker;
- a MySQL database server;
- a RabbitMQ message broker;
- a MailPit pseudo-SMTP server.

The quotes service, worker, database server and message broker are placed in a private network, without internet access, only accesible by the web frontend.

### Frontend
The frontend registers and authenticates/authorises Client access to the application. 
Whenever a Client makes a quote request it also queues a message to the message broker with the email to be sent to the Client at a later time, with the requested quote data.
It stores Client data in its own database.
The main endpoints are:
- `/register` to register new Client;
- `/login` to access the application;
- `/home` to access the request quote and stored quote requests interface.

It uses Symfony framework.

### Service
The quotes service gets results from the external quotes provider API and stores them in its own MySQL database. 
It has 2 endpoints: 
- `POST /quote` receives quote requests from the frontend and consumes AlphaVantage API;
- `GET /quotes` to return the Client's stored quote requests.

It uses Slim framework.

### Worker
The worker consumes the message broker queue and sends the Client's emails with the quote requests.

It uses Symfony framework.

## Issues
This project has the following issues to be resolved:
- the interface sucks heavily, this was on purpose;
- Sometimes the quotes and/or worker containers fail after instantiation because the database server takes too long to start;
- Upon logging in the web frontend for the first time, javascript does not load properly, i believe this has something to do with Symfony's Turbo Drive, refresh the page and all is well;
- Could not load tests fixtures automatically (drop database >> create database >> run migrations >> load fixtures).

## TBD
- a Vue application;
- the Postman collection;
- a better suite of unit, integration and funcional tests.

# That's it!
I hope you past the issues and use this application with joy.
Thank you.
