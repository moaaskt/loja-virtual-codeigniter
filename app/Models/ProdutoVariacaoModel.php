<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdutoVariacaoModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'produto_variacoes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'produto_id',
        'sku',
        'nome_variacao',
        'atributos_json',
        'tamanho',
        'cor',
        'cor_hex',
        'preco',
        'imagem_url',
        'codigo_barras',
        'estoque',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Retorna variações formatadas de um produto com atributos decodificados
     */
    public function getVariacoesFormatadas(int $produtoId, ?string $imagemFallback = null): array
    {
        $variacoes = $this->where('produto_id', $produtoId)->findAll();
        $formatadas = [];

        foreach ($variacoes as $var) {
            $atributos = [];
            if (!empty($var['atributos_json'])) {
                $decoded = json_decode($var['atributos_json'], true);
                if (is_array($decoded)) {
                    $atributos = $decoded;
                }
            }

            // Fallback de atributos caso atributos_json seja vazio (dados legados)
            if (empty($atributos)) {
                if (!empty($var['cor'])) {
                    $atributos['Cor'] = $var['cor'];
                }
                if (!empty($var['tamanho'])) {
                    $atributos['Tamanho / Opção'] = $var['tamanho'];
                }
            }

            // Fallback de nome_variacao caso vazio
            $nomeVariacao = $var['nome_variacao'] ?? '';
            if (empty($nomeVariacao) && !empty($atributos)) {
                $nomeVariacao = implode(' / ', array_values($atributos));
            }

            // Fallback de imagem
            $imagemUrl = !empty($var['imagem_url']) ? $var['imagem_url'] : $imagemFallback;

            $formatadas[] = array_merge($var, [
                'id'            => (int)$var['id'],
                'produto_id'    => (int)$var['produto_id'],
                'sku'           => $var['sku'] ?? '',
                'nome_variacao' => $nomeVariacao,
                'atributos'     => $atributos,
                'imagem_url'    => $imagemUrl,
                'preco'         => !empty($var['preco']) ? (float)$var['preco'] : null,
                'estoque'       => (int)$var['estoque'],
            ]);
        }

        return $formatadas;
    }

    /**
     * Extrai os atributos únicos e seus respectivos valores para um produto
     * Retorna ex: ['Cor' => ['Azul', 'Vermelho'], 'Armazenamento' => ['128GB', '256GB']]
     */
    public function getAtributosDisponiveis(int $produtoId): array
    {
        $variacoes = $this->getVariacoesFormatadas($produtoId);
        $atributosMap = [];

        foreach ($variacoes as $var) {
            if (!empty($var['atributos']) && is_array($var['atributos'])) {
                foreach ($var['atributos'] as $nomeAtributo => $valorAtributo) {
                    $nome = trim($nomeAtributo);
                    $valor = trim($valorAtributo);
                    if ($nome !== '' && $valor !== '') {
                        if (!isset($atributosMap[$nome])) {
                            $atributosMap[$nome] = [];
                        }
                        if (!in_array($valor, $atributosMap[$nome])) {
                            $atributosMap[$nome][] = $valor;
                        }
                    }
                }
            }
        }

        return $atributosMap;
    }

    /**
     * Busca uma variação pelo código SKU
     */
    public function buscarPorSku(string $sku): ?array
    {
        return $this->where('sku', trim($sku))->first();
    }
}
