# Sitegeist.CriQuel
## !!! This is purely experimental at the current point in time !!! 

Experimental package for querying the upcoming new CR coming of Neos 9.
The aim is to have a query language that is usable from fusion but also 
in a type safe way from php.

```php
use Sitegeist\CriQuel\Query;
use Sitegeist\CriQuel\Operations\Add;
use Sitegeist\CriQuel\Operations\Remove;
use Sitegeist\CriQuel\Operations\WithDescendants;
use Sitegeist\CriQuel\Extractor\Property;

$result = Query::create($node)
  ->apply(new Add($otherNode))
  ->apply(new Remove($stuff))
  ->apply(new WithDescendants('Neos.Neos:Document'))
  ->extract(new Property("title"));
```

The equivalent fusion code looks like:

```neosfusion
value = ${crql(node).add(otherNode).remove(stuff).withDescendants('Neos.Neos:Document').get()}
```

