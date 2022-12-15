<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string created_at
 * @property string label
 * @property string city
 * @property string district
 * @property string tax_id
 * @property string government_id
 * @property string note
 */
class Organization extends Model
{

    const UPDATED_AT   = null;
    protected $guarded = [];

}
