#Sonata field template vars

How to parse extra variables to a custom template for a Sonata field.
```php
<?php

/** inside the Admin class ***/
$builder->add('fieldName', 'fieldType', [
    'template' => 'your-template-file.html.twig',
    'your_template_var' => $yourTemplateVar
]
```

```twig
{{ field_description.options.types.your_template_var }}
```