# Starter Kit for Laravel API

-   composer install
-   php artisan l5-swagger:generate

Swagger Documentation Path

your-domain/api/documentation#/

---

## Features

### Exporter


Example: Place the ff code in the Module Controller.

```
<?php

use App\Exports\CsvExport;

$data = [
    ['name' => 'Alice', 'email' => 'alice@example.com'],
    ['name' => 'Bob', 'email' => 'bob@example.com'],
];

$exporter = new CsvExport($data);
$exporter->setFileName('custom-file-name.csv');
return $exporter->export();
```

When executed, this code will download a csv file, which can then be imported to different spreadsheet softwares.

Code for this is available inside the App\Exports namespace.

---

## Generate Management CRUD with minimal Swagger Docs

---

## Suggestions

install extension for folder swagger annotations
link: https://drive.google.com/drive/folders/1uNjC8CUp79EM79ZfGAtyhxzxhgj66yxR?usp=sharing
command to install: code --install-extension lara-swaggyfold-0.0.1.vsix
shortcuts to toggle fold: ctrl + shift + o

---

## To generate ERD

`php artisan generate:erd`
