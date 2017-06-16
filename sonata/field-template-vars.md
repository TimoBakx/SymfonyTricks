# Sonata field template vars

## Description
Sometimes you will need additional variables in your field templates. This is how you can add those.

## Code
Inside the Admin class, in the configureShowFields or configureFormFields methods:
```php
$builder->add('fieldName', 'fieldType', [
    'template' => 'your-template-file.html.twig',
    'your_template_var' => $yourTemplateVar
]);
```

In the your-template-file.html.twig template:
```twig
{{ field_description.options.types.your_template_var }}
```
