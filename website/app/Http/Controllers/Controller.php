<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Data;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Khill\Lavacharts\Lavacharts;
use App\Http\Requests\ConfigsStoreRequest;
use App\Models\Configs;
use Salman\Mqtt\MqttClass\Mqtt;
use App\Models\GPS;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        //get database data
        $data = Data::latest()->first();
	$gps = GPS::latest()->first();

        if ($data == null) {
            abort(404);
        }

        $lava = new Lavacharts;

        //temperature chart
        $temperature = $lava->DataTable();
        $temperature->addStringColumn('Type')
            ->addNumberColumn('Value')
            ->addRow(['Temp', $data->temperature]);

        $lava->GaugeChart('Temperature', $temperature, [
            'max'        => 40,
            'greenFrom'  => 0,
            'greenTo'    => 20,
            'yellowFrom' => 21,
            'yellowTo'   => 30,
            'redFrom'    => 31,
            'redTo'      => 40,
        ]);

        //humidity chart
        $humidity = $lava->DataTable();
        $humidity->addStringColumn('Humidity')
            ->addNumberColumn('%')
            ->addRow(['Humidade',  $data->humidity]);

        $lava->BarChart('Humidity', $humidity, [
            'min' => 0,
            'max' => 100,
            'height'   => 40,
            'legend' => 'none'
        ]);

        $lava->BarChart('Humidity', $humidity);

        //luminosity chart
        $luminosity = $lava->DataTable();
        $luminosity->addStringColumn('Luminosity')
            ->addNumberColumn('%')
            ->addRow(['Luminosidade',  $data->luminosity]);

        $lava->BarChart('Luminosity', $luminosity, [
            'min' => 0,
            'max' => 100,
            'height'   => 40,
            'legend' => 'none'
        ]);

        $lava->BarChart('Luminosity', $luminosity);

        //heat index chart
        $heatIndex = $lava->DataTable();
        $heatIndex->addStringColumn('Type')
            ->addNumberColumn('Value')
            ->addRow(['HI', $data->heat_index]);

        $lava->GaugeChart('Heat Index', $heatIndex, [
            'max'        => 50,
            'greenFrom'  => 0,
            'greenTo'    => 26,
            'yellowFrom' => 27,
            'yellowTo'   => 41,
            'redFrom'    => 42,
            'redTo'      => 50,
        ]);

        //fire risk chart
        if ($data->humidity != 0){
            $fireRiskValue = round(($data->temperature / $data->humidity), 2);
        }
        else {
            $fireRiskValue = 0.00;
        }   

        $fireRisk = $lava->DataTable();
        $fireRisk->addStringColumn('Type')
            ->addNumberColumn('Value')
            ->addRow(['Risco', $fireRiskValue]);

        $lava->GaugeChart('Risco de Incendio', $fireRisk, [
            'max'        => 2,
            'greenFrom'  => 0,
            'greenTo'    => 0.49,
            'yellowFrom' => 0.50,
            'yellowTo'   => 0.99,
            'redFrom'    => 1,
            'redTo'      => 2,
            'majorTicks' => [
                'Baixo',
                'Elevado'
            ]
        ]);

        $risk = "";
        if ($fireRiskValue >= 0.0 && $fireRiskValue <= 0.49){
            $risk = "Baixo";
        }
        if ($fireRiskValue >= 0.50 && $fireRiskValue <= 0.99){
            $risk = "Moderado";
        }
        if ($fireRiskValue >= 1.00 && $fireRiskValue <= 1.49){
            $risk = "Alto";
        }
        if ($fireRiskValue >= 1.50 && $fireRiskValue <= 1.99){
            $risk = "Muito alto";
        }
        if ($fireRiskValue >= 2.00){
            $risk = "Extremo";
        }

        return view('dashboard')
            ->with("lava", $lava)
            ->with("data", $data)
            ->with("risk", $risk)
	    ->with("gps", $gps);
    }


    public function history() {
        $data = Data::orderBy('id', 'desc')->paginate(25)->withQueryString();

        return view('history')->with("data", $data);
    }

    public function historyDashboard($id)
    {
        //get database data
        $data = Data::where('id', $id)->first();

        if ($data == null) {
            abort(404);
        }

        $lava = new Lavacharts;

        //temperature chart
        $temperature = $lava->DataTable();
        $temperature->addStringColumn('Type')
            ->addNumberColumn('Value')
            ->addRow(['Temp', $data->temperature]);

        $lava->GaugeChart('Temperature', $temperature, [
            'max'        => 40,
            'greenFrom'  => 0,
            'greenTo'    => 20,
            'yellowFrom' => 21,
            'yellowTo'   => 30,
            'redFrom'    => 31,
            'redTo'      => 40,
        ]);

        //humidity chart
        $humidity = $lava->DataTable();
        $humidity->addStringColumn('Humidity')
            ->addNumberColumn('%')
            ->addRow(['Humidade',  $data->humidity]);

        $lava->BarChart('Humidity', $humidity, [
            'min' => 0,
            'max' => 100,
            'height'   => 40,
            'legend' => 'none'
        ]);

        $lava->BarChart('Humidity', $humidity);

        //luminosity chart
        $luminosity = $lava->DataTable();
        $luminosity->addStringColumn('Luminosity')
            ->addNumberColumn('%')
            ->addRow(['Luminosidade',  $data->luminosity]);

        $lava->BarChart('Luminosity', $luminosity, [
            'min' => 0,
            'max' => 100,
            'height'   => 40,
            'legend' => 'none'
        ]);

        $lava->BarChart('Luminosity', $luminosity);

        //heat index chart
        $heatIndex = $lava->DataTable();
        $heatIndex->addStringColumn('Type')
            ->addNumberColumn('Value')
            ->addRow(['HI', $data->heat_index]);

        $lava->GaugeChart('Heat Index', $heatIndex, [
            'max'        => 50,
            'greenFrom'  => 0,
            'greenTo'    => 26,
            'yellowFrom' => 27,
            'yellowTo'   => 41,
            'redFrom'    => 42,
            'redTo'      => 50,
        ]);

        //fire risk chart
        if ($data->humidity != 0){
            $fireRiskValue = round(($data->temperature / $data->humidity), 2);
        }
        else {
            $fireRiskValue = 0.00;
        }

        $fireRisk = $lava->DataTable();
        $fireRisk->addStringColumn('Type')
            ->addNumberColumn('Value')
            ->addRow(['Risco', $fireRiskValue]);

        $lava->GaugeChart('Risco de Incendio', $fireRisk, [
            'max'        => 2,
            'greenFrom'  => 0,
            'greenTo'    => 0.49,
            'yellowFrom' => 0.50,
            'yellowTo'   => 0.99,
            'redFrom'    => 1,
            'redTo'      => 2,
            'majorTicks' => [
                'Baixo',
                'Elevado'
            ]
        ]);

        $risk = "";
        if ($fireRiskValue >= 0.0 && $fireRiskValue <= 0.49){
            $risk = "Baixo";
        }
        if ($fireRiskValue >= 0.50 && $fireRiskValue <= 0.99){
            $risk = "Moderado";
        }
        if ($fireRiskValue >= 1.00 && $fireRiskValue <= 1.49){
            $risk = "Alto";
        }
        if ($fireRiskValue >= 1.50 && $fireRiskValue <= 1.99){
            $risk = "Muito alto";
        }
        if ($fireRiskValue >= 2.00){
            $risk = "Extremo";
        }

        return view('history_dashboard')
            ->with("lava", $lava)
            ->with("data", $data)
            ->with("risk", $risk);
    }

    public function about() {
        return view('about');
    }

    public function configs() {
        $configs = Configs::latest()->first();

        return view('configs')->with("configs", $configs);
    }

    public function saveConfigs(ConfigsStoreRequest $request) {
        $validated = $request->validated();

        if ($request->type == "T") {
            $time = $validated['time'];
            $meters = null;
        }
        else {
            $meters = $validated['meters'];
            $time = null;
        }

        $configs = new Configs([
            'type' => $validated['type'],
            'time' => $time,
            'meters' => $meters,
        ]);
      
        $configs->save();

        $mqtt = new Mqtt();
        $output = $mqtt->ConnectAndPublish('configs', $configs->toJson());

        return $this->configs();
    }

    public function getConfigs()
    {
        $configs = Configs::latest()->first();
        return response($configs);
    }
}
