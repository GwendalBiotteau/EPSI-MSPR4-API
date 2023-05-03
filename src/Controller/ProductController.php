<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
use App\Service\SoftwareService;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Product Controller to manage all products linked routes
 */
class ProductController extends AbstractController
{
    /**
     * Init services
     *
     * @param SoftwareService $softwareAPI
     */
    public function __construct(
        private SoftwareService $softwareAPI
    ) {
    }

    /**
     * Return the list of product
     *
     * @return JsonResponse
     */
    #[Route('/products', name: 'products', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns a list of products',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'int',
                ),
                new OA\Property(
                    property: 'name',
                    type: 'string'
                ),
                new OA\Property(
                    property: 'price',
                    type: 'int'
                ),
                new OA\Property(
                    property: 'description',
                    type: 'string'
                ),
                new OA\Property(
                    property: 'color',
                    type: 'string'
                ),
                new OA\Property(
                    property: 'stock',
                    type: 'int',
                    description: 'Only with website access'
                ),
                new OA\Property(
                    property: 'createdAt',
                    type: 'string',
                    description: 'Only with website access'
                ),
            ],
            example: [['id' => 1, 'name' => 'string', 'price' => 100, 'description' => 'string', 'color' => 'string', 'stock' => 10, 'createdAt' => '2023-01-01T00:00:00.000Z']],
        )
    )]
    #[OA\Response(
        response: 204,
        description: 'No products found',
    )]
    #[OA\Tag(name: 'Retailers')]
    #[OA\Tag(name: 'Website')]
    #[Security(name: 'Bearer')]
    public function products(): JsonResponse
    {
        // Fetch products
        $products = $this->softwareAPI->products();

        if (empty($products)) {
            return new JsonResponse(status: 204);
        }

        $serializedProducts = array_map(
            function ($product) {
                return $this->serializeProduct($product);
            },
            $products,
        );

        return $this->json($serializedProducts);
    }

    /**
     * Return a product
     *
     * @param string $idProduct
     *
     * @return JsonResponse
     */
    #[Route('/products/{idProduct}', name: 'product', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns a product',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'int',
                ),
                new OA\Property(
                    property: 'name',
                    type: 'string'
                ),
                new OA\Property(
                    property: 'price',
                    type: 'int'
                ),
                new OA\Property(
                    property: 'description',
                    type: 'string'
                ),
                new OA\Property(
                    property: 'color',
                    type: 'string'
                ),
                new OA\Property(
                    property: 'stock',
                    type: 'int',
                    description: 'Only with website access'
                ),
                new OA\Property(
                    property: 'createdAt',
                    type: 'string',
                    description: 'Only with website access'
                ),
            ],
            example: ['id' => 1, 'name' => 'string', 'price' => 100, 'description' => 'string', 'color' => 'string', 'stock' => 10, 'createdAt' => '2023-01-01T00:00:00.000Z'],
        )
    )]
    #[OA\Response(
        response: 204,
        description: 'No product found',
    )]
    #[OA\Tag(name: 'Retailers')]
    #[OA\Tag(name: 'Website')]
    #[Security(name: 'Bearer')]
    public function product(string $idProduct): JsonResponse
    {
        // Fetch product
        $product = $this->softwareAPI->product($idProduct);

        if (empty($product)) {
            return new JsonResponse(status: 204);
        }

        $serializedProduct = $this->serializeProduct($product);

        return $this->json($serializedProduct);
    }

    /**
     * Serialize a product for API return
     *
     * @param array $product
     *
     * @return array
     */
    protected function serializeProduct(array $product): array
    {
        $serializedProduct = [
            'id' => (int)$product['id'],
            'name' => (string)$product['name'],
            'price' => (float)$product['details']['price'],
            'description' => (string)$product['details']['description'],
            'color' => (string)$product['details']['color'],
        ];

        // Add additional data for webshop
        if ($this->isGranted('ROLE_WEBSHOP')) {
            $serializedProduct['stock'] = (int)$product['stock'];
            $serializedProduct['createdAt'] = (string)$product['createdAt'];
        }

        return $serializedProduct;
    }
}
