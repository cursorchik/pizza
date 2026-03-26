<?php

namespace App\Models;

use Database\Factories\DrinkFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $name
 * @property float $price
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @method static \Database\Factories\DrinkFactory factory($count = null, $state = [])
 * @method static Builder<static>|Drink newModelQuery()
 * @method static Builder<static>|Drink newQuery()
 * @method static Builder<static>|Drink query()
 * @method static Builder<static>|Drink whereCreatedAt($value)
 * @method static Builder<static>|Drink whereId($value)
 * @method static Builder<static>|Drink whereName($value)
 * @method static Builder<static>|Drink wherePrice($value)
 * @method static Builder<static>|Drink whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Drink extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price'];

    public function orders() : BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'orders_drinks')
            ->withPivot('count')
            ->withTimestamps();
    }
}
