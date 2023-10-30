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
use Sitegeist\CriQuel\Processor\AddProcessor;
use Sitegeist\CriQuel\Processor\RemoveProcessor;
use Sitegeist\CriQuel\Processor\WithDescendantsProcessor;
use Sitegeist\CriQuel\Processor\FilterProcessor;
use Sitegeist\CriQuel\Extractor\GetPropertyExtractor;

$result = Query::create($node)
  ->chain(new AddProcessor($otherNode))
  ->chain(new RemoveProcessor($stuff))
  ->chain(new WithDescendantsProcessor('Neos.Neos:Document'))
  ->chain(new FilterProcessor('Neos.Neos:Document', 'title *= "foo" OR title *= "bar"'))
  ->extract(new GetPropertyExtractor("title"));
```
The equivalent fusion code would be:
```neosfusion
value = ${crql(node).add(otherNode).remove(stuff).withDescendants('Neos.Neos:Document').filter('Neos.Neos:Document', 'title *= "foo" OR title *= "bar"').get()}
```

A more advanced example using taxonomy references could look like:

```php
use Sitegeist\CriQuel\Query;
use Sitegeist\CriQuel\Processor\ReferencingNodesProcessor;
use Sitegeist\CriQuel\Processor\WithDescendantsProcessor;
use Sitegeist\CriQuel\Processor\ReferencedByNodesProcessor;
use Sitegeist\CriQuel\Processor\UniqueProcessor;
use Sitegeist\CriQuel\Processor\RemoveProcessor;
use Sitegeist\CriQuel\Extractor\GetNodesExtractor;

$result = Query::create($documentNode)
  ->chain(new ReferencingNodesProcessor('taxonomyReferences', 'Sitegeist.Taxonomy:Taxonomy'))
  ->chain(new WithDescendantsProcessor('Sitegeist.Taxonomy:Taxonomy'))
  ->chain(new ReferencedByNodesProcessor('taxonomyReferences', 'Neos.Neos:Document'))
  ->chain(new UniqueProcessor())
  ->chain(new RemoveProcessor($documentNode))
  ->extract(new GetNodesExtractor());
```
The equivalent fusion code would be:
```neosfusion
similarDocuments = ${crql(documentNode).referencingNodes('taxonomyReferences', 'Sitegeist.Taxonomy:Taxonomy').withDescendants('Sitegeist.Taxonomy:Taxonomy').referencedByNodes('taxonomyReferences', 'Neos.Neos:Document').unique().remove(documentNode).getNodes()}
```

# CriQuel Operations ... so far

## Processors

are defined in the php namespace `Sitegeist\CriQuel\Processor`

- `new AddProcessor(Nodes|Node|Query ...$items)` 
- `new RemoveProcessor(Nodes|Node|Query ...$items)`
- `new ClosesetProcessor(NodeTypeConstraints|string $nodeTypeConstraints = null)`
- `new ParentProcessor()`
- `new UniqueProcessor()`
- `new FirstProcessor()`
- `new LastProcessor()`
- `new TetheredProcessor(NodeName|string $name)`
- `new AncestorsProcessor(NodeTypeConstraints|string $nodeTypeConstraints = null)`
- `new WithAncestorsProcessor(NodeTypeConstraints|string $nodeTypeConstraints = null)`
- `new DescendantsProcessor(NodeTypeConstraints|string $nodeTypeConstraints = null)`
- `new WithDescendantsProcessor(NodeTypeConstraints|string $nodeTypeConstraints = null)`
- `new ChildrenProcessor(NodeTypeConstraints|string $nodeTypeConstraints = null, PropertyValueCriteriaInterface|string $propertyValueCriteria)`
- `new FilterProcessor(NodeTypeConstraints|string $nodeTypeConstraints = null, PropertyValueCriteriaInterface|string $propertyValueCriteria)`
- `new ReferencingNodesProcessor(string|ReferenceName $referenceName = null, string|NodeTypeConstraints $nodeTypeConstraints = null, string|PropertyValueCriteriaInterface $nodePropertyValueCriteria= null, string|PropertyValueCriteriaInterface $referencePropertyValueCriteria = null)`
- `new ReferencedByNodesProcessor(string|ReferenceName $referenceName = null, string|NodeTypeConstraints $nodeTypeConstraints = null, string|PropertyValueCriteriaInterface $nodePropertyValueCriteria= null, string|PropertyValueCriteriaInterface $referencePropertyValueCriteria = null)`

## Extractors

are defined in the php namespace `Sitegeist\CriQuel\Extractor`

- `new GetNodesExtractor(): Nodes`
- `new GetNodeExtractor(): ?Node`
- `new GetReferencesExtractor(string|ReferenceName $referenceName): References`
- `new GetReferenceExtractor(string|ReferenceName $referenceName): ?Reference`
- `new GetPropertiesExtractor(string $name): mixed[]`
- `new GetPropertyExtractor(string $name): mixed`
- `new GetCountExtractor(): int`
