<?php

namespace Eudovic\PrometheusPHP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MetricsTokens extends Model
{

    use SoftDeletes;

    protected $table = 'metric_auth_token';

    protected $fillable = [
        'auth_token',
    ];

}
