Simple migration for sql files

This takes MySQL (.sql) files and run them for you so don't have to run each one manually.

Setup
* Open .env_sample, save as .env
* Change the values in the .env to match your project
* Move the bin/migrate file to your bin folder
* edit the path in the migrate file to point to migrate.php
* change permissions

```
chmod +x ~/bin/migrate
```

To run your migrations

```
migrate
```

