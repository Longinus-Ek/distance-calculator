<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Distance Calculator</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body>
    <div class="container justify-content-center" style="margin-top: 50px">
        <form id="DADOSFORMCONSULTACEP">
            <input class="form-control" name="id" id="idPesquisa" type="hidden"/>
            <div class="row">
                <div class="col-4">
                    <div class="form-floating mb-3">
                        <input class="form-control" name="cep1" id="cep1" type="text" placeholder="XXXXX-XXX"/>
                        <label for="cep1">Cep Inicial</label>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-floating mb-3">
                        <input class="form-control" name="cep2" id="cep2" type="text" placeholder="XXXXX-XXX"/>
                        <label for="cep2">Cep Final</label>
                    </div>
                </div>
                <div class="col-4">
                    <button class="btn btn-outline-success me-2" style="margin-top: 10px" onclick="saveDados()" type="button">Pesquisar</button>
                    <button class="btn btn-outline-danger" style="margin-top: 10px" onclick="limparCampos()" type="button">Limpar</button>
                </div>
                <h5>Importar arquivo</h5>
                <div class="col-3">
                    <input type="file" name="csv_file" id="csv_file">
                </div>
                <div class="col-6">
                    <button class="btn btn-outline-success" style="margin-top: 10px" onclick="enviarArquivo()" type="button">Importar CSV</button>
                </div>
            </div>
        </form>
        <table class="table table-striped table-hover" id="distance-table">
            <thead>
                <tr>
                    <th>Cep Origem</th>
                    <th>Cep Fim</th>
                    <th>Distância</th>
                    <th>Data da Pesquisa</th>
                    <th>Data da Alteração</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody id="TBODYDISTANCETABLE"></tbody>
        </table>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

<script type="text/javascript">
    $(function(){
        getDados();
    })
    function limparCampos(){
        $('#DADOSFORMCONSULTACEP').each(function(){
            this.reset()
        })
        $('#idPesquisa').val(null)
    }
    function montaDatatable(dados){
        let tbodyDOM = $('#TBODYDISTANCETABLE')
        tbodyDOM.empty()
        dados.forEach( (item) => {
            let tbody = `<tr>
                            <td>${item.cepOrigem}</td>
                            <td>${item.cepFim}</td>
                            <td>${item.distance} km</td>
                            <td>${item.dataEmiss}</td>
                            <td>${item.dataAlter}</td>
                            <td>${item.action}</td>
                        </tr>`

            tbodyDOM.append(tbody)
        })
    }

    function enviarArquivo() {
        let formData = new FormData();
        let fileInput = document.getElementById('csv_file');

        if (fileInput.files.length > 0) {
            formData.append('csv_file', fileInput.files[0]);
            alert('Após pressionar o botão ok, o processo de importação será iniciado! espere até o final do processo!')
            $.ajax({
                type: 'POST',
                url: '{{route('distance.import')}}',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.success) {
                        alert('Arquivo enviado com sucesso!');
                        getDados()
                    } else {
                        alert('Erro ao enviar o arquivo:', data.message);
                    }
                },
                error: function (xhr, status, error) {
                    alert('Erro ao enviar o arquivo:', error);
                }
            });
        } else {
            alert('Nenhum arquivo selecionado.');
        }
    }

    function saveDados() {
        let url = '{{route('distance.store')}}'
        let type = 'POST'
        let id = $('#idPesquisa').val()
        if(id.length > 0){
            url = '{{route('distance.update', ['IDPROBJTORRO'])}}'
            url = url.replace('IDPROBJTORRO', id)
            type = 'PUT'
        }
        $.ajax({
            type: type,
            url: url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: $('#DADOSFORMCONSULTACEP').serializeArray(),
            success: function (data) {
                if(data.error){
                    alert(data.msg)
                }else{
                    console.log(data);
                    limparCampos();
                    getDados();
                }
            }
        });
    }

    function editDados(prID) {
        let url = '{{route('distance.show', ['IDPRDIST'])}}'
        url = url.replace('IDPRDIST', prID)
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                console.log(data)
                $('#idPesquisa').val(data.id)
                $('#cep1').val(data.cepIn)
                $('#cep2').val(data.cepFn)
            }
        });
    }

    function getDados() {
        let url = '{{route('distance.create')}}'
        $.ajax({
            type: 'GET',
            url: url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                console.log(data)
                montaDatatable(data)
            }
        });
    }
</script>
</html>