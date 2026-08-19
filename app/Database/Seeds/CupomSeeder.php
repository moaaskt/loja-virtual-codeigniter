<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CupomSeeder extends Seeder
{
    public function run()
    {
        $cupons = [
            [
                'codigo'              => 'PRIMEIRACOMPRA',
                'tipo'                => 'porcentagem',
                'valor'               => 10.00,
                'valor_minimo_pedido' => 0.00,
                'limite_uso'          => 100,
                'vezes_usado'         => 0,
                'data_validade'       => date('Y-m-d', strtotime('+60 days')),
                'ativo'               => 1,
                'criado_em'           => date('Y-m-d H:i:s'),
                'atualizado_em'       => date('Y-m-d H:i:s'),
            ],
            [
                'codigo'              => 'OFF20',
                'tipo'                => 'fixo',
                'valor'               => 20.00,
                'valor_minimo_pedido' => 100.00,
                'limite_uso'          => 50,
                'vezes_usado'         => 0,
                'data_validade'       => date('Y-m-d', strtotime('+30 days')),
                'ativo'               => 1,
                'criado_em'           => date('Y-m-d H:i:s'),
                'atualizado_em'       => date('Y-m-d H:i:s'),
            ],
            [
                'codigo'              => 'FRETEVIP',
                'tipo'                => 'fixo',
                'valor'               => 15.00,
                'valor_minimo_pedido' => 50.00,
                'limite_uso'          => null,
                'vezes_usado'         => 0,
                'data_validade'       => date('Y-m-d', strtotime('+90 days')),
                'ativo'               => 1,
                'criado_em'           => date('Y-m-d H:i:s'),
                'atualizado_em'       => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($cupons as $cupom) {
            $existe = $this->db->table('cupons')->where('codigo', $cupom['codigo'])->countAllResults();
            if ($existe === 0) {
                $this->db->table('cupons')->insert($cupom);
            }
        }
    }
}
