<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\ProdutoModel;
use App\Models\PedidoModel;

class AdminController extends BaseController
{
    public function index()
    {
        $produtoModel = new ProdutoModel();
        $pedidoModel = new PedidoModel();
        $db = \Config\Database::connect(); // Conexão para queries customizadas

        // --- Dados para os Cards (KPIs) ---
        $data['total_produtos'] = $produtoModel->countAllResults();
        $data['total_pedidos'] = $pedidoModel->countAllResults();
        $data['baixo_estoque'] = $produtoModel->where('estoque <=', 5)->where('estoque >', 0)->countAllResults();
        $data['sem_estoque'] = $produtoModel->where('estoque', 0)->countAllResults();

        // --- Dados para o Gráfico de Status de Pedidos (Pizza) ---
        $statusPedidosQuery = $db->table('pedidos')
                                 ->select('status, COUNT(id) as total')
                                 ->groupBy('status')
                                 ->get();
        $data['status_pedidos_labels'] = [];
        $data['status_pedidos_data'] = [];
        foreach ($statusPedidosQuery->getResultArray() as $row) {
            $data['status_pedidos_labels'][] = ucfirst($row['status']);
            $data['status_pedidos_data'][] = $row['total'];
        }

        // --- Dados para o Gráfico de Estoque por Categoria (Barras) ---
        $estoqueCategoriaQuery = $db->table('produtos')
                                    ->select('categorias.nome, SUM(produtos.estoque) as total_estoque')
                                    ->join('categorias', 'categorias.id = produtos.categoria_id')
                                    ->groupBy('categorias.nome')
                                    ->orderBy('total_estoque', 'DESC')
                                    ->get();
        $data['estoque_categoria_labels'] = [];
        $data['estoque_categoria_data'] = [];
        foreach ($estoqueCategoriaQuery->getResultArray() as $row) {
            $data['estoque_categoria_labels'][] = $row['nome'];
            $data['estoque_categoria_data'][] = $row['total_estoque'];
        }

        // --- Dados para as Tabelas ---
        $data['ultimos_pedidos'] = $pedidoModel->getAllPedidosComCliente(5); // Pega os 5 últimos
        $data['produtos_criticos'] = $produtoModel->where('estoque <=', 5)->orderBy('estoque', 'ASC')->findAll(5);

        $data['title'] = 'Dashboard';

        return view('admin/dashboard', $data);
    }
}