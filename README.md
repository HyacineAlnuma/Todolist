# Projet Todolist

## CONTEXT

This project was built using Symfony.
The project is about upgrading an old symfony project, implementing new functionnalities aswell as implementing tests for already existing and newly created functionnalities.


## INSTALLATION

To clone the project run the following command: 
```
git clone https://github.com/HyacineAlnuma/Projet_Symfony.git
```

To install the dependencies of the project run the following command:
```
composer install
```

### Environment variables

Create a .env.local file at the root of the project which is a copy of the .env file where you update the following variables with your own configuration:

DATABASE_URL  


### Data

To set up the databases, run following commands (make sure to run your mysql server before):

>_`dev/prod :`_
>```
>php bin/console doctrine:database:create
>php bin/console doctrine:schema:update --force
>```
>_`test :`_
>```
>php bin/console doctrine:database:create --env=test
>php bin/console doctrine:schema:update --force --env=test
>```

To load the fixtures, run the following commands:

>_`dev/prod :`_
>```
>php bin/console doctrine:fixtures:load
>```
>_`test :`_
>```
>php bin/console doctrine:fixtures:load --env=test
>```

### Test the app

Launch the symfony server:
```
symfony server:start
```

You can now test the application on https://localhost:8000/

Here are some credentials with which you can try the app:

>_`user account :`_
>```
>Username: user0
>Password: password
>```
>_`admin account :`_
>```
>Username: admin
>Password: password
>```

