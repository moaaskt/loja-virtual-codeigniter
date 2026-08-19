<?php

namespace App\Models;

use CodeIgniter\Model;

class CupomModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'cupons';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'codigo',
        'tipo',
        'valor',
        'valor_minimo_pedido',
        'limite_uso',
        'vezes_usado',
        'data_validade',
        'ativo',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';

    // Validation
    protected $validationRules = [
        'codigo'              => 'required|min_length[3]|max_length[50]|is_unique[cupons.codigo,id,{id}]',
        'tipo'                => 'required|in_list[fixo,porcentagem]',
        'valor'               => 'required|decimal|greater_than[0]',
        'valor_minimo_pedido' => 'permit_empty|decimal',
        'limite_uso'          => 'permit_empty|integer',
        'data_validade'       => 'permit_empty|valid_date[Y-m-d]',
        'ativo'               => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'codigo' => [
            'required'    => 'O código do cupom é obrigatório.',
            'min_length'  => 'O código deve ter no mínimo 3 caracteres.',
            'max_length'  => 'O código deve ter no máximo 50 caracteres.',
            'is_unique'   => 'Este código de cupom já está em uso.',
        ],
        'tipo' => [
            'required' => 'Selecione o tipo do cupom (fixo ou porcentagem).',
            'in_list'  => 'O tipo deve ser fixo ou porcentagem.',
        ],
        'valor' => [
            'required'     => 'O valor do desconto é obrigatório.',
            'decimal'      => 'Insira um valor numérico válido.',
            'greater_than' => 'O valor do desconto deve ser maior que zero.',
        ],
    ];

    /**
     * Valida um cupom para um determinado subtotal.
     * Retorna array com ['valido' => bool, 'cupom' => array|null, 'desconto' => float, 'erro' => string, 'mensagem' => string]
     */
    public function validarCupom(string $codigo, float $subtotal): array
    {
        $codigoLimpo = strtoupper(trim($codigo));

        if (empty($codigoLimpo)) {
            return [
                'valido'   => false,
                'erro'     => 'Informe o código do cupom.',
                'cupom'    => null,
                'desconto' => 0.0,
            ];
        }

        $cupom = $this->where('codigo', $codigoLimpo)->where('ativo', 1)->first();

        if (!$cupom) {
            return [
                'valido'   => false,
                'erro'     => 'Cupom inválido ou não encontrado.',
                'cupom'    => null,
                'desconto' => 0.0,
            ];
        }

        // Validação de data de validade
        if (!empty($cupom['data_validade'])) {
            $hoje = date('Y-m-d');
            if ($hoje > $cupom['data_validade']) {
                return [
                    'valido'   => false,
                    'erro'     => 'Este cupom expirou em ' . date('d/m/Y', strtotime($cupom['data_validade'])) . '.',
                    'cupom'    => null,
                    'desconto' => 0.0,
                ];
            }
        }

        // Validação de limite de uso
        if (!empty($cupom['limite_uso']) && (int) $cupom['limite_uso'] > 0) {
            if ((int) $cupom['vezes_usado'] >= (int) $cupom['limite_uso']) {
                return [
                    'valido'   => false,
                    'erro'     => 'Este cupom atingiu o limite máximo de utilizações.',
                    'cupom'    => null,
                    'desconto' => 0.0,
                ];
            }
        }

        // Validação de valor mínimo de pedido
        $valorMinimo = (float) ($cupom['valor_minimo_pedido'] ?? 0);
        if ($valorMinimo > 0 && $subtotal < $valorMinimo) {
            return [
                'valido'   => false,
                'erro'     => 'O valor mínimo do pedido para este cupom é de R$ ' . number_format($valorMinimo, 2, ',', '.') . '.',
                'cupom'    => null,
                'desconto' => 0.0,
            ];
        }

        // Cálculo do desconto
        $desconto = 0.0;
        if ($cupom['tipo'] === 'porcentagem') {
            $desconto = round(($subtotal * (float) $cupom['valor']) / 100, 2);
        } else {
            $desconto = min($subtotal, (float) $cupom['valor']);
        }

        return [
            'valido'   => true,
            'cupom'    => $cupom,
            'desconto' => $desconto,
            'mensagem' => 'Cupom "' . esc($cupom['codigo']) . '" aplicado com sucesso!',
        ];
    }

    /**
     * Incrementa o contador de utilizações do cupom.
     */
    public function incrementarUso(int $cupomId): bool
    {
        return (bool) $this->where('id', $cupomId)
            ->set('vezes_usado', 'vezes_usado + 1', false)
            ->update();
    }
}
