jQuery(document).ready(function($){
    $('#requerimiento').click(function(event) {
        var valueToSend = $('#dropColoniarq').val();
        $.ajax({
            type: "POST",
            url: ajaxurl, //variable global provista por Wordpress. /wp-admin/admin-ajax.php este we es que se encarga de manejar las llamadas ajax
            data: { selectColoniarq: valueToSend,
                action: 'btnrequerimiento'

            }
        })
        .done(function(data){

            alert("Requerimientos Generados");
        })
        .fail(function(data){
            alert("Necesitas estar Logueado");
        });
    });
});

jQuery(document).ready(function($){
    $(document).one('click', '#dropColoniarq', function(event) {
            $.ajax({
            type: "POST",
            url: ajaxurl, //variable global provista por Wordpress. /wp-admin/admin-ajax.php este we es que se encarga de manejar las llamadas ajax
            data: {action: 'pruebamerq' }
            })
            .done(function(data){
            var datajson = data.slice(0,-1);
            var colonia = JSON.parse(datajson);
            length = Object.keys(colonia).length;

                for(var i=1; i<length; i++){
                $('#dropColoniarq').append('<option value="'+colonia[i]+'">'+colonia[i]+'</option>');

                }
                $("#dropColoniarq option:selected").text();
                var s = document.getElementById("dropColoniarq");
                var t = s.options[s.selectedIndex].text;
                // var s = $('#dropColonia').val($("#dropColonia option:selected").text());

            })
            fail(function(data){
            alert("Necesitas estar Logueado ");
            });

    });
});
