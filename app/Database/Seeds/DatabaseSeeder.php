<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Desativar verificação de chaves estrangeiras para limpar dados limpos
        $this->db->disableForeignKeyChecks();

        $this->db->table('pedido_produtos')->truncate();
        $this->db->table('pedidos')->truncate();
        $this->db->table('produtos')->truncate();
        $this->db->table('categorias')->truncate();
        $this->db->table('usuarios')->truncate();

        $this->db->enableForeignKeyChecks();

        // 1. USUÁRIOS
        $senhaHash = password_hash('123456', PASSWORD_DEFAULT);
        $usuarios = [
            [
                'nome'       => 'Administrador da Loja',
                'email'      => 'admin@admin.com',
                'senha_hash' => $senhaHash,
                'role'       => 'admin',
                'ativo'      => 1,
                'criado_em'  => date('Y-m-d H:i:s'),
            ],
            [
                'nome'       => 'Cliente Exemplo',
                'email'      => 'cliente@cliente.com',
                'senha_hash' => $senhaHash,
                'role'       => 'cliente',
                'ativo'      => 1,
                'criado_em'  => date('Y-m-d H:i:s'),
            ],
            [
                'nome'       => 'Maria Silva',
                'email'      => 'maria@exemplo.com',
                'senha_hash' => $senhaHash,
                'role'       => 'cliente',
                'ativo'      => 1,
                'criado_em'  => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('usuarios')->insertBatch($usuarios);

        // 2. CATEGORIAS
        $categorias = [
            ['nome' => 'Eletrônicos', 'descricao' => 'Smartphones, notebooks, fones e dispositivos modernos.'],
            ['nome' => 'Roupas & Vestuário', 'descricao' => 'Camisas, calças e moda em geral.'],
            ['nome' => 'Calçados', 'descricao' => 'Tênis esportivos, sapatos sociais e sapatilhas.'],
            ['nome' => 'Casa & Decoração', 'descricao' => 'Móveis, luminárias e utensílios domésticos.'],
        ];
        $this->db->table('categorias')->insertBatch($categorias);

        // 3. PRODUTOS
        $produtos = [
            [
                'nome'         => 'Smartphone Galaxy Pro Max',
                'descricao'    => 'Smartphone de última geração com tela de 120Hz, 256GB de armazenamento e câmera tripla de 108MP.',
                'preco'        => 3499.90,
                'estoque'      => 15,
                'imagem'       => null,
                'categoria_id' => 1,
                'criado_em'    => date('Y-m-d H:i:s'),
                'atualizado_em'=> date('Y-m-d H:i:s'),
            ],
            [
                'nome'         => 'Notebook UltraSlim i7 16GB',
                'descricao'    => 'Notebook potente e leve com processador Intel Core i7, 16GB RAM e SSD NVMe de 512GB.',
                'preco'        => 4899.00,
                'estoque'      => 8,
                'imagem'       => null,
                'categoria_id' => 1,
                'criado_em'    => date('Y-m-d H:i:s'),
                'atualizado_em'=> date('Y-m-d H:i:s'),
            ],
            [
                'nome'         => 'Fone de Ouvido Bluetooth Noise Cancelling',
                'descricao'    => 'Fone de ouvido sem fio com cancelamento ativo de ruído e bateria com autonomia de até 30 horas.',
                'preco'        => 599.90,
                'estoque'      => 30,
                'imagem'       => null,
                'categoria_id' => 1,
                'criado_em'    => date('Y-m-d H:i:s'),
                'atualizado_em'=> date('Y-m-d H:i:s'),
            ],
            [
                'nome'         => 'Camisa Polo Casual Algodão Premium',
                'descricao'    => 'Camisa polo confeccionada em algodão pima 100%, toque macio e caimento perfeito.',
                'preco'        => 129.90,
                'estoque'      => 50,
                'imagem'       => null,
                'categoria_id' => 2,
                'criado_em'    => date('Y-m-d H:i:s'),
                'atualizado_em'=> date('Y-m-d H:i:s'),
            ],
            [
                'nome'         => 'Jaqueta Jeans Vintage Unisex',
                'descricao'    => 'Jaqueta jeans estilo clássico com acabamento premium e botões reforçados.',
                'preco'        => 249.00,
                'estoque'      => 20,
                'imagem'       => null,
                'categoria_id' => 2,
                'criado_em'    => date('Y-m-d H:i:s'),
                'atualizado_em'=> date('Y-m-d H:i:s'),
            ],
            [
                'nome'         => 'Tênis Esportivo Running Air Comfort',
                'descricao'    => 'Tênis de alta performance ideal para corridas e caminhadas, com amortecimento a ar.',
                'preco'        => 329.90,
                'estoque'      => 25,
                'imagem'       => null,
                'categoria_id' => 3,
                'criado_em'    => date('Y-m-d H:i:s'),
                'atualizado_em'=> date('Y-m-d H:i:s'),
            ],
            [
                'nome'         => 'Luminária de Mesa LED Articulada',
                'descricao'    => 'Luminária de mesa com regulagem de intensidade de luz, 3 temperaturas de cor e entrada USB.',
                'preco'        => 149.90,
                'estoque'      => 40,
                'imagem'       => null,
                'categoria_id' => 4,
                'criado_em'    => date('Y-m-d H:i:s'),
                'atualizado_em'=> date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('produtos')->insertBatch($produtos);

        // 4. PEDIDOS E ITENS DO PEDIDO
        $pedidos = [
            [
                'usuario_id'  => 2, // Cliente Exemplo
                'valor_total' => 4099.80,
                'status'      => 'entregue',
                'cep'         => '01310-100',
                'logradouro'  => 'Av. Paulista',
                'numero'      => '1000',
                'complemento' => 'Apto 42',
                'bairro'      => 'Bela Vista',
                'cidade'      => 'São Paulo',
                'uf'          => 'SP',
                'criado_em'   => date('Y-m-d H:i:s', strtotime('-5 days')),
            ],
            [
                'usuario_id'  => 2, // Cliente Exemplo
                'valor_total' => 599.90,
                'status'      => 'processando',
                'cep'         => '01310-100',
                'logradouro'  => 'Av. Paulista',
                'numero'      => '1000',
                'complemento' => 'Apto 42',
                'bairro'      => 'Bela Vista',
                'cidade'      => 'São Paulo',
                'uf'          => 'SP',
                'criado_em'   => date('Y-m-d H:i:s', strtotime('-1 day')),
            ],
            [
                'usuario_id'  => 3, // Maria Silva
                'valor_total' => 378.90,
                'status'      => 'pendente',
                'cep'         => '04543-000',
                'logradouro'  => 'Rua Funchal',
                'numero'      => '500',
                'complemento' => null,
                'bairro'      => 'Vila Olímpia',
                'cidade'      => 'São Paulo',
                'uf'          => 'SP',
                'criado_em'   => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('pedidos')->insertBatch($pedidos);

        // Itens do Pedido 1
        $itensPedido1 = [
            [
                'pedido_id'      => 1,
                'produto_id'     => 1, // Smartphone
                'quantidade'     => 1,
                'preco_unitario' => 3499.90,
            ],
            [
                'pedido_id'      => 1,
                'produto_id'     => 3, // Fone Bluetooth
                'quantidade'     => 1,
                'preco_unitario' => 599.90,
            ],
        ];
        $this->db->table('pedido_produtos')->insertBatch($itensPedido1);

        // Itens do Pedido 2
        $itensPedido2 = [
            [
                'pedido_id'      => 2,
                'produto_id'     => 3, // Fone Bluetooth
                'quantidade'     => 1,
                'preco_unitario' => 599.90,
            ],
        ];
        $this->db->table('pedido_produtos')->insertBatch($itensPedido2);

        // Itens do Pedido 3
        $itensPedido3 = [
            [
                'pedido_id'      => 3,
                'produto_id'     => 4, // Camisa Polo
                'quantidade'     => 1,
                'preco_unitario' => 129.90,
            ],
            [
                'pedido_id'      => 3,
                'produto_id'     => 5, // Jaqueta Jeans
                'quantidade'     => 1,
                'preco_unitario' => 249.00,
            ],
        ];
        $this->db->table('pedido_produtos')->insertBatch($itensPedido3);
    }
}
