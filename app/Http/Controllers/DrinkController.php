<?php

namespace App\Http\Controllers;

use App\Models\Drink;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DrinkController extends Controller
{
    use ApiResponses;

    public function index() : JsonResponse
    {
        $pizzas = Drink::simplePaginate(15);
        return $this->success($pizzas, 'Drinks retrieved successfully');
    }

    public function store(Request $request) : JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|decimal:2',
        ]);

        $data = $request->only(['name', 'price']);
        $drink = Drink::create($data);

        return $this->success($drink, 'Drink created successfully', 201);
    }

    public function show(Drink $drink) : JsonResponse
    {
        return $this->success($drink);
    }

    public function update(Request $request, Drink $drink) : JsonResponse
    {
        $validated = $request->validate([
            'name'  => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|decimal:2',
        ]);

        if (empty($validated)) return $this->error('No fields to update provided', 422);

        $drink->update($validated);

        return $this->success($drink, 'Drink updated successfully');
    }

    public function destroy(Drink $drink) : JsonResponse
    {
        $drink->delete();
        return $this->success(null, 'Drink deleted successfully', 204);
    }
}
