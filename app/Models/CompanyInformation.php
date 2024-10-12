<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "vision",
        "goals",
        "values",
        "address",
        "phone_number",
        "vision_image",
        "goals_image",
        "values_image",
    ];
}
