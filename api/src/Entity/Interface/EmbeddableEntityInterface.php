<?php

namespace App\Entity\Interface;

use App\Enum\EmbeddingEnum;

interface EmbeddableEntityInterface
{
    public static function getClassIdentifier(): string;

    public function getUuid(): string;

    public function prepareForEmbedding(EmbeddingEnum $type): string;

    /**
     * @return EmbeddingEnum[]
     */
    public function supportEmbedding(): array;
}
