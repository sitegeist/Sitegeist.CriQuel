# Sitegeist.CriQuel
## !!! This is purely experimental at the current point in time, everything can change at any time !!! 

Experimental package for querying the upcoming new CR coming of Neos 9.
The aim is to have a query language that is usable from fusion but also 
in a type safe way from php.

Improvement in regards to flowQuery:
- Offer a php interface that is as type safe as possible for Query-creation and Operations
- A clear interface for each operation that always does the same
- Allow a fluent interface in php and eel to specify the operations in the order of execution- 
- Register operations explicitly via setting, no overloading

```php
use Sitegeist\CriQuel\Query;
use Sitegeist\CriQuel\Processor;
use Sitegeist\CriQuel\Extractor;

$result = Query::create($node)
  ->chain(new Processor\Add($otherNode))
  ->chain(new Processor\Remove($stuff))
  ->chain(new Processor\WithDescendants('Neos.Neos:Document'))
  ->chain(new Processor\Filter('Neos.Neos:Document', 'title *= "foo" OR title *= "bar"'))
  ->extract(new Extractor\GetProperty("title"));
```
The equivalent fusion code would be:
```neosfusion
value = ${crql(node).add(otherNode).remove(stuff).withDescendants('Neos.Neos:Document').filter('Neos.Neos:Document', 'title *= "foo" OR title *= "bar"').get()}
```

A more advanced example using taxonomy references could look like:

```php
use Sitegeist\CriQuel\Query;
use Sitegeist\CriQuel\Processor;
use Sitegeist\CriQuel\Extractor;

$result = Query::create($documentNode)
  ->chain(new Processor\ReferencingNodes('taxonomyReferences', 'Sitegeist.Taxonomy:Taxonomy'))
  ->chain(new Processor\WithDescendants('Sitegeist.Taxonomy:Taxonomy'))
  ->chain(new Processor\ReferencedByNodes('taxonomyReferences', 'Neos.Neos:Document'))
  ->chain(new Processor\Unique())
  ->chain(new Processor\Remove($documentNode))
  ->extract(new Extractor\GetNodes());
```
The equivalent fusion code would be:
```neosfusion
similarDocuments = ${crql(documentNode).referencingNodes('taxonomyReferences', 'Sitegeist.Taxonomy:Taxonomy').withDescendants('Sitegeist.Taxonomy:Taxonomy').referencedByNodes('taxonomyReferences', 'Neos.Neos:Document').unique().remove(documentNode).getNodes()}
```

# CriQuel Operations ... so far

## Processors

are defined in the php namespace `Sitegeist\CriQuel\Processor`

- `new Add(Nodes|Node|Query ...$items)` 
- `new Remove(Nodes|Node|Query ...$items)` 
- `new Unique()`
- `new First()`
- `new Last()`
- `new Tethered(NodeName|string $name)`
- `new Ancestors(NodeTypeConstraints|string $nodeTypeConstraints = null)`
- `new WithAncestors(NodeTypeConstraints|string $nodeTypeConstraints = null)`
- `new Descendants(NodeTypeConstraints|string $nodeTypeConstraints = null)`
- `new WithDescendants(NodeTypeConstraints|string $nodeTypeConstraints = null)`
- `new Children(NodeTypeConstraints|string $nodeTypeConstraints = null, PropertyValueCriteriaInterface|string $propertyValueCriteria)`
- `new Filter(NodeTypeConstraints|string $nodeTypeConstraints = null, PropertyValueCriteriaInterface|string $propertyValueCriteria)`
- `new ReferencingNodes(string|ReferenceName $referenceName = null, string|NodeTypeConstraints $nodeTypeConstraints = null, string|PropertyValueCriteriaInterface $nodePropertyValueCriteria= null, string|PropertyValueCriteriaInterface $referencePropertyValueCriteria = null)`
- `new ReferencedByNodes(string|ReferenceName $referenceName = null, string|NodeTypeConstraints $nodeTypeConstraints = null, string|PropertyValueCriteriaInterface $nodePropertyValueCriteria= null, string|PropertyValueCriteriaInterface $referencePropertyValueCriteria = null)`

## Extractors

are defined in the php namespace `Sitegeist\CriQuel\Extractor`

- `new GetNodes(): Nodes`
- `new GetNode(): ?Node`
- `new GetReferences(string|ReferenceName $referenceName): References`
- `new GetReference(string|ReferenceName $referenceName): ?Reference`
- `new GetProperties(string $name): mixed[]`
- `new GetProperty(string $name): mixed`
- `new GetCount(): int`
