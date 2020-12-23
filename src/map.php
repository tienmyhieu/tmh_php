<?php
parse_str($_SERVER['REDIRECT_QUERY_STRING'], $fields);
$urlTitle = trim($fields['title']);
$hostLanguages = [
    'tienmyhieu.com' => 'vi'
];
$language = in_array($_SERVER['HTTP_HOST'], $hostLanguages) ? $hostLanguages[$_SERVER['HTTP_HOST']] : 'vi';
$languageTitles = [
    'vi' => [
        '' => 'Tiền mỹ hiệu'
    ]
];
$titles = $languageTitles[$language];
$title = in_array($urlTitle, $titles) ? $titles[$urlTitle] : $titles[''];
