# Sitegeist.CriQuel
## !!! This is purely experimental !!! 

Experimental package for querying the upcoming new CR coming of Neos 9.
The aim is to have a query language that is usable from fusion but also 
in a type safe way from php.

```php
use Sitegeist\CriQuel\Query;
use Sitegeist\CriQuel\Operations\AddOperation;
use Sitegeist\CriQuel\Operations\RemoveOperation;

$result = Query::create($node)
  ->apply(new AddOperation($otherNode))
  ->apply(new RemoveOperation($stuff))
  ->get();
```

```neosfusion
value = ${crql(node).add(otherNode).get('foo')}
```

