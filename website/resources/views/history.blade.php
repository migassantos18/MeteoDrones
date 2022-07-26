@extends('layout')
@section('content')

<div class="card-body">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            <th scope="col">Data e Hora</th>
            <th scope="col">Temperatura</th>
            <th scope="col">Humidade</th>
            <th scope="col" class="pc">Luminosidade</th>
        </tr>
        </thead>
        <tbody>        
            @foreach($data as $d)
                <tr class="table_row" onclick="show_hide_row({{$loop->iteration}});">
                    <td>{{ $d['date']}} {{ $d['time']}}</td>
                    <td>{{ $d['temperature']}}ºC</td>
                    <td>{{ $d['humidity']}}%</td>
                    @if($d['luminosity'] != null)
                        <td class="pc">{{ $d['luminosity']}}%</td> 
                    @else
                        <td class="pc"></td>
                    @endif
                </tr>
                <tr id="{{$loop->iteration}}" class="hidden_row">
                    <td class="hidden_td">
                        <b>Latitude:</b> 
                        @if($d['latitude'] != null)
                            {{ $d['latitude']}}º
                        @endif
                        <br>
                        <b>Longitude:</b> 
                        @if($d['longitude'] != null)
                            {{ $d['longitude']}}º
                        @endif
                        <br>
                        <b>Altitude:</b> 
                        @if($d['altitude'] != null)
                            {{ $d['altitude']}} m
                        @endif
                        <br>
                    </td>
                    <td class="hidden_td"><b>Indice de calor:</b><br>{{ $d['heat_index']}}ºC<br>
                        <div class="mobile">
                            <br>
                            <b>Luminosidade:</b>
                            @if($d['luminosity'] != null)
                                {{ $d['luminosity']}}%
                            @else
                                &nbsp;
                            @endif
                        </div>
                    </td>
                    @guest
                        <td colspan="2" class="hidden_td"><a href="/historico/{{ $d['id']}}"><button type="button" class="btn btn-primary">Ver mais</button></a></td>
                    @else
                        <td class="hidden_td pc"><a href="/historico/{{ $d['id']}}"><button type="button" class="btn btn-primary">Ver mais</button></a></td>
                        <td class="hidden_td pc"><button type="button" class="btn btn-danger" onclick="delete_entry({{ $d['id']}})">Apagar</button></td>

                        <td class="hidden_td mobile">
                            <a href="/historico/{{ $d['id']}}"><button type="button" class="btn btn-primary">Ver mais</button></a><br><br>
                            <button type="button" class="btn btn-danger" onclick="delete_entry({{ $d['id']}})">Apagar</button>
                        </td>         
                    @endguest
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $data->withQueryString()->links() }}
</div>

<script src="{{ asset('js/app.js') }}"></script>
<script>
    Echo.channel('data')
        .listen('NewEvent', (e) => {
            location.reload();
    })

    function show_hide_row(row) {
        $("#"+row).toggle();
    }

    function delete_entry($id) {
        var result = confirm("Tem a certeza que pretende eliminar o registo " + $id + "?");

        if (result == false) {
            return;
        }

        axios.delete('/api/data/' + $id)
            .then(function (response) {
                window.location.href = '/historico';
            })
            .catch(error => {
                alert("Ocorreu um erro ao apagar o registo\n" + error);
        })
    }
</script> 

@endsection