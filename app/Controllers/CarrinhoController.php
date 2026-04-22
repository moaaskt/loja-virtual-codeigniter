<?php

namespace App\Controllers;

use App\Models\ProdutoModel;

class CarrinhoController extends BaseController
{

    public function __construct()
    {
        // Carrega os helpers para todos os métodos deste controller
        helper(['form', 'url']);
    }

    public function index()
    {
        $data = [
            'title' => 'Meu Carrinho de Compras',
            'carrinho' => session()->get('carrinho') ?? [] // Pega o carrinho da sessão
        ];

        return view('shop/carrinho', $data);
    }

    

// --- Função para atualizar a quantidade de um produto no carrinho ---
// Esta função recebe os dados do formulário e atualiza a quantidade do produto no carrinho
public function atualizar()
{
    // Pega os dados do formulário
    $produto_id = $this->request->getPost('produto_id');
    $quantidade = (int) $this->request->getPost('quantidade');

    // Validação simples: a quantidade não pode ser menor que 1
    if ($quantidade < 1) {
        return redirect()->to(site_url('carrinho'))->with('error', 'Quantidade inválida.');
    }

    // Pega o carrinho da sessão
    $carrinho = session()->get('carrinho') ?? [];

    // Verifica se o produto existe no carrinho antes de atualizar
    if (isset($carrinho[$produto_id])) {
        $carrinho[$produto_id]['quantidade'] = $quantidade;
    }

    // Salva o carrinho de volta na sessão
    session()->set('carrinho', $carrinho);

    return redirect()->to(site_url('carrinho'))->with('success', 'Quantidade atualizada!');
}



    // --- Função para remover um produto do carrinho ---
// Esta função recebe o ID do produto a ser removido e atualiza o carrinho na sessão
    public function remover($produto_id = null)
    {
        // Pega o carrinho da sessão
        $carrinho = session()->get('carrinho') ?? [];

        // Verifica se o produto a ser removido realmente existe no carrinho
        if (isset($carrinho[$produto_id])) {
            // Remove o item do array do carrinho usando a função unset()
            unset($carrinho[$produto_id]);
        }

        // Salva o carrinho atualizado de volta na sessão
        session()->set('carrinho', $carrinho);

        // Redireciona de volta para a página do carrinho com uma mensagem
        return redirect()->to(site_url('carrinho'))->with('success', 'Produto removido do carrinho!');
    }


    // --- Rota para adicionar um produto ao carrinho ---
    // Valida estoque antes de inserir ou incrementar o item na sessão.
    public function adicionar()
    {
        $produtoModel = new ProdutoModel();

        $produto_id = $this->request->getPost('produto_id');
        $quantidade = (int) $this->request->getPost('quantidade');

        $produto = $produtoModel->find($produto_id);

        if ($produto === null) {
            return redirect()->back()->with('error', 'Produto não encontrado!');
        }

        $estoqueDisponivel = (int) $produto['estoque'];

        if ($estoqueDisponivel <= 0) {
            return redirect()->back()->with('error', 'Produto fora de estoque.');
        }

        if ($quantidade < 1) {
            return redirect()->back()->with('error', 'A quantidade mínima é 1.');
        }

        $carrinho         = session()->get('carrinho') ?? [];
        $quantidadeNoCarrinho = isset($carrinho[$produto_id])
            ? (int) $carrinho[$produto_id]['quantidade']
            : 0;

        $quantidadeTotal  = $quantidadeNoCarrinho + $quantidade;

        if ($quantidadeTotal > $estoqueDisponivel) {
            $disponivelParaAdicionar = $estoqueDisponivel - $quantidadeNoCarrinho;
            return redirect()->back()->with(
                'error',
                "Estoque insuficiente. Você pode adicionar no máximo {$disponivelParaAdicionar} unidade(s) deste produto."
            );
        }

        if (isset($carrinho[$produto_id])) {
            $carrinho[$produto_id]['quantidade'] = $quantidadeTotal;
        } else {
            $carrinho[$produto_id] = [
                'id'         => $produto['id'],
                'nome'       => $produto['nome'],
                'preco'      => $produto['preco'],
                'imagem'     => $produto['imagem'],
                'quantidade' => $quantidade,
            ];
        }

        session()->set('carrinho', $carrinho);

        return redirect()->back()->with('success', 'Produto adicionado ao carrinho!');
    }
}