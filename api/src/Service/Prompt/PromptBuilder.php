<?php

namespace App\Service\Prompt;

class PromptBuilder
{
    public function __construct(
        private readonly string $basePath,
    ) {
    }
    
    /**
    * @param array<string, string> $context
    */
    public function build(PromptEnum $type, array $context = []): string
    {
        $path = $this->basePath . '/' . $type->value; 
        if (!file_exists($path)) {
            throw new \Exception('Le fichier prompt.md n\'existe pas à la racine du projet');
        }
        
        $promptContent = file_get_contents($path);
        if (!$promptContent) {
            throw new \Exception('Impossible de lire le fichier prompt.md');
        }

        return str_replace(
            array_keys($context),
            array_values($context),
            $promptContent
        );
    }
}
