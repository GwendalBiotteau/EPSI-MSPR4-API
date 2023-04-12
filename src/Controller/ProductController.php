<?php

namespace App\Controller;

use App\Service\SoftwareService;
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
     * Return the list of products
     *
     * @return JsonResponse
     */
    #[Route('/products', name: 'products', methods: ['GET'])]
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
     * @param string $id
     *
     * @return JsonResponse
     */
    #[Route('/products/{id}', name: 'product', methods: ['GET'])]
    public function product(string $id): JsonResponse
    {
        // Fetch product
        $product = $this->softwareAPI->product($id);

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
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['details']['price'],
            'description' => $product['details']['description'],
            'color' => $product['details']['color'],
        ];

        // Add additional data for webshop
        if ($this->isGranted('ROLE_WEBSHOP')) {
            $serializedProduct['stock'] = $product['stock'];
            $serializedProduct['createdAt'] = $product['createdAt'];
        }

        return $serializedProduct;
    }
}
