<?php

class TagTextWrapper
{
    /** @var \TagNode[] */
    private array $chunks;

    public function __construct(string $characters, array $tokens)
    {
        $lastStyle = null;
        $this->chunks = [];
        $chunk = $currentChunk = new TagNode();
        foreach (str_split($characters, 1) as $key => $value) {
            if ($value === "\n") {
                $this->chunks[] = $chunk;
                $chunk = $currentChunk = new TagNode();
                $lastStyle = null;
                continue;
            }
            $token = $tokens[$key] ?? null;
            if (array_key_exists($key, $tokens) && $lastStyle != $token) {
                $currentChunk = new TagNode('span', $token);
                $chunk->setChild($currentChunk);
            }
            $lastStyle = $token;
            $currentChunk->setChar($value);
        }
        $this->chunks[] = $chunk;
    }

    public function getData(): string
    {
        $results = [];
        foreach ($this->chunks as $chunk) {
            $results[] = $chunk->getData();
        }

        return implode('', $results);
    }
}

