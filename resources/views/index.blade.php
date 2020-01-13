<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Quero Educação</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <div class="header clearfix">
        <h3 class="text-muted">Quero Educação - Avaliação Técnica</h3>
    </div>
    <hr />
    <div class="alert alert-secondary">
        <p>O arquivo de entrada deve ser em CSV, com duas colunas, a primeira coluna é o título da Palestra, e a segunda coluna é o tempo de duração da palestra em minutos.</p>
        @include('flash::message')
        <div class="alert alert-light">
            <form action="/" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="csv_file">Carregar arquivo</label>
                    <input type="file" class="form-control-file" id="csv_file" name="csv_file">
                </div>
                <input type="submit" class="btn btn-success" value="Enviar">
            </form>
        </div>
    </div>
    @if($final_result != '')
    <div class="alert alert-secondary">
        <h4>Resultado</h4>
        <pre>{{$final_result}}</pre>
    </div>
    @endif
</div>
</body>
</html>
