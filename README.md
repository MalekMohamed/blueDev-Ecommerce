<p align="center"><a href="https://still-savannah-89535.herokuapp.com" target="_blank"><img src="https://still-savannah-89535.herokuapp.com/images/logo.png" width="400"></a></p>
## How to Set Up



1. Clone the Git Repository:

   Run ```git clone https://github.com/MalekMohamed/blueDev-app.git```

2. cd into the project directory:

   Run ```cd blueDev-app```

## Backend

3. Copy .env.example file and rename as .env; 
Alternatively, run ```cp .env.example .env```

4. Edit database credentials in your newly generated/created .env file

5. Run ```composer install``` to install all libraries and dependencies in the composer.lock file

6. Run ```php artisan key:generate``` to set the APP_KEY value in the .env file

7. Having created a database, and specifying the same with the right credentials in your .env file, run ```php artisan migrate``` to create the tables

8. Run ```php artisan passport:keys``` to set the Passport_KEYS.

9. Run ```php artisan passport:client --personal``` and set the Personal key to ```authToken``` to set the Passport personal key.

10. Run ```php artisan serve``` to run the PHP development server. Alternatively, you can run your project with XAMPP or WAMP.

## Frontend

11. Run ```npm install``` to download all packages and dependencies needed for our client

12. While making sure that the API (Laravel) Server is up and running, run ```npm run dev``` to start your Vue application

13. Congratulations!!!
## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
