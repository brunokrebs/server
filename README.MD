### Steps to install ###
1. Clone the repo
2. Run composer install
2. Run npm install
3. After configuring your database settings (see below) run php artisan migrate
4. Run php artisan db:seed
4. Run php artisan key:generate
5. Run php artisan serve --port=8000


### Configure the Database

If you want to use `mysql` for the example, copy the `env.example` file to `.env` and change the settings in this 
file. The default settings are:

    DB_HOST=localhost
    DB_DATABASE=homestead
    DB_USERNAME=homestead
    DB_PASSWORD=secret

After the database is configured, apply the migrations:

    php artisan migrate