<?php

namespace App\Http\Controllers;

use App\Models\Maquina;
use App\Models\Pedido;
use App\Models\Materiais;
use Illuminate\Http\Request;

class PedidosController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::select('id', 'peca', 'solicitante', 'created_at', 'status', 'valor_total_final', 'valor_final_aprovado')
            ->orderBy('id', 'desc')
            ->paginate(10);

        $pedidos->getCollection()->transform(function ($pedido) {
            $pedido->created_at_formatado = $pedido->created_at->format('d/m/Y');
            return $pedido;
        });

        return response()->json($pedidos);
    }

    public function last()
    {
        $pedidos = Pedido::select('id', 'peca', 'solicitante', 'created_at', 'status', 'valor_total_final', 'valor_final_aprovado')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get()
            ->map(function ($pedido) {
                $pedido->created_at_formatado = $pedido->created_at->format('d/m/Y');
                return $pedido;
            });

        return response()->json($pedidos);
    }
    
    public function show(string $id)
    {
        $pedido = Pedido::findOrFail($id);
        $pedido->created_at_formatado = $pedido->created_at->format('d/m/Y');

        return response()->json($pedido);
    }

    public function buscar(Request $request)
    {
        $termo = strtolower($request->query('q'));

        if (strlen($termo) > 100) {
            return response()->json([
                'message' => 'O termo de busca é muito longo (máx. 50 caracteres).'
            ], 400);
        }

        $pedidos = Pedido::select('id', 'peca', 'descricao', 'solicitante')
            ->whereRaw('LOWER(peca) LIKE ?', ["%{$termo}%"])
            ->orWhereRaw('LOWER(solicitante) LIKE ?', ["%{$termo}%"])
            ->orWhereRaw('CAST(id AS CHAR) LIKE ?', ["%{$termo}%"])
            ->orWhereRaw('LOWER(descricao) LIKE ?', ["%{$termo}%"])
            ->orderBy('id', 'asc')
            ->limit(10)
            ->get();

        return response()->json($pedidos);
    }

    public function count()
    {
        $total = Pedido::count();
        return response()->json([$total]);
    }

    public function pendentes()
    {
        $totalPendentes = Pedido::where('status', 'Pendente')->count();

        $pendentes = Pedido::where('status', 'Pendente')->get();

        $pendentes->transform(function ($pedido) {
            $pedido->created_at_formatado = $pedido->created_at->format('d/m/Y');
            return $pedido;
        });

        return response()->json([
            'total_pendentes' => $totalPendentes,
            'pedidos' => $pendentes,
        ]);
    }

    public function emProducao()
    {
        $totalEmProducao = Pedido::where('status', 'Em Produção')->count();

        $emProducao = Pedido::where('status', 'Em Produção')->get();

        $emProducao->transform(function ($pedido) {
            $pedido->created_at_formatado = $pedido->created_at->format('d/m/Y');
            return $pedido;
        });

        return response()->json([
            'total_producao' => $totalEmProducao,
            'pedidos' => $emProducao,
        ]);
    }

    public function concluidos()
    {
        $totalConcluidos = Pedido::where('status', 'Concluído')->count();

        $concluidos = Pedido::where('status', 'Concluído')
            ->orderBy('id', 'desc')
            ->get();

        $concluidos->transform(function ($pedido) {
            $pedido->created_at_formatado = $pedido->created_at->format('d/m/Y');
            return $pedido;
        });

        return response()->json([
            'total_concluidos' => $totalConcluidos,
            'pedidos' => $concluidos,
        ]);
    }

    public function faturamento()
    {
        $faturamento = Pedido::whereIn('status', ['Concluído', 'Em Produção'])->sum('valor_final_aprovado');

        return response()->json([$faturamento]);
    }

    private function montarDadosPedido(array $validated): array
    {
        $tipo = $validated['tipo_filamento'];
        $material = Materiais::where('nome', $tipo)->firstOrFail();

        $preco_filamento_kg = $material->preco_por_kg;
        $filamento_usado_em_kg = ($validated['filamento_gramas_por_peca'] * $validated['quantidade']) / 1000;
        $valor_total_filamento = round($filamento_usado_em_kg * $preco_filamento_kg, 2);

        $quantidade_vezes_maquina_utilizada = $validated['quantidade'] / $validated['quantidade_produzida_ao_mesmo_tempo'];
        $maquina = Maquina::where('tipo', $validated['tipo_maquina'])->firstOrFail();
        $preco_hora_maquina = round($maquina->valor_maquina / $maquina->vida_util_horas, 2);
        $valor_total_maquina = round(($validated['tempo_fabricacao_horas'] * $quantidade_vezes_maquina_utilizada) * $preco_hora_maquina, 2);

        $valor_total_acabamento = round($validated['quantidade'] * $validated['valor_operacao_por_peca'], 2);
        $valor_mao_de_obra = round(($validated['mao_de_obra'] * $validated['tempo_modelagem_minutos']) / 60, 2);
        $valor_total_fabricacao = round($valor_mao_de_obra + $valor_total_filamento + $valor_total_maquina + (
            $validated['valor_operacao_por_peca'] * $quantidade_vezes_maquina_utilizada
        ), 2);
        
        $valor_itens_extras = $validated['valor_itens_extras'] ?? 0;
        $valor_bruto_final = round($valor_total_fabricacao + $valor_itens_extras, 2);
        $valor_total_final = round($valor_bruto_final + ($valor_bruto_final * ($validated['margem_lucro_percentual'] / 100)), 2);

        

        return [
            ...$validated,
            'preco_filamento_kg' => $preco_filamento_kg,
            'valor_total_filamento' => $valor_total_filamento,
            'valor_total_maquina' => $valor_total_maquina,
            'valor_total_acabamento' => $valor_total_acabamento,
            'valor_mao_de_obra' => $valor_mao_de_obra,
            'valor_total_fabricacao' => $valor_total_fabricacao,
            'valor_bruto_final' => $valor_bruto_final,
            'valor_total_final' => $valor_total_final,
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Primeiro passo
            'solicitante' => 'required|string|max:250',
            'peca' => 'required|string|max:250',
            'descricao' => 'required|string|max:600',
            'quantidade' => 'required|integer|min:1',
            'quantidade_produzida_ao_mesmo_tempo' => 'required|integer|min:1',

            // Segundo passo
            'tempo_fabricacao_horas' => 'required|numeric|min:0.1',
            'tempo_modelagem_minutos' => 'required|numeric|min:0',
            'filamento_gramas_por_peca' => 'required|numeric|min:0.1',

            // Terceiro passo
            'precisa_filamento' => 'required|boolean',
            'tipo_filamento' => 'required|string',

            // Quarto passo
            'cores' => 'required|array|min:1',
            'cores.*' => 'string|max:100',

            // Quinto passo
            'tipo_maquina' => 'required|string',
            'valor_operacao_por_peca' => 'required|numeric|min:0',
            'tem_itens_extras' => 'required|boolean',
            'valor_itens_extras' => 'nullable|numeric|min:0',
            'mao_de_obra' => 'required|numeric|min:0',
            'margem_lucro_percentual' => 'required|numeric|min:0',
        ]);

        $data = $this->montarDadosPedido($validated);
        $data['valor_final_aprovado'] = $data['valor_total_final'];

        Pedido::create($data);

        return response()->json(['message' => 'Pedido adicionado com sucesso!']);
    }

    public function atualizarStatus(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);
        $pedido->status = $request->input('status');
        $pedido->save();

        return response()->json(['mensagem' => 'Status atualizado com sucesso']);
    }

    public function update(Request $request, string $id)
    {
        $pedido = Pedido::findOrFail($id);

        $validated = $request->validate([
            //Primeiro passo
            'solicitante' => 'required|string|max:250',
            'peca' => 'required|string|max:250',
            'descricao' => 'required|string|max:600',
            'quantidade' => 'required|integer|min:1',
            'quantidade_produzida_ao_mesmo_tempo' => 'required|integer|min:1',

            //Segundo passo
            'tempo_fabricacao_horas' => 'required|numeric|min:0.1',
            'tempo_modelagem_minutos' => 'required|numeric|min:0',
            'filamento_gramas_por_peca' => 'required|numeric|min:0.1',

            //Terceiro passo
            'precisa_filamento' => 'required|boolean',
            'tipo_filamento' => 'required|string',

            //Quarto passo
            'cores' => 'required|array|min:1',
            'cores.*' => 'string|max:100',

            //Quinto passo
            'tipo_maquina' => 'required|string',
            'valor_operacao_por_peca' => 'required|numeric|min:0',
            'tem_itens_extras' => 'required|boolean',
            'valor_itens_extras' => 'nullable|numeric|min:0',
            'mao_de_obra' => 'required|numeric|min:0',
            'margem_lucro_percentual' => 'required|numeric|min:0',
        ]);

        $data = $this->montarDadosPedido($validated);
        $data['valor_final_aprovado'] = $data['valor_total_final'];
        $pedido->update($data);

        return response()->json(['message' => 'Pedido atualizado com sucesso!']);
    }

    public function changeFinalValue(Request $request, string $id)
    {
        $pedido = Pedido::findOrFail($id);

        $validated = $request->validate([
            'valor_final_aprovado' => 'required|numeric|min:0',
        ]);

        $pedido->valor_final_aprovado = $validated['valor_final_aprovado'];
        $pedido->save();

        return response()->json(['message' => 'Valor final atualizado com sucesso!']);
    }

    public function destroy(string $id)
    {
        $pedido = Pedido::findOrFail($id);
        $pedido->delete();

        return response()->json(['message' => 'Pedido deletado com sucesso!']);
    }
}
