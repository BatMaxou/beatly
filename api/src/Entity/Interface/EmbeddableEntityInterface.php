<?php

namespace App\Entity\Interface;

interface EmbeddableEntityInterface
{
    public function prepareForEmbedding(): string;
}
