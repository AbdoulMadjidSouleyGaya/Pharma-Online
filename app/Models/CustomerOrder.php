<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerOrder extends Model
{
    protected $table = 'customer_orders';
    public $incrementing = false;       // UUID
    protected $keyType = 'string';

    /**
     * Statuts en base.
     */
    public const STATUS_EN_ATTENTE = 'en_attente';
    public const STATUS_EN_COURS   = 'en_cours';
    public const STATUS_VALIDE     = 'valide';
    public const STATUS_REJETE     = 'rejete';
    public const STATUS_ANNULEE    = 'annulee';

    /**
     * Filtres utilisés dans l'URL (ex. ?status=pending).
     */
    public const FILTER_PENDING    = 'pending';
    public const FILTER_INPROGRESS = 'inprogress';
    public const FILTER_VALIDATED  = 'validated';
    public const FILTER_REJECTED   = 'rejected';
    public const FILTER_CANCELLED  = 'cancelled';

    protected $fillable = [
        'id',
        'user_id',
        'number',
        'count',
        'total',
        'status',
        'items',
        'pharmacy_id',
        'decision_comment',
        'cashier_no',
    ];

    protected $casts = [
        'items'      => 'array',
        'cashier_no' => 'integer',
    ];

    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mappe un filtre d'URL (pending, validated, ...) vers un statut DB.
     */
    public static function mapFilterToStatus(?string $filter): ?string
    {
        $filter = strtolower((string) $filter);

        $map = [
            self::FILTER_PENDING    => self::STATUS_EN_ATTENTE,
            self::FILTER_INPROGRESS => self::STATUS_EN_COURS,
            self::FILTER_VALIDATED  => self::STATUS_VALIDE,
            self::FILTER_REJECTED   => self::STATUS_REJETE,
            self::FILTER_CANCELLED  => self::STATUS_ANNULEE,
        ];

        // Par défaut on reste sur "en_attente" pour éviter les surprises
        return $map[$filter] ?? self::STATUS_EN_ATTENTE;
    }

    /**
     * Scope : applique les filtres de pharmacie + statut (à partir du filtre URL).
     */
    public function scopeForPharmacyAndStatusFilter($query, int $pharmacyId, ?string $filter)
    {
        $query->where('pharmacy_id', $pharmacyId);

        $status = static::mapFilterToStatus($filter);

        if (! empty($status)) {
            $query->where('status', $status);
        }

        return $query;
    }

    /**
     * Compteurs de commandes par filtre pour une pharmacie donnée.
     * (utilisé dans le dashboard pharmacien + liste)
     */
    public static function countsByFilterForPharmacy(int $pharmacyId): array
    {
        return [
            self::FILTER_PENDING    => static::where('pharmacy_id', $pharmacyId)
                ->where('status', self::STATUS_EN_ATTENTE)
                ->count(),

            self::FILTER_VALIDATED  => static::where('pharmacy_id', $pharmacyId)
                ->where('status', self::STATUS_VALIDE)
                ->count(),

            self::FILTER_REJECTED   => static::where('pharmacy_id', $pharmacyId)
                ->where('status', self::STATUS_REJETE)
                ->count(),
        ];
    }
}
