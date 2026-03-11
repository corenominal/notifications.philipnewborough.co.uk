<?php

namespace App\Models;

use CodeIgniter\Model;

class SubscriptionModel extends Model
{
    protected $table            = 'subscriptions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_uuid',
        'endpoint',
        'p256dh',
        'auth',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'user_uuid' => 'required|max_length[255]',
        'endpoint'  => 'required',
        'p256dh'    => 'required',
        'auth'      => 'required',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
