#Default ordering in Admin list view
How to set the default ordering in the Admin list view

In the admin constructor:
```php
$this->datagridValues = [
    '_page' => 1,
    '_sort_order' => 'ASC',
    '_sort_by' => 'name', // Attribute name here
];
```