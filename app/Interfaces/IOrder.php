<?php

namespace App\Interfaces;

interface IOrder
{
    const array STATUSES = [
        'preparing',
        'delivering',
        'delivered',
        'cancelled'
    ];

    const string STATUS_PREPARING  = 'preparing';
    const string STATUS_DELIVERING = 'delivering';
    const string STATUS_DELIVERED  = 'delivered';
    const string STATUS_CANCELLED  = 'cancelled';
}
