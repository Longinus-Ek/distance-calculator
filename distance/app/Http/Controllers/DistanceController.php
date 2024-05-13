<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Distance;
use App\Class\DistanceCalculator;

class DistanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $distances = Distance::all();
        foreach($distances as &$distance){
            $distance->dataEmiss = date_format($distance->created_at, 'd-m-Y H:i:s');
            $distance->dataAlter = date_format($distance->updated_at, 'd-m-Y H:i:s');
            $distance->cepOrigem = preg_replace("/^(\d{5})(\d{3})$/", "$1-$2", $distance->cepIn);
            $distance->cepFim = preg_replace("/^(\d{5})(\d{3})$/", "$1-$2", $distance->cepFn);
            $distance->action = '<button class="btn btn-outline-info" style="margin-top: 10px" onclick="editDados(\''.$distance->id.'\')" type="button">Editar</button>';
        }
        return response()->json($distances);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->manipulateDados($request, 'store');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json(Distance::find($id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return $this->manipulateDados($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    private function manipulateDados($request, $id)
    {
        try{
            $dados = $request->all();
            foreach($dados as &$dado){
                $dado = preg_replace("/[^0-9]/", "", $dado);
            }
            if(strlen($dados['cep1']) !== 8){
                return response()->json(['error' => true, 'msg' => 'Cep Origem Inválido!']);
            }
            if(strlen($dados['cep2']) !== 8){
                return response()->json(['error' => true, 'msg' => 'Cep Fim Inválido!']);
            }
            $distances = Distance::all();
            foreach($distances as $distanceModel){
                if($distanceModel->cepIn == $dados['cep2'] && $distanceModel->cepFn == $dados['cep1']){
                    return response()->json(['error' => true, 'msg' => 'Pesquisa já realizada!']);
                    break;
                }
                if($distanceModel->cepIn == $dados['cep1'] && $distanceModel->cepFn == $dados['cep2']){
                    return response()->json(['error' => true, 'msg' => 'Pesquisa já realizada!']);
                    break;
                }
            }

            if($id !== 'store'){
                $distanceModel = Distance::find($id);
            }else{
                $distanceModel = new Distance();
            }
            $distance = new DistanceCalculator('2eae6f2e74591d2a5ca6f1143aafac08');
            $consultaCep1 = $distance->getCep($cep1);
            $consultaCep2 = $distance->getCep($cep2);
            if(!is_array($consultaCep1)){
                return response()->json(['error' => true, 'msg' => 'Cep Origem Inválido!']);
            }
            if(!is_array($consultaCep2)){
                return response()->json(['error' => true, 'msg' => 'Cep Fim Inválido!']);
            }
            $distance = new DistanceCalculator('2eae6f2e74591d2a5ca6f1143aafac08');
            $latitude1 = $consultaCep1['latitude'];
            $latitude2 = $consultaCep2['latitude'];
            $longitude1 = $consultaCep1['longitude'];;
            $longitude2 = $consultaCep2['longitude'];;
            $model->latitude1 = $latitude1;
            $model->longitude1 = $longitude1;
            $model->latitude2 = $latitude2;
            $model->longitude2 = $longitude2;
            $model->distance = $distance->getDistance($latitude1, $latitude2, $longitude1, $longitude2);
            $model->cepIn = $dados['cep1'];
            $model->cepFn = $dados['cep2'];
            $model->save();
            return response()->json(['success' => true]);
        }catch(Exception $e){
            return response()->json(['error' => true, 'msg' => 'Ocorreu um erro ao calcular a distância!']);
        }
    }

    public function importCSV(Request $request)
    {
        if ($request->hasFile('csv_file')) {
            $arquivo = $request->file('csv_file');
            $caminho = $arquivo->store('csv');
            $caminhoCompleto = storage_path('app/' . $caminho);
            $file = fopen($caminhoCompleto, 'r');

            if ($file !== false) {

                $dados = array();

                while (($row = fgetcsv($file)) !== false) {
                    $dadosLimpos = array();

                    foreach ($row as $value) {
                        $cleanValue = preg_replace("/[^0-9]/", "", $value);
                        $dadosLimpos[] = $cleanValue;
                    }
                    $dados[] = $dadosLimpos;
                }
            }
            unset($dados[0]);
            $distance = new DistanceCalculator('2eae6f2e74591d2a5ca6f1143aafac08');
            foreach($dados as $key => $dado){
                $distances = Distance::all();
                $continuar = false;
                foreach($distances as $distanceModel){
                    if($distanceModel->cepIn == $dado[0] && $distanceModel->cepFn == $dado[1]){
                        $continuar = true;
                    }
                    if($distanceModel->cepIn == $dado[1] && $distanceModel->cepFn == $dado[0]){
                        $continuar = true;
                    }
                }
                if($continuar){
                    continue;
                }
                $model = new Distance();
                $consultaCep1 = $distance->getCep($dado[0]);
                $consultaCep2 = $distance->getCep($dado[1]);
                if(!is_array($consultaCep1)){
                    continue;
                }
                if(!is_array($consultaCep2)){
                    continue;
                }
                
                $latitude1 = $consultaCep1['latitude'];
                $latitude2 = $consultaCep2['latitude'];
                $longitude1 = $consultaCep1['longitude'];;
                $longitude2 = $consultaCep2['longitude'];;
                $model->latitude1 = $latitude1;
                $model->longitude1 = $longitude1;
                $model->latitude2 = $latitude2;
                $model->longitude2 = $longitude2;
                $model->distance = $distance->getDistance($latitude1, $latitude2, $longitude1, $longitude2);
                $model->cepIn = $dado[0];
                $model->cepFn = $dado[1];
                $model->save();
            }
            return response()->json(['success' => true]);
        }else{
            return response()->json(['error' => true, 'msg' => 'Nenhum arquivo encontrado!']);
        }
    }
}
