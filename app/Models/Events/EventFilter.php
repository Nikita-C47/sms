<?php

namespace App\Models\Events;

use Illuminate\Database\Eloquent\Model;

class EventFilter extends Model
{
    const FILTERS = [
        'date_from',
        'date_to',
        'boards',
        'captains',
        'airports',
        'statuses',
        'responsible_departments',
        'users',
        'relations',
        'attachments'
    ];

    const SINGLE_FILTERS = [
        'date_from',
        'date_to',
        'attachments'
    ];

    protected $fillable = ['user_id', 'key', 'value'];
}
