<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model {
    use HasFactory;

     protected $fillable = [
        'name',
        'pastor_id',
        'description', // Adicionado para consistência com o Controller
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // RELACIONAMENTOS
    public function supervisions() {
        return $this->hasMany(Supervision::class);
    }
     public function pastor()
    {
        return $this->belongsTo(User::class, 'pastor_id');
    }

    public function cells() {
        // Assume-se que esta relação many-through está correta
        return $this->hasManyThrough(Cell::class, Supervision::class);
    }
    public function contributions() {
        return $this->hasMany(Contribution::class);
    }

    public function getTotalContributedThisMonth() {
        $now = now();
        $monthStart = $now->copy()->startOfMonth()->addDays(19);
        $monthEnd = $now->copy()->addMonth()->startOfMonth()->addDays(4);

        return $this->contributions()
            ->whereBetween('contribution_date', [$monthStart, $monthEnd])
            ->where('status', 'verificada')
            ->sum('amount');
    }

    // HELPERS
    public function getSupervisionsCount()
    {
        // ASSUMIDO: supervision tem 'is_active'
        return $this->supervisions()->where('is_active', true)->count();
    }

    public function getTotalCells()
    {
        // CORREÇÃO CRÍTICA: Removida a condição is_active para evitar o erro 1054.
        // Se a tabela 'cells' não tiver a coluna 'is_active', esta é a correção.
        return Cell::whereIn('supervision_id',
            $this->supervisions()->pluck('id')
        )->count();
    }

    public function getTotalMembers()
    {
        // CORREÇÃO CRÍTICA: Removida a condição is_active para evitar o erro 1054.
        $cellIds = Cell::whereIn('supervision_id',
            $this->supervisions()->pluck('id')
        )->pluck('id');
        
        // Mantive o filtro 'is_active' no User pois assumimos que existe no modelo User
        return User::whereIn('cell_id', $cellIds)
            ->where('is_active', true)
            ->count();
    }
}