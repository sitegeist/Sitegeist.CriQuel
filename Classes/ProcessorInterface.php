<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel;

use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;

interface ProcessorInterface
{
    public function process(Nodes $nodes): Nodes;
}
