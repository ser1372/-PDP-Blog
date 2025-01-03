<?php

namespace App\Service\Api;

use jcobhams\NewsApi\NewsApi;
use jcobhams\NewsApi\NewsApiException;

class NewsService
{
    const string FORBSE = 'forbes.com';
    public function __construct(private NewsApi $newsApi)
    {}


    /**
     * @throws NewsApiException
     */
    public function getPostsByDomain(array $domain)
    {
        return $this->newsApi->getEverything(domains: implode(',',$domain), sort_by: 'publishedAt');
    }
}
