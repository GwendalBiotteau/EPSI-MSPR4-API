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
     * Get a list of customers
     *
     * @return array
     */
    public function customers(): array
    {
        $customers = $this->fetch('customers');

        return $customers;
    }

    /**
     * Get a customer by id
     *
     * @param string $id
     *
     * @return array
     */
    public function customer(string $id): array
    {
        $customer = $this->fetch('customers/' . $id);

        return $customer;
    }

    /**
     * Get a user orders with products
     *
     * @param string $idUser
     *
     * @return array
     */
    public function orders(string $idUser): array
    {
        // Fetch orders
        $orders = $this->fetch('customers/' . $idUser . '/orders');

        // Insert products in orders
        foreach ($orders as &$order) {
            if (!empty($order['id'])) {
                $products = $this->fetch('customers/' . $idUser . '/orders/' . $order['id'] . '/products');
                if (!empty($products)) {
                    $order['products'] = $products;
                }
            }
        }

        return $orders;
    }

    /**
     * Get a user order with products
     *
     * @param string $idUser
     * @param string $idOrder
     *
     * @return array
     */
    public function order(string $idUser, string $idOrder): array
    {
        $order = $this->fetch('customers/' . $idUser . '/orders/' . $idOrder);

        if (!empty($order['id'])) {
            $products = $this->fetch('customers/' . $idUser . '/orders/' . $idOrder . '/products');
            if (!empty($products)) {
                $order['products'] = $products;
            }
        }

        return $order;
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
