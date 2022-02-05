<?php

namespace tmh_adapters;

class TmhRoute
{
    private $mimeType;

    public function contentType()
    {
        return $this->mimeType;
    }

    public function template($routes)
    {
        parse_str($_SERVER['REDIRECT_QUERY_STRING'], $fields);
        $this->mimeType = 'text/html';
        $forwardSlashPosition = strpos($fields['title'], "/");
        if ($forwardSlashPosition) {
            $this->mimeType = $this->getMimeType(substr($fields['title'], 0, $forwardSlashPosition));
            $fields['title'] = substr($fields['title'], $forwardSlashPosition + 1);
        }
        $routeExists = array_key_exists($fields['title'], $routes);
        $route = $routeExists ? $routes[$fields['title']] : TMH_NOT_FOUND;
        return $route['type'] ? $route['type'] . '/' . $route['template'] : $route['template'];
    }

    private function getMimeType($fileType)
    {
        $mimeTypes = ['json' => 'application/json', 'pdf' => 'application/pdf'];
        return in_array($fileType, array_keys($mimeTypes)) ? $mimeTypes[$fileType] : 'text/html';
    }
}
