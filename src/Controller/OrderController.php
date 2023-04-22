<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
use App\Service\SoftwareService;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    /**
     * Return a user orders
     *
     * @param string $idCustomer
     * @param SoftwareService $softwareService
     *
     * @return Response
     */
    #[Route('/customers/{idCustomer}/orders', name: 'orders', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return a user orders',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'orderId',
                    type: 'int',
                ),
                new OA\Property(
                    property: 'customerId',
                    type: 'int',
                ),
                new OA\Property(
                    property: 'products',
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'productId',
                            type: 'int',
                        ),
                        new OA\Property(
                            property: 'name',
                            type: 'string',
                        ),
                        new OA\Property(
                            property: 'price',
                            type: 'int',
                        ),
                        new OA\Property(
                            property: 'description',
                            type: 'string',
                        ),
                        new OA\Property(
                            property: 'color',
                            type: 'string',
                        ),
                        new OA\Property(
                            property: 'stock',
                            type: 'int',
                        ),
                        new OA\Property(
                            property: 'createdAt',
                            type: 'string',
                        ),
                    ],
                ),
                new OA\Property(
                    property: 'createdAt',
                    type: 'string',
                ),
            ],
            example: [['orderId' => 10, 'customerId' => 5, 'products' => [['productId' => 150, 'name' => 'My Product', 'price' => 120, 'description' => 'My Product description', 'color' => 'green', 'stock' => 2000, 'createdAt' => '2023-01-01T00:00:00.000Z']], 'createdAt' => '2023-01-01T00:00:00.000Z']]
        )
    )]
    #[OA\Response(
        response: 204,
        description: 'No orders found',
    )]
    #[OA\Tag(name: 'Website')]
    #[Security(name: 'Bearer')]
    public function orders(string $idCustomer, SoftwareService $softwareService): Response
    {
        // Fetch orders
        $orders = $softwareService->orders($idCustomer);

        if (empty($orders)) {
            return new JsonResponse(status: 204);
        }

        $serializedOrders = array_map(
            function ($order) {
                return $this->serializeOrders($order);
            },
            $orders,
        );

        return $this->json($serializedOrders);
    }

    /**
     * Return a user order
     *
     * @param string $idCustomer
     * @param string $idOrder
     * @param SoftwareService $softwareService
     *
     * @return Response
     */
    #[Route('/customers/{idCustomer}/orders/{idOrder}', name: 'order', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return a user order',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'orderId',
                    type: 'int',
                ),
                new OA\Property(
                    property: 'customerId',
                    type: 'int',
                ),
                new OA\Property(
                    property: 'products',
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'productId',
                            type: 'int',
                        ),
                        new OA\Property(
                            property: 'name',
                            type: 'string',
                        ),
                        new OA\Property(
                            property: 'price',
                            type: 'int',
                        ),
                        new OA\Property(
                            property: 'description',
                            type: 'string',
                        ),
                        new OA\Property(
                            property: 'color',
                            type: 'string',
                        ),
                        new OA\Property(
                            property: 'stock',
                            type: 'int',
                        ),
                        new OA\Property(
                            property: 'createdAt',
                            type: 'string',
                        ),
                    ],
                ),
                new OA\Property(
                    property: 'createdAt',
                    type: 'string',
                ),
            ],
            example: ['orderId' => 10, 'customerId' => 5, 'products' => [['productId' => 150, 'name' => 'My Product', 'price' => 120, 'description' => 'My Product description', 'color' => 'green', 'stock' => 2000, 'createdAt' => '2023-01-01T00:00:00.000Z']], 'createdAt' => '2023-01-01T00:00:00.000Z']
        )
    )]
    #[OA\Response(
        response: 204,
        description: 'No order found',
    )]
    #[OA\Tag(name: 'Website')]
    #[Security(name: 'Bearer')]
    public function order(string $idCustomer, string $idOrder, SoftwareService $softwareService): Response
    {
        // Fetch orders
        $order = $softwareService->order($idCustomer, $idOrder);

        if (empty($order)) {
            return new JsonResponse(status: 204);
        }

        $serializedOrder = $this->serializeOrders($order);

        return $this->json($serializedOrder);
    }

    /**
     * Serialize a customer order for API return
     *
     * @param array $order
     *
     * @return array
     */
    protected function serializeOrders(array $order): array
    {
        $products = [];
        if (!empty($order['products'])) {
            foreach ($order['products'] as $product) {
                $products[] = [
                    'productId' => isset($product['id']) && is_numeric($product['id']) ? (int)$product['id'] : null,
                    'name' => !empty($product['name']) ? (string)$product['name'] : null,
                    'price' => isset($product['details']['price']) && is_numeric($product['details']['price']) ? (int)$product['details']['price'] : null,
                    'description' => !empty($product['details']['description']) ? (string)$product['details']['description'] : null,
                    'color' => !empty($product['details']['color']) ? (string)$product['details']['color'] : null,
                    'stock' => isset($product['stock']) && is_numeric($product['stock']) ? (int)$product['stock'] : null,
                    'createdAt' => !empty($product['createdAt']) ? (string)$product['createdAt'] : null,
                ];
            }
        }

        $serializedOrder = [
            'orderId' => isset($order['id']) && is_numeric($order['id']) ? (int)$order['id'] : null,
            'customerId' => isset($order['customerId']) && is_numeric($order['customerId']) ? (int)$order['customerId'] : null,
            'products' => $products,
            'createdAt' => !empty($order['createdAt']) ? (string)$order['createdAt'] : null,
        ];

        return $serializedOrder;
    }
}
