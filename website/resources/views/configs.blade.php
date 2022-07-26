@extends('layout')
@section('content')

<div class="configs">
    <h2>Configurações do drone</h2><br>

    <form method="POST" action="{{route('save-configs')}}">
        @csrf
        <div class="form-group">

            <fieldset>
                <legend>Método de envio de dados:</legend>
                <div>
                <input type="radio" id="T" name="type" value="T" {{$configs->type == 'T' ? 'checked' : ''}}>
                <label for="T">Tempo</label>
                </div>
                <div>
                <input type="radio" id="D" name="type" value="D" {{$configs->type == 'D' ? 'checked' : ''}}>
                <label for="D">Distância</label>
                </div>
            </fieldset><br>

            <div id="timeInput">
                <label for="time">Tempo (segundos)</label>
                <input type="number" step="any" class="form-control" id="time" name="time" value="{{old('time', $configs->time)}}" placeholder="Tempo">
                @error('time')
                    <div class="error">{{$message}}</div>
                @enderror
            </div>

            <div id="distanceInput">
                <label for="distance">Distância (metros)</label>
                <input type="number" step="any" class="form-control" id="distance" name="meters" value="{{old('meters', $configs->meters)}}" placeholder="Distância">
                @error('meters')
                    <div class="error">{{$message}}</div>
                @enderror
            </div>
            <br>
            <!-- <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="locData">
                <label class="form-check-label" for="locData">
                    Não enviar dados enquanto a localização não estiver atualizada
                </label>
            </div> -->
        </div>
        <button type="submit" class="btn btn-primary" id="submitButton">Guardar</button>
    </form>
</div>

<script>
    const timeInput = document.getElementById('timeInput');
    const distanceInput = document.getElementById('distanceInput');

    if ($('input[id=T]:checked').length > 0) {
        timeInput.style.display = 'block';
    }
    else {
        distanceInput.style.display = 'block';
    }

    function handleRadioClick() {
        if (document.getElementById('T').checked) {
            distanceInput.style.display = 'none';
            timeInput.style.display = 'block';
        } else {
            timeInput.style.display = 'none';
            distanceInput.style.display = 'block';
        }
    }

    const radioButtons = document.querySelectorAll('input[name="type"]');
    radioButtons.forEach(radio => {
        radio.addEventListener('click', handleRadioClick);
    });    
</script>

@endsection