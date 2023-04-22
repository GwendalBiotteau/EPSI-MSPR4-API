<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
use App\Service\SoftwareService;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CustomerController extends AbstractController
{
    /**
     * Return the list of customers
     *
     * @param SoftwareService $softwareAPI
     *
     * @return JsonResponse
     */
    #[Route('/customers', name: 'customers', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return the list of customers',
        content: new OA\JsonContent(
            required: ['id'],
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'int',
                ),
                new OA\Property(
                    property: 'email',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'name',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'username',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'firstName',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'lastName',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'phone',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'address',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'company',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'postalCode',
                    type: 'int',
                ),
                new OA\Property(
                    property: 'city',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'createdAt',
                    type: 'string',
                ),
            ],
            example: [['id' => 12, 'email' => 'demo@email.com', 'name' => 'John', 'username' => 'john.doe', 'firstName' => 'John', 'lastName' => 'Doe', 'phone' => '666-666-6661', 'address' => '150 example street', 'company' => 'Fake Industries', 'postalCode' => 43769, 'city' => 'Example City', 'createdAt' => '2023-01-01T00:00:00.000Z']]
        )
    )]
    #[OA\Response(
        response: 204,
        description: 'No customers found',
    )]
    #[OA\Tag(name: 'Website')]
    #[Security(name: 'Bearer')]
    public function customers(SoftwareService $softwareAPI): JsonResponse
    {
        // Fetch customers
        $customers = $softwareAPI->customers();

        if (empty($customers)) {
            return new JsonResponse(status: 204);
        }

        // Serialize customers
        $serializedCustomers = array_map(
            function ($customer) {
                return $this->serializeCustomer($customer);
            },
            $customers,
        );

        // Remove rows without id
        $requiredCustomers = array_values(array_filter($serializedCustomers, fn ($customer) => !empty($customer['id'])));

        return $this->json($requiredCustomers);
    }

    /**
     * Return a customer
     *
     * @param string $idCustomer
     * @param SoftwareService $softwareAPI
     *
     * @return JsonResponse
     */
    #[Route('/customers/{idCustomer}', name: 'customer', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return a customer',
        content: new OA\JsonContent(
            required: ['id'],
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'int',
                ),
                new OA\Property(
                    property: 'email',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'name',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'username',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'firstName',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'lastName',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'phone',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'address',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'company',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'postalCode',
                    type: 'int',
                ),
                new OA\Property(
                    property: 'city',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'createdAt',
                    type: 'string',
                ),
            ],
            example: ['id' => 12, 'email' => 'demo@email.com', 'name' => 'John', 'username' => 'john.doe', 'firstName' => 'John', 'lastName' => 'Doe', 'phone' => '666-666-6661', 'address' => '150 example street', 'company' => 'Fake Industries', 'postalCode' => 43769, 'city' => 'Example City', 'createdAt' => '2023-01-01T00:00:00.000Z']
        )
    )]
    #[OA\Response(
        response: 204,
        description: 'No customer found',
    )]
    #[OA\Tag(name: 'Website')]
    #[Security(name: 'Bearer')]
    public function customer(string $idCustomer, SoftwareService $softwareAPI): JsonResponse
    {
        // Fetch customers
        $customer = $softwareAPI->customer($idCustomer);

        if (empty($customer) || empty($customer['id'])) {
            return new JsonResponse(status: 204);
        }

        // Serialize customers
        $serializedCustomer = $this->serializeCustomer($customer);

        return $this->json($serializedCustomer);
    }

    /**
     * Serialize a customer for API return
     *
     * @param array $customer
     *
     * @return array
     */
    protected function serializeCustomer(array $customer): array
    {
        // Serialize received data 
        $serializedCustomer = [
            'id' => isset($customer['id']) && is_numeric($customer['id']) ? (int)$customer['id'] : null,
            'email' => !empty($customer['email']) ? (string)$customer['email'] : null,
            'name' => !empty($customer['name']) ? (string)$customer['name'] : null,
            'username' => !empty($customer['username']) ? (string)$customer['username'] : null,
            'firstName' => !empty($customer['firstName']) ? (string)$customer['firstName'] : null,
            'lastName' => !empty($customer['lastName']) ? (string)$customer['lastName'] : null,
            'phone' => !empty($customer['phone']) ? (string)$customer['phone'] : null,
            'address' => !empty($customer['address']) && is_string($customer['address']) ? (string)$customer['address'] : null,
            'company' => !empty($customer['company']['companyName']) ? (string)$customer['company']['companyName'] : null,
            'postalCode' => isset($customer['address']['postalCode']) && is_numeric($customer['address']['postalCode']) ? (int)$customer['address']['postalCode'] : null,
            'city' => !empty($customer['address']['city']) ? (string)$customer['address']['city'] : null,
            'createdAt' => !empty($customer['createdAt']) ? (string)$customer['createdAt'] : null,
        ];

        // Remove null values
        $serializedCustomer = array_filter($serializedCustomer, fn($param) => !is_null($param));

        if (!empty($customer['orders'])) {
            $serializedCustomer['nbOrders'] = count($customer['orders']);
        }

        return $serializedCustomer;
    }
}
