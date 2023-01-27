<?php
/**
 * Payment Model file
 * php version 8.0
 *
 * @category Class
 * @package  Model
 * @author   Pelin Nikita <pelin.dev@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/pelindev/teleport.git
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * Payment class
 * php version 8.0
 *
 * @category Class
 * @package  Model
 * @author   Pelin Nikita <pelin.dev@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/pelindev/teleport.git
 */
class Payment extends Model
{
    use HasFactory;

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'amount',
        'action',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * Applies filters from the request
     *
     * @param \Illuminate\Database\Eloquent\Builder $query   query
     * @param array                                 $filters filters from request
     *
     * @return void
     **/
    public function scopeFilter(Builder $query, array $filters) : void
    {
        if (isset($filters['date'])) {
            $query->where('updated_at', 'like', $filters['date'] . '%');
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['from'])) {
            $from = str_replace('_', ' ', $filters['from']);
            $from = Carbon::parse($from);
            $from = str_contains($filters['from'], '_')
                ? $from->format('Y-m-d H:i:s')
                : $from->format('Y-m-d');

            $query->where('updated_at', '>=', $from);
        }

        if (isset($filters['to'])) {
            $to = str_replace('_', ' ', $filters['to']);
            $to = Carbon::parse($to);
            $to = str_contains($filters['to'], '_')
                ? $to->format('Y-m-d H:i:s')
                : $to->format('Y-m-d');

            $query->where('updated_at', '<=', $to);
        }
    }

    /**
     * Relationship with User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function user() : HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
