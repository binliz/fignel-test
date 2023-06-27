<?php

require_once 'code/StyleParser.php';
require_once 'code/DocumentParser.php';
require_once 'code/TagNode.php';
require_once 'code/TagTextWrapper.php';

$documentParser = new DocumentParser('data.json');
$page = $documentParser->getPage();

$result = $documentParser->getItem($page->document->children, '0:1');
$result = $documentParser->getItem($result->children, '1:4');

$blockStyles = new StyleParser(['text' => $result->style]);
$blockStyles->addStyles($result->styleOverrideTable);

$data = json_decode(
    '{"padding": "20px", "fills": [{"color": {"r": 1,"g": 1,"b": 1,"a": 1}}],"width": 655,"height": 198,"background": {"r": 0.009250008501112461,"g": 0,"b": 0.4625000059604645,"a": 1}}'
);

$blockStyles->addStyle($data, 'text');

$currentText = new TagTextWrapper($result->characters, $result->characterStyleOverrides);
$resultStrings = $currentText->getData();

$minified = $blockStyles->getCssStyles();
$minified = str_replace(["\n", "  ", "  ", " {", "{ ", " }", "} ", ", ", "; ", ": ",],
                        ["", " ", " ", "{", "{", "}", "}", ",", ";", ":"],
                        $minified);

// Show
require_once "view.php";
