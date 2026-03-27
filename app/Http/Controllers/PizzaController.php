<?php

namespace App\Http\Controllers;

use App\Models\Pizza;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PizzaController extends Controller
{
    use ApiResponses;

    public function index() : JsonResponse
    {
        $pizzas = Pizza::simplePaginate(15);
        return $this->success($pizzas, 'Pizzas retrieved successfully');
    }

    public function store(Request $request) : JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|decimal:2',
        ]);

        $data = $request->only(['name', 'price']);
        $pizza = Pizza::create($data);

        return $this->success($pizza, 'Pizza created successfully', 201);
    }

    public function show(Pizza $pizza) : JsonResponse
    {
        return $this->success($pizza);
    }

    public function update(Request $request, Pizza $pizza) : JsonResponse
    {
        $validated = $request->validate([
            'name'  => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|decimal:2',
        ]);

        if (empty($validated)) return $this->error('No fields to update provided', 422);

        $pizza->update($validated);

        return $this->success($pizza, 'Pizza updated successfully');
    }

    public function destroy(Pizza $pizza) : JsonResponse
    {
        $pizza->delete();
        return $this->success(null, 'Pizza deleted successfully', 204);
    }
}
