<?php

namespace App\Models;

use CodeIgniter\Model;

class PagamentoModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'pagamentos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'pedido_id',
        'metodo',
        'status',
        'valor',
        'transacao_id',
        'pix_copiacola',
        'pix_qrcode_base64',
        'pix_expiracao',
        'cartao_ultimos_digitos',
        'cartao_bandeira',
        'cartao_parcelas',
        'detalhes_json',
        'pago_em',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';

    public function buscarPorTransacao(string $transacaoId): ?array
    {
        return $this->where('transacao_id', $transacaoId)->first();
    }

    public function buscarPorPedido(int $pedidoId): ?array
    {
        return $this->where('pedido_id', $pedidoId)->orderBy('id', 'DESC')->first();
    }

    public function marcarComoPago(int $pagamentoId, ?string $pagoEm = null): bool
    {
        return $this->update($pagamentoId, [
            'status'  => 'pago',
            'pago_em' => $pagoEm ?? date('Y-m-d H:i:s'),
        ]);
    }

    public function marcarComoFalho(int $pagamentoId, string $motivo = ''): bool
    {
        $dados = ['status' => 'falhou'];
        if (!empty($motivo)) {
            $atual = $this->find($pagamentoId);
            $detalhes = [];
            if (!empty($atual['detalhes_json'])) {
                $detalhes = json_decode($atual['detalhes_json'], true) ?? [];
            }
            $detalhes['motivo_falha'] = $motivo;
            $dados['detalhes_json'] = json_encode($detalhes);
        }

        return $this->update($pagamentoId, $dados);
    }
}
