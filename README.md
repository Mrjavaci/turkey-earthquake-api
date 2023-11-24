# turkey-earthquake-api

The last earthquakes that occurred in Turkey are shared by KANDİLLİ OBSERVATORY AND EARTHQUAKE RESEARCH INSTITUTE.

The API is used both to make detailed queries (by date, earthquake magnitude, location, etc.) and to add current data to
the database.

## Note

I found xml files of old earthquakes at http://udim.koeri.boun.edu.tr/zeqmap link.  
ex: http://udim.koeri.boun.edu.tr/zeqmap/xmlt/202108.xml

To be able to use the data here, you need to follow the steps below.

The following command adds the relevant tables to the database

```shell
php artisan migrate
```

The following command creates [jobs-queues](https://laravel.com/docs/10.x/queues) with xml file names with data.

```shell
php artisan app:get-earth-quakes
```

The following command runs the queue and creates the relevant data to the database.

```shell
php artisan queue:work
```

## Requirements

look composer.json

## Authors

- [@MrJavaci](https://www.github.com/Mrjavaci)

  
