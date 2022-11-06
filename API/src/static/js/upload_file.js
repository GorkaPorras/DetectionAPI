function readURL(input) {
    if (input.files && input.files[0]) {

        var reader = new FileReader();

        reader.onload = function (e) {
            $('.image-upload-wrap').hide();
            
            if(e.target.result.toString().split("/", 1)[0].localeCompare('data:image')){

                $('.file-upload-image').attr('src', '../static/img/video.png');
            }
            else{

                $('.file-upload-image').attr('src', e.target.result);     
            }

            $('.file-upload-content').show();

            $('.image-title').html(input.files[0].name);


            $('#btn_enviar').removeAttr('disabled');

        };

        reader.readAsDataURL(input.files[0]);

    } else {
        removeUpload();
    }
}

//Deshabilitar boton de enviar del formulario si no hay nada seleccionado
function disable() {
    if ($('.file-upload-content').is(':visible')) {
        $('#btn_enviar').removeAttr('disabled');
    }
    else {
        $('#btn_enviar').attr('disabled', 'disabled');
    }

}

//Borrar del formulario imagen/video seleccionado
function removeUpload() {
    $('.file-upload-input').replaceWith($('.file-upload-input').clone());
    $('.file-upload-content').hide();
    $('.image-upload-wrap').show();
    $('#btn_enviar').attr('disabled', 'disabled');
}
$('.image-upload-wrap').bind('dragover', function () {
    $('.image-upload-wrap').addClass('image-dropping');
});
$('.image-upload-wrap').bind('dragleave', function () {
    $('.image-upload-wrap').removeClass('image-dropping');
});



