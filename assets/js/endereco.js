$(function(){
    function concatena_enderco(){
        var nome_logradouro = $('#En_Nome_Logradouro').editable('getValue', true);
        var cep = $('#En_CEP').editable('getValue', true);
        var numero = $('#En_Num').editable('getValue', true);
        var complemento = $('#En_Complemento').editable('getValue', true);
        var bairro = $('#En_Bairro').editable('getValue', true);
        var municipio = $('#En_Municipio').editable('getValue', true);
        var estado = $('#En_Estado').editable('getValue', true);
        if(cep && nome_logradouro && numero && bairro && municipio && estado){
            var endereco =  nome_logradouro + ", " + numero + (complemento ? ", " + complemento : " ") + ", " + bairro + ", " + cep  + ", " + municipio + ", " + estado;
            $('#endereco').editable('setValue', endereco);
            $('#endereco').trigger('changeAddress', endereco);
            $('.js-endereco').html(endereco);
        }


    };

    $('#En_Nome_Logradouro, #En_CEP, #En_Num, #En_Complemento, #En_Bairro, #En_Municipio,  #En_Estado').on('hidden', function(e, params) {
        concatena_enderco();
    });

    $('#En_CEP').on('hidden', function(e, params){
        var cep = $('#En_CEP').editable('getValue', true);
        $.getJSON('http://cep.correiocontrol.com.br/'+cep+'.json',function(r){
            $('#En_Nome_Logradouro').editable('setValue', r.logradouro);
            $('#En_Bairro').editable('setValue', r.bairro);
            $('#En_Municipio').editable('setValue', r.localidade);
            $('#En_Estado').editable('setValue', r.uf);
            concatena_enderco();
        });

    });
});