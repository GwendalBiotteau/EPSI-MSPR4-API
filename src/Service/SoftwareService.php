<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

/**
 * Software Service to fetch data from ERP & CRM
 * Simulating the software services but actually using a demo API
 */
class SoftwareService
{
    /**
     * Construct service & init services & configs
     *
     * @param HttpClientInterface $client
     * @param ContainerBagInterface $params
     * @param string $environment
     * @param string $softwareAPI
     */
    public function __construct(
        private HttpClientInterface $client,
        private ContainerBagInterface $params,
        protected string $environment,
        protected string $softwareAPI,
    ) {
    }

    /**
     * Get a list of products
     *
     * @return array
     */
    public function products(): array
    {
        $products = $this->fetch('products');

        return $products;
    }

    /**
     * Get a product details
     *
     * @param string $id
     *
     * @return array
     */
    public function product(string $id): array
    {
        $product = $this->fetch('products/' . $id);

        return $product;
    }

    /**
     * Perform an API request to the fake ERP/CRM
     *
     * @param string $route
     *
     * @return array
     */
    protected function fetch(string $route): array
    {
        $response = $this->client->request(
            method: 'GET',
            url: $this->softwareAPI . $route,
        );

        $statusCode = $response->getStatusCode();

        if ($statusCode === 200) {
            return $response->toArray();
        } elseif ($statusCode === 500) {
            return [];
        } elseif ($this->environment === 'dev') {
            throw new HttpException(
                statusCode: $statusCode,
                message: 'Error while fetching: ' . $this->softwareAPI . $route . '. Error message: ' . $response->getContent(false),
            );
        } else {
            throw new HttpException(
                statusCode: 500,
                message: 'Internal Server Error',
            );
        }
    }
}
