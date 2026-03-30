<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Services\CartService;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiResponses;
    protected CartService $cartService;

    public function __construct(CartService $cartService) { $this->cartService = $cartService; }

    public function index() : JsonResponse
    {
        $cart = $this->cartService->formatCart($this->cartService->getCart());
        return $this->success($cart);
    }

    public function add(AddToCartRequest $request) : JsonResponse
    {
        try
        {
            $cart = $this->cartService->addItem($request->id, $request->input('quantity', 1));
            return $this->success($cart, 'Item added');
        }
        catch (\Exception $e)
        {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function update(UpdateCartItemRequest $request, int $id) : JsonResponse
    {
        try
        {
            $cart = $this->cartService->updateItem($id, $request->quantity);
            return $this->success($cart, 'Cart updated');
        }
        catch (\Exception $e)
        {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function destroy(int $id) : JsonResponse
    {
        try
        {
            $cart = $this->cartService->removeItem($id);
            return $this->success($cart, 'Item removed');
        }
        catch (\Exception $e)
        {
            return $this->error($e->getMessage(), 404);
        }
    }
}
