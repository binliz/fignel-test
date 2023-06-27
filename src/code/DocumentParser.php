<?php

class DocumentParser
{
    private stdClass $page;

    public function __construct(string $filename)
    {
        if (!is_file($filename)) {
            throw new RuntimeException('File not found');
        }

        $data = file_get_contents($filename);
        $page = json_decode($data);

        if (!is_object($page)) {
            throw new RuntimeException('File is not json');
        }

        $this->page = $page;
    }

    public function getPage(): stdClass
    {
        return $this->page;
    }

    /** When need to parse all document may be need realize other methods not getItem */
    function getItem($object, $id)
    {
        foreach ($object as $item) {
            if ($item->id == $id) {
                return $item;
            }
        }
        return null;
    }
}
