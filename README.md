# Starter Kit for Laravel API

-   composer install
-   php artisan l5-swagger:generate

Swagger Documentation Path

your-domain/api/documentation#/

---

## Features

### CSV Exporter


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

You can also pre declare the Exporter Class, and set the data later.

```
<?php

use App\Exports\CsvExport;

$data = [
    ['name' => 'Alice', 'email' => 'alice@example.com'],
    ['name' => 'Bob', 'email' => 'bob@example.com'],
];

$exporter = new CsvExport();
$exporter->setFileName('custom-file-name.csv');
$exporter->setData($data);
return $exporter->export();
```

### PDF Exporter

First, create a blade file that will serve as a template for the pdf.

Then place the ff code in the Module Controller.

The ff example assumes that a blade file named test.blade.php exists inside resources/view/pdf directory.

```
<?php

use App\Exports\PdfExport;

$data = ['name' => 'User Name', 'age' => '23 Years Old'];

$exporter = new PdfExport();
$exporter->setFileName('custom-file-name.pdf');
$exporter->setTemplate('pdf.test');
$exporter->setData($data);

return $exporter->export();
```

Code for both CSV and PDF Export Classes are all available inside the App\Exports namespace.

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
