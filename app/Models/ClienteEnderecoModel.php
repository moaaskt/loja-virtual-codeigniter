<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteEnderecoModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'cliente_enderecos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'usuario_id',
        'titulo',
        'destinatario',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'padrao',
        'criado_em',
        'atualizado_em',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';

    // Validation
    protected $validationRules = [
        'usuario_id' => 'required|integer',
        'cep'        => 'required|min_length[8]|max_length[10]',
        'logradouro' => 'required|min_length[3]|max_length[255]',
        'numero'     => 'required|max_length[30]',
        'bairro'     => 'required|min_length[2]|max_length[100]',
        'cidade'     => 'required|min_length[2]|max_length[100]',
        'uf'         => 'required|exact_length[2]',
    ];

    public function getEnderecosPorUsuario(int $usuarioId): array
    {
        return $this->where('usuario_id', $usuarioId)
                    ->orderBy('padrao', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->findAll();
    }

    public function getEnderecoPadrao(int $usuarioId): ?array
    {
        $padrao = $this->where('usuario_id', $usuarioId)
                       ->where('padrao', 1)
                       ->first();

        if ($padrao) {
            return $padrao;
        }

        return $this->where('usuario_id', $usuarioId)
                    ->orderBy('id', 'ASC')
                    ->first();
    }

    public function definirComoPadrao(int $enderecoId, int $usuarioId): bool
    {
        // Remove padrão dos outros endereços do usuário
        $this->where('usuario_id', $usuarioId)->set(['padrao' => 0])->update();

        // Define o selecionado como padrão
        return $this->where('id', $enderecoId)
                    ->where('usuario_id', $usuarioId)
                    ->set(['padrao' => 1])
                    ->update();
    }

    public function salvarEndereco(int $usuarioId, array $dados, ?int $enderecoId = null): array
    {
        $dadosLimpos = [
            'usuario_id'   => $usuarioId,
            'titulo'       => !empty($dados['titulo']) ? trim($dados['titulo']) : 'Meu Endereço',
            'destinatario' => !empty($dados['destinatario']) ? trim($dados['destinatario']) : null,
            'cep'          => preg_replace('/\D/', '', $dados['cep'] ?? ''),
            'logradouro'   => trim($dados['logradouro'] ?? ''),
            'numero'       => trim($dados['numero'] ?? ''),
            'complemento'  => !empty($dados['complemento']) ? trim($dados['complemento']) : null,
            'bairro'       => trim($dados['bairro'] ?? ''),
            'cidade'       => trim($dados['cidade'] ?? ''),
            'uf'           => strtoupper(trim($dados['uf'] ?? '')),
            'padrao'       => !empty($dados['padrao']) ? 1 : 0,
        ];

        // Se for o primeiro endereço do usuário, define automaticamente como padrão
        $totalExistente = $this->where('usuario_id', $usuarioId)->countAllResults();
        if ($totalExistente === 0) {
            $dadosLimpos['padrao'] = 1;
        }

        if ($dadosLimpos['padrao'] === 1) {
            $this->where('usuario_id', $usuarioId)->set(['padrao' => 0])->update();
        }

        if ($enderecoId && $enderecoId > 0) {
            $existente = $this->where('id', $enderecoId)->where('usuario_id', $usuarioId)->first();
            if (!$existente) {
                return ['ok' => false, 'erro' => 'Endereço não encontrado ou não pertence a este usuário.'];
            }
            $this->update($enderecoId, $dadosLimpos);
            $idFinal = $enderecoId;
        } else {
            $idFinal = $this->insert($dadosLimpos);
        }

        return ['ok' => true, 'id' => $idFinal];
    }
}
