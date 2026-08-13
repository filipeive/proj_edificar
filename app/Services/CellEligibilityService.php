<?php

namespace App\Services;

use App\Models\Cell;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Fonte de verdade da elegibilidade hierárquica de células.
 *
 * Cada papel (users.role) tem um conjunto de "tipos de célula" a que pode
 * pertencer. Alguns papéis "sobem de nível" (ex.: timoteo -> também célula de
 * liderança; sub_supervisor -> também célula de pastores de zona). O método
 * utilizável por tudo (UI e validação) deve passar sempre por este serviço.
 */
class CellEligibilityService
{
    /**
     * Tipos de célula a que uma pessoa (pelo seu role) pode pertencer.
     */
    public function tiposElegiveis(string $role): array
    {
        return match ($role) {
            'membro'                          => [Cell::TYPE_MEMBROS],
            'timoteo', 'lider_celula'         => [Cell::TYPE_MEMBROS, Cell::TYPE_LIDERES],
            'supervisor'                      => [Cell::TYPE_SUPERVISORES],
            'sub_supervisor'                  => [Cell::TYPE_SUPERVISORES, Cell::TYPE_PASTORES_ZONA],
            'pastor_zona'                     => [Cell::TYPE_PASTORES_ZONA],
            'subpastor_zona'                  => [Cell::TYPE_PASTORES_ZONA, Cell::TYPE_PASTORES],
            'pastor', 'subpastor'             => [Cell::TYPE_PASTORES],
            default                           => [],
        };
    }

    /**
     * Tipos de célula que impõem a regra de "mesma zona" entre a pessoa e a célula
     * (membros, lideres, supervisores). Para pastores_zona e pastores NÃO há
     * restrição de zona (grupos regionais/gerais).
     */
    public function temRestricaoDeZona(string $type): bool
    {
        return in_array($type, [
            Cell::TYPE_MEMBROS,
            Cell::TYPE_LIDERES,
            Cell::TYPE_SUPERVISORES,
        ], true);
    }

    /**
     * Zona da pessoa, derivada da célula em que está inserida (via supervisão).
     */
    public function zonaDaPessoa(User $member): ?int
    {
        return $member->cell?->supervision?->zone_id;
    }

    /**
     * Valida se a pessoa pode ser adicionada/movida para a célula.
     * Devolve true ou uma mensagem de erro explicativa.
     */
    public function podeSerAdicionado(User $member, Cell $cell): bool|string
    {
        $tipos = $this->tiposElegiveis($member->role);

        if (! in_array($cell->type, $tipos, true)) {
            return "O papel \"{$member->role}\" de {$member->name} não permite entrar numa célula do tipo \"{$cell->type_label}\".";
        }

        if ($this->temRestricaoDeZona($cell->type)) {
            $memberZone = $this->zonaDaPessoa($member);
            $cellZone = $cell->supervision?->zone_id;

            if ($memberZone && $cellZone && $memberZone !== $cellZone) {
                return "{$member->name} pertence a outra zona e só pode ser movido(a) entre células de \"{$cell->type_label}\" da mesma zona.";
            }
        }

        if ($member->cell_id === $cell->id) {
            return "{$member->name} já pertence a esta célula.";
        }

        return true;
    }

    /**
     * Query com os membros (users) elegíveis para serem adicionados a uma célula:
     * papel elegível para o tipo + (se zona restrita) mesma zona (ou sem célula) +
     * exclui quem já está nesta célula.
     */
    public function membrosElegiveisPara(Cell $cell): Builder
    {
        $roles = $this->rolesElegiveisParaTipo($cell->type);

        $query = User::query()
            ->where('is_active', true)
            ->whereIn('role', $roles)
            ->where(function ($q) use ($cell) {
                $q->where('cell_id', '!=', $cell->id)
                    ->orWhereNull('cell_id');
            });

        if ($this->temRestricaoDeZona($cell->type)) {
            $cellZone = $cell->supervision?->zone_id;
            if ($cellZone) {
                $query->where(function ($q) use ($cellZone) {
                    $q->whereNull('cell_id')
                        ->orWhereHas('cell.supervision', fn ($q2) => $q2->where('zone_id', $cellZone));
                });
            }
        }

        return $query;
    }

    /**
     * Todos os roles cujo conjunto de tipos elegíveis contém o tipo de célula.
     */
    public function rolesElegiveisParaTipo(string $type): array
    {
        $roles = [];

        foreach ([
            'membro', 'timoteo', 'lider_celula', 'supervisor', 'sub_supervisor',
            'pastor_zona', 'subpastor_zona', 'pastor', 'subpastor',
        ] as $role) {
            if (in_array($type, $this->tiposElegiveis($role), true)) {
                $roles[] = $role;
            }
        }

        return $roles;
    }
}
