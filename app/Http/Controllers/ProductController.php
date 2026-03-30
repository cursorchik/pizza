<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\ApiResponses;
use App\Services\ProductService;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    use ApiResponses;

    protected ProductService $productService;

    public function __construct(ProductService $productService) { $this->productService = $productService; }

    public function index(Request $request): JsonResponse
    {
        $products = $this->productService->getAll($request->category);
        return ProductResource::collection($products)->response()->setStatusCode(200);
    }

    /**
     * @throws AuthorizationException
     */
    public function show(Product $product): JsonResponse
    {
        $this->authorize('view', $product);
        $product->load('category', 'attributes');
        return new ProductResource($product)->response();
    }

    /**
     * @throws AuthorizationException
     */
    public function store(StoreProductRequest $request) : JsonResponse
    {
        $this->authorize('create', Product::class);

        $product = $this->productService->create($request->only(['category_id', 'name', 'price', 'attributes']));
        return $this->success($product, 'Product created', 201);
    }

    public function update(UpdateProductRequest $request, int $id) : JsonResponse
    {
        $product = $this->productService->getById($id);

        $updated = $this->productService->update($product, $request->all());
        return $this->success($updated, 'Product updated');
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(int $id) : JsonResponse
    {
        $product = $this->productService->getById($id);

        $this->authorize('delete', $product);

        $this->productService->delete($product);
        return $this->success(null, 'Product deleted');
    }
}
