<?php

namespace App\Models;

use Eloquent;
use Database\Factories\OrderFactory;

use Illuminate\Support\Carbon;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $user_id
 * @property string $address
 * @property string $status
 * @property Carbon $delivery_time
 * @property-read Collection<int, Drink> $drinks
 * @property-read int|null $drinks_count
 * @property-read Collection<int, Pizza> $pizzas
 * @property-read int|null $pizzas_count
 * @property-read User $user
 * @method static OrderFactory factory($count = null, $state = [])
 * @method static Builder<static>|Order newModelQuery()
 * @method static Builder<static>|Order newQuery()
 * @method static Builder<static>|Order query()
 * @method static Builder<static>|Order whereAddress($value)
 * @method static Builder<static>|Order whereCreatedAt($value)
 * @method static Builder<static>|Order whereDeliveryTime($value)
 * @method static Builder<static>|Order whereId($value)
 * @method static Builder<static>|Order whereStatus($value)
 * @method static Builder<static>|Order whereUpdatedAt($value)
 * @method static Builder<static>|Order whereUserId($value)
 * @mixin Eloquent
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'address', 'status', 'delivery_time'];

    protected $casts = [
        'delivery_time' => 'datetime',
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pizzas() : BelongsToMany
    {
        return $this->belongsToMany(Pizza::class, 'orders_pizzas')
            ->withPivot('count')
            ->withTimestamps();
    }

    public function drinks() : BelongsToMany
    {
        return $this->belongsToMany(Drink::class, 'orders_drinks')
            ->withPivot('count')
            ->withTimestamps();
    }
}
