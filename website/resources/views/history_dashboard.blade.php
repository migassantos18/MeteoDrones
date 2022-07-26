@extends('layout')
@section('content')

<button type="button" class="btn btn-secondary" id="back_button" onclick="history.back()">Voltar</button>

<div class="pc">
    <div class="row">
        <div class="card">
            <h5 id="card-name">Temperatura</h5>
            <div class="info">
                <div class="chart-container">
                    <div id="temps_div"></div>
                </div>
                {!! $lava->render('GaugeChart', 'Temperature', 'temps_div') !!}
                <h4><b>{{ $data['temperature'] }}ºC</b></h4>
            </div>
        </div>

        <div class="card">
            <h5 id="card-name">Humidade</h5>
            <div class="info">
                <div id="poll_div"></div>
                {!! $lava->render('BarChart', 'Humidity', 'poll_div') !!}
                <h4><b>{{ $data['humidity'] }}%</b></h4>
            </div><br>

            <h5 id="card-name">Luminosidade</h5>
            <div class="info">
                <div id="luminosity_div"></div>
                @if($data['luminosity'] != null)
                    {!! $lava->render('BarChart', 'Luminosity', 'luminosity_div') !!}
                    <h4><b>{{ $data['luminosity'] }}%</b></h4> 
                @endif
            </div>
        </div>

        <div class="card">
            <h5 id="card-name">Indice de calor</h5>
            <div class="info">
                <div class="chart-container">
                    <div id="heat_index_div"></div>
                </div>
                {!! $lava->render('GaugeChart', 'Heat Index', 'heat_index_div') !!}
                <h4><b>{{ $data['heat_index'] }}ºC</b></h4> 
            </div>
        </div>

        <div class="card">
            <h5 id="card-name">Risco de Incêndio</h5>
            <div class="info">
                <div class="chart-container">
                    <div id="fire_risk_div"></div>
                </div>
                {!! $lava->render('GaugeChart', 'Risco de Incendio', 'fire_risk_div') !!}
                <h4><b>{{ $risk }}</b></h4> 
            </div>
        </div>
    </div>

    <div class="row">
        <div class="card">
            <h5 id="card-name">Latitude</h5>
            <div class="info">
                @if($data['latitude'] != null)
                    <h4><b>{{ $data['latitude'] }}º</b></h4>
                @endif
            </div>
        </div>

        <div class="card">
            <h5 id="card-name">Longitude</h5>
            <div class="info">
                @if($data['longitude'] != null)
                    <h4><b>{{ $data['longitude'] }}º</b></h4>
                @endif
            </div>
        </div>

        <div class="card">
            <h5 id="card-name">Altitude</h5>
            <div class="info">
                @if($data['altitude'] != null)
                    <h4><b>{{ $data['altitude'] }} m</b></h4>
                @endif 
            </div>
        </div>

        <div class="card">
            <h5 id="card-name">Data e Hora</h5>
            <div class="info">
                <h4><b>{{ $data['date'] }} <br> {{ $data['time'] }} UTC</b></h4> 
            </div>
        </div>
    </div>
</div>

<div class="mobile">
    <div class="row">
        <div class="card">
            <h5 id="card-name">Temperatura</h5>
            <div class="info">
                <div id="temps_div2"></div>
                {!! $lava->render('GaugeChart', 'Temperature', 'temps_div2') !!}
                <h5><b>{{ $data['temperature'] }}ºC</b></h5>
            </div>
        </div>

        <div class="card">
            <h5 id="card-name">Humidade</h5>
            <div class="info">
                <div id="poll_div2"></div>
                {!! $lava->render('BarChart', 'Humidity', 'poll_div2') !!}
                <h5><b>{{ $data['humidity'] }}%</b></h5>
            </div><br>

            <h5 id="card-name">Luminosidade</h5>
            <div class="info">
                <div id="luminosity_div2"></div>
                @if($data['luminosity'] != null)
                    {!! $lava->render('BarChart', 'Luminosity', 'luminosity_div2') !!}
                    <h5><b>{{ $data['luminosity'] }}%</b></h5> 
                @else
                    <h5><b>Erro</b></h5>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="card">
            <h5 id="card-name">Indice de calor</h5>
            <div class="info">
                <div id="heat_index_div2"></div>
                {!! $lava->render('GaugeChart', 'Heat Index', 'heat_index_div2') !!}
                <h5><b>{{ $data['heat_index'] }}ºC</b></h5> 
            </div>
        </div>

        <div class="card">
            <h5 id="card-name">Risco de Incêndio</h5>
            <div class="info">
                <div id="fire_risk_div2"></div>
                {!! $lava->render('GaugeChart', 'Risco de Incendio', 'fire_risk_div2') !!}
                <h5><b>{{ $risk }}</b></h5> 
            </div>
        </div>
    </div>

    <div class="row">
        <div class="card">
            <h5 id="card-name">Latitude</h5>
            <div class="info">
                @if($data['latitude'] != null)
                    <h5><b>{{ $data['latitude'] }}º</b></h5>
                @endif
            </div>
        </div>

        <div class="card">
            <h5 id="card-name">Longitude</h5>
            <div class="info">
                @if($data['longitude'] != null)
                    <h5><b>{{ $data['longitude'] }}º</b></h5>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="card">
            <h5 id="card-name">Altitude</h5>
            <div class="info">
                @if($data['altitude'] != null)
                    <h5><b>{{ $data['altitude'] }} m</b></h5>
                @endif 
            </div>
        </div>

        <div class="card">
            <h5 id="card-name">Data e Hora</h5>
            <div class="info">
                <h5><b>{{ $data['date'] }} <br> {{ $data['time'] }} UTC</b></h5> 
            </div>
        </div>
    </div>
</div>

<div id="map"></div>

<script id="id" data="{{ $data['id'] }}" src="{{ asset('js/history_map.js') }}"></script>
<script
src="https://maps.googleapis.com/maps/api/js?key=key&callback=initMap&v=weekly"
async></script>

@endsection