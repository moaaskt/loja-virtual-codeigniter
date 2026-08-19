<?php

namespace App\Services;

class FreteService
{
    /**
     * Tabela de precificação e prazos regionais por faixa de CEP inicial (2 primeiros dígitos).
     */
    protected static array $tabelaRegioes = [
        'sudeste_sp' => [
            'prefixos' => ['01','02','03','04','05','06','07','08','09','11','12','13','14','15','16','17','18','19'],
            'regiao'   => 'São Paulo',
            'pac'      => ['valor' => 14.90, 'prazo_min' => 2, 'prazo_max' => 4],
            'sedex'    => ['valor' => 24.90, 'prazo_min' => 1, 'prazo_max' => 2],
        ],
        'sudeste_demais' => [
            'prefixos' => ['20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37','38','39'],
            'regiao'   => 'Sudeste (RJ/MG/ES)',
            'pac'      => ['valor' => 19.90, 'prazo_min' => 4, 'prazo_max' => 6],
            'sedex'    => ['valor' => 34.90, 'prazo_min' => 2, 'prazo_max' => 3],
        ],
        'sul' => [
            'prefixos' => ['80','81','82','83','84','85','86','87','88','89','90','91','92','93','94','95','96','97','98','99'],
            'regiao'   => 'Sul (PR/SC/RS)',
            'pac'      => ['valor' => 22.90, 'prazo_min' => 5, 'prazo_max' => 7],
            'sedex'    => ['valor' => 39.90, 'prazo_min' => 2, 'prazo_max' => 4],
        ],
        'centro_oeste' => [
            'prefixos' => ['70','71','72','73','74','75','76','78','79'],
            'regiao'   => 'Centro-Oeste (DF/GO/MT/MS)',
            'pac'      => ['valor' => 26.90, 'prazo_min' => 6, 'prazo_max' => 8],
            'sedex'    => ['valor' => 46.90, 'prazo_min' => 3, 'prazo_max' => 5],
        ],
        'nordeste' => [
            'prefixos' => ['40','41','42','43','44','45','46','47','48','49','50','51','52','53','54','55','56','57','58','59','60','61','62','63','64','65'],
            'regiao'   => 'Nordeste',
            'pac'      => ['valor' => 29.90, 'prazo_min' => 7, 'prazo_max' => 10],
            'sedex'    => ['valor' => 54.90, 'prazo_min' => 3, 'prazo_max' => 6],
        ],
        'norte' => [
            'prefixos' => ['66','67','68','69','77'],
            'regiao'   => 'Norte',
            'pac'      => ['valor' => 36.90, 'prazo_min' => 8, 'prazo_max' => 14],
            'sedex'    => ['valor' => 68.90, 'prazo_min' => 4, 'prazo_max' => 7],
        ],
    ];

    /**
     * Calcula opções de frete para um CEP fornecido.
     * Retorna array com ['ok' => bool, 'opcoes' => array, 'cep_formatado' => string, 'regiao' => string, 'erro' => string]
     */
    public function calcular(string $cep, float $subtotal = 0.0, bool $temFreteGratis = false): array
    {
        $cepLimpo = preg_replace('/\D/', '', $cep);

        if (strlen($cepLimpo) !== 8) {
            return [
                'ok'     => false,
                'erro'   => 'Por favor, informe um CEP válido com 8 dígitos.',
                'opcoes' => [],
            ];
        }

        $cepFormatado = substr($cepLimpo, 0, 5) . '-' . substr($cepLimpo, 5, 3);
        $prefixo      = substr($cepLimpo, 0, 2);

        // Identifica a região correspondente
        $dadosRegiao = null;
        foreach (self::$tabelaRegioes as $regiao) {
            if (in_array($prefixo, $regiao['prefixos'], true)) {
                $dadosRegiao = $regiao;
                break;
            }
        }

        // Fallback padrão se não bater prefixo específico
        if (!$dadosRegiao) {
            $dadosRegiao = self::$tabelaRegioes['sudeste_demais'];
        }

        $opcoes = [];

        // Verifica elegibilidade para Frete Grátis (produto com flag ou subtotal >= R$ 199)
        $elegivelFreteGratis = $temFreteGratis || ($subtotal >= 199.00);

        if ($elegivelFreteGratis) {
            $opcoes[] = [
                'codigo'    => 'gratis',
                'nome'      => 'Frete Grátis Promocional',
                'descricao' => 'Entrega padrão sem custo',
                'valor'     => 0.00,
                'prazo'     => $dadosRegiao['pac']['prazo_min'] . ' a ' . $dadosRegiao['pac']['prazo_max'] . ' dias úteis',
                'icone'     => 'bi-gift-fill',
                'destaque'  => true,
            ];
        }

        // Opção PAC / Econômico
        $opcoes[] = [
            'codigo'    => 'pac',
            'nome'      => 'Econômico (PAC)',
            'descricao' => 'Melhor custo-benefício',
            'valor'     => (float) $dadosRegiao['pac']['valor'],
            'prazo'     => $dadosRegiao['pac']['prazo_min'] . ' a ' . $dadosRegiao['pac']['prazo_max'] . ' dias úteis',
            'icone'     => 'bi-truck',
            'destaque'  => false,
        ];

        // Opção SEDEX / Expresso
        $opcoes[] = [
            'codigo'    => 'sedex',
            'nome'      => 'Expresso (SEDEX)',
            'descricao' => 'Entrega rápida com prioridade',
            'valor'     => (float) $dadosRegiao['sedex']['valor'],
            'prazo'     => $dadosRegiao['sedex']['prazo_min'] . ' a ' . $dadosRegiao['sedex']['prazo_max'] . ' dias úteis',
            'icone'     => 'bi-lightning-charge-fill',
            'destaque'  => false,
        ];

        return [
            'ok'            => true,
            'cep_formatado' => $cepFormatado,
            'regiao'        => $dadosRegiao['regiao'],
            'opcoes'        => $opcoes,
        ];
    }
}
