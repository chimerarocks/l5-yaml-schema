#l5-yaml-schema

#Installation
###1. Install package

```
composer require chimerarocks/l5-yaml-schema
```

###2. Add provider

#####in config/app.php

```php
'providers' => [
    ...
    ChimeraRocks\YamlSchema\Providers\YamlSchemaServiceProvider::class,
],
```

###3. Publish config file and resources

```
php artisan vendor:publish
```

###4. Create schemas at properly dir, specified in config.repository.schemaPath
```
#the entity name
User:
  fields:
    name:
        #type based on Eloquent types
      type: string

  #create relationships
  hasOne:
    entity: [Car,Job]
  belongsTo:
    entity: Family

Family:
  fields:
    name:
      type: string
    address:
      type: string
      length: 255
  hasMany:
    entity: User
```