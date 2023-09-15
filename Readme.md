# Sitegeist.CriQuel
## !!! This is purely experimental at the current point in time !!! 

Experimental package for querying the upcoming new CR coming of Neos 9.
The aim is to have a query language that is usable from fusion but also 
in a type safe way from php.

```php
use Sitegeist\CriQuel\Query;
use Sitegeist\CriQuel\Processor;
use Sitegeist\CriQuel\Extractor;

$result = Query::create($node)
  ->process(new Processor\Add($otherNode))
  ->process(new Processor\Remove($stuff))
  ->process(new Processor\WithDescendants('Neos.Neos:Document'))
  ->extract(new Extractor\Property("title"));
```

The equivalent fusion code looks like:

```neosfusion
value = ${crql(node).add(otherNode).remove(stuff).withDescendants('Neos.Neos:Document').get()}
```

