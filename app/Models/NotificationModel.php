<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid',
        'title',
        'url',
        'icon',
        'body',
        'user_uuid',
        'calltoaction',
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
        'uuid'      => 'required|max_length[255]',
        'title'     => 'required|max_length[255]',
        'url'       => 'required|max_length[255]',
        'icon'      => 'permit_empty|max_length[255]',
        'body'      => 'required',
        'user_uuid' => 'required|max_length[255]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
