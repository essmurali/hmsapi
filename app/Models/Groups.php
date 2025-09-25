<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Groups extends Model
{
    use HasFactory; // Add this trait

    //
    protected $table = 'groups';
    protected $fillable = ['name','parent_id'];

    public function children(): HasMany
    {
        return $this->hasMany(Groups::class, 'parent_id')->with('children');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Groups::class, 'parent_id');
    }

}
