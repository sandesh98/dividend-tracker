# Bugiro Tracker

## About this project
The Bugiro Dividend Tracker lets you import your transactions from Degiro and shows you statistics that Degiro does not provide. Examples include your total transaction costs or total received dividends.

## Usage

### Export CSV from Degiro
1. Login to your [Degiro](https://www.degiro.nl/) account.
2. From the left sidebar click on "**inbox**" and go to "**account overview**" (*Rekeningoverzicht in Dutch*).
3. Change the startdate to your desired startdate.
4. Click on the download icon button and download as CSV. An `Account.csv` file will now be downloaded.

### Import the CSV file into this project
In this project we are using the local filesystem to import the Account.csv file, so we first have to link the storage. You can find instructions in the [Laravel documentation](https://laravel.com/docs/11.x/filesystem#the-public-disk) on how to set this up.

- You can now place the `Account.csv` file inside the `storage/app/public` folder.

### Run migrations and seeder
Assuming you have set up a database environment and completed all the necessary Composer and npm steps, you can now run the seeder to import all `Account.csv` contents into your database. You can do this by running the following command:

````
php artisan migrate:refresh --seed
````

### Fetch stock information
After seeding your database you need to run two command to get more stock information. The first command retreives general stock information like the Ticker and a display name. 

````
php artisan stock:update
````

The second command retreives the last available price for the given stock.
````
php artisan stock:price
````

#### 🐶
![Bugiro](Bugiro.png)
