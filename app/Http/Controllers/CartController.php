<?php

namespace App\Http\Controllers;

use App\Models\Drink;
use App\Models\Pizza;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Session;

class CartController extends Controller
{
    use ApiResponses;

    public function index() : JsonResponse
    {
        $cart = Session::get('cart', []);
        $formatted = CartController::formatCart($cart);
        return $this->success($formatted, 'Cart retrieved successfully');
    }

    public function add(Request $request) : JsonResponse
    {
        $validated = $request->validate([
            'type'     => 'required|in:pizza,drink',
            'id'       => 'required|integer',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        $type     = $validated['type'];
        $id       = $validated['id'];
        $quantity = $validated['quantity'] ?? 1;

        $model = $type === 'pizza' ? Pizza::find($id) : Drink::find($id);
        if (!$model) return $this->error("{$type} with id {$id} not found", 404);

        $cart = Session::get('cart', []);
        $key = "{$type}_{$id}";

        $totalPizzas = 0;
        $totalDrinks = 0;
        foreach ($cart as $k => $qty)
        {
            if (str_starts_with($k, 'pizza_')) $totalPizzas += $qty;
            elseif (str_starts_with($k, 'drink_')) $totalDrinks += $qty;
        }

        if ($type === 'pizza')
        {
            $newTotalPizzas = $totalPizzas + $quantity;
            if ($newTotalPizzas > 10) return $this->error('Maximum 10 pizzas allowed in cart', 422);
        }
        else
        {
            $newTotalDrinks = $totalDrinks + $quantity;
            if ($newTotalDrinks > 20) return $this->error('Maximum 20 drinks allowed in cart', 422);
        }

        $cart[$key] = ($cart[$key] ?? 0) + $quantity;
        Session::put('cart', $cart);

        return $this->success($this->formatCart($cart), 'Item added to cart');
    }

    public function update(Request $request, string $type, int $id) : JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        if (!in_array($type, ['pizza', 'drink'])) return $this->error('Invalid type', 422);

        $key = "{$type}_{$id}";
        $cart = Session::get('cart', []);

        if (!isset($cart[$key])) return $this->error('Item not found in cart', 404);

        $quantity = $request->input('quantity');

        $totalPizzas = 0;
        $totalDrinks = 0;
        foreach ($cart as $k => $qty)
        {
            if ($k === $key) continue;
            if (str_starts_with($k, 'pizza_')) $totalPizzas += $qty;
            elseif (str_starts_with($k, 'drink_')) $totalDrinks += $qty;
        }

        if ($type === 'pizza')
        {
            $newTotalPizzas = $totalPizzas + $quantity;
            if ($newTotalPizzas > 10) return $this->error('Maximum 10 pizzas allowed in cart', 422);
        }
        else
        {
            $newTotalDrinks = $totalDrinks + $quantity;
            if ($newTotalDrinks > 20) return $this->error('Maximum 20 drinks allowed in cart', 422);
        }

        if ($quantity <= 0) unset($cart[$key]);
        else $cart[$key] = $quantity;

        Session::put('cart', $cart);

        return $this->success($this->formatCart($cart), 'Cart updated');
    }

    public function destroy(string $type, int $id) : JsonResponse
    {
        if (!in_array($type, ['pizza', 'drink'])) return $this->error('Invalid type', 422);

        $key = "{$type}_{$id}";
        $cart = Session::get('cart', []);

        if (!isset($cart[$key])) return $this->error('Item not found in cart', 404);

        unset($cart[$key]);
        session()->put('cart', $cart);

        return $this->success($this->formatCart($cart), 'Item removed from cart');
    }

    /**
     * Форматирует корзину для ответа API.
     *
     * Преобразует массив корзины, где ключи вида "pizza_1", "drink_2",
     * в структурированный список с актуальными данными о товарах.
     *
     * @param array<string, int> $cart Ассоциативный массив корзины:
     *                                 ключ – строка типа "{type}_{id}", значение – количество.
     *
     * @return array<int, array{
     *     id: int,
     *     type: string,
     *     name: string,
     *     total: float,
     *     price: float,
     *     quantity: int
     * }> Массив элементов корзины. Каждый элемент содержит:
     *     - id    : идентификатор товара
     *     - type  : тип товара ('pizza' или 'drink')
     *     - name  : название товара
     *     - total : общая стоимость позиции (цена * количество)
     *     - price : цена за единицу
     *     - quantity : количество
     *     Если товар был удалён из БД, он не включается в результат.
     */
    public static function formatCart(array $cart): array
    {
        $items = [];
        foreach ($cart as $key => $quantity)
        {
            [$type, $id] = explode('_', $key);
            $model = $type === 'pizza' ? Pizza::find($id) : Drink::find($id);

            if ($model)
            {
                $items[] = [
                    'id'       => $id,
                    'type'     => $type,
                    'name'     => $model->name,
                    'total'    => $model->price * $quantity,
                    'price'    => (float) $model->price,
                    'quantity' => $quantity,
                ];
            }
        }
        return $items;
    }
}
