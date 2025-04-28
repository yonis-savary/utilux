<?php 

namespace YonisSavary\DBWand\Database\Classes;

use Generator;

class UpdateTree
{
    public Field $base;
    /** @var UpdateTree[] */
    public array $relationsToUpdate = [];

    public function __construct(Field $base)
    {
        $this->base = $base;
    }

    /**
     * @return Generator<Field>
     */
    public function goThrough(): Generator
    {
        yield $this->base;

        foreach ($this->relationsToUpdate as $subtree)
            yield from $subtree->goThrough();
    }

    public function pushSubTree(UpdateTree $subtree): self
    {
        $this->relationsToUpdate[] = $subtree;
        return $this;
    }
}