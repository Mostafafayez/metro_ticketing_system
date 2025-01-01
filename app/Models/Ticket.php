<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Ticket extends Model
{
    use HasFactory;
protected $tabel='tickets';
    protected $fillable = ['name', 'stations_count', 'price','image'];

    public function user()
    {
        return $this->belongsToMany(User::class)->withPivot('id','count','status_of_payment','status_of_received');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

}
