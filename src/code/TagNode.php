<?php

class TagNode
{
    private array $children = [];
    private string|null $style = null;
    private string $chars = '';
    private string $type = 'p';

    public function __construct(string $type = 'p', string|null $style = null)
    {
        $this->type = $type;
        $this->style = $style;
    }

    public function setChar($char): void
    {
        $this->chars .= $char;
    }

    public function setChild(TagNode $node): void
    {
        $this->children[] = $node;
    }

    public function getData(): string
    {
        $prefix = "<" . $this->type;
        if ($this->style) {
            $prefix .= ' class="class-' . $this->style . '"';
        }
        $prefix .= ">";

        $data = $prefix . $this->chars;
        foreach ($this->children as $child) {
            $data .= $child->getData();
        }

        return $data . "</" . $this->type . ">";
    }

}
