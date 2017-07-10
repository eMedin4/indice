
$( document ).ready(function() {

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

/************************************************************************************************
    RESPONSIVE MENU
************************************************************************************************/

$('.toggle-icon').on('click', function() {
    $('body').toggleClass('menu-visible');
});

$('.darken-overlay, .info-close').on('click', function() {
    $('body').removeClass('menu-visible');
})


/************************************************************************************************
    SEARCH
************************************************************************************************/





/************************************************************************************************
    MODAL GENERIC
************************************************************************************************/





/************************************************************************************************
    FILTER
************************************************************************************************/
/*
$('.icon-filter').on('click', function() {
    $('.filter-wrap').addClass('display');
});

var starsSlider = document.getElementById('js-stars-slider');

noUiSlider.create(starsSlider, {
    start: [ 0,  6],
    connect: [false, true, false],
    range: {
        'min': 0,
        'max': 6
    },
    step: 1,
    margin: 1,
    pips: {
        mode: 'steps',
        density: 12
    },
    format: {
      to: function ( value ) {
        return value;
      },
      from: function ( value ) {
        return value.replace(',-', '');
      }
    }
});

starsSlider.noUiSlider.on('change', function() {
    $('#from-stars').val(starsSlider.noUiSlider.get()[0]);
    $('#to-stars').val(starsSlider.noUiSlider.get()[1]);
});

$('.to-year').on('change', function() {
    var to = $(this).val();
    var from = $('.from-year').val();
    if (from > to) {
        $('.from-year').val(to);
    }
});

$('.from-year').on('change', function() {
    var to = $('.to-year').val();
    var from = $(this).val();
    if (from > to) {
        $('.to-year').val(from);
    }
});

$('#select-all').change(function() {
    var checkboxes = $('.channel-group').find(':checkbox');
    if($(this).is(':checked')) {
        checkboxes.prop('checked', true);
    } else {
        checkboxes.prop('checked', false);
    }
});

$('.btn-channel-dropdown').on('click', function() {
    $('.channel-group').toggle();
});

//cerrar el modal
$('.filter-wrap').on('click', function() {
    $(this).removeClass('display');
});
//clickando en inner no se cerrará, excepto todo lo que tenga .propagation que si se cerrará
$('.filter .inner').on('click', function (e) {
    if(!$(e.target).is('.propagation')){
      e.stopPropagation();
    }
});
*/

/************************************************************************************************
    MODAL LOGIN
************************************************************************************************/

$('.js-launch-login').on('click', function() {
    var facebook = $('.single-wrap').data('facebook');
    var google = $('.single-wrap').data('google');
    var info = $(this).parent().parent().data('info');
    var html = 
        `<div class="login-modal panel-modal">
            <h3>Entra</h3>
            <h4>en Indicecine</h4>
            <a class="social-btn facebook" href="{{route('authsocial', ['provider' => 'facebook'])}}">
               <i class="fa fa-facebook-fa" aria-hidden="true"></i>
               <span>Entra con Facebook</span>
            </a>
            <a class="social-btn google" href="{{route('authsocial', ['provider' => 'google'])}}">
               <i class="fa fa-google" aria-hidden="true"></i>
               <span>Entra con Google</span>
            </a>
            <div class="oval-shape"></div>
            <p>` + info + `</p>
        </div>`;
    $('.modal .inner').html(html);
    $('.modal-wrap').fadeIn(500).css('display','table');
    $('.modal input').focus();
});


/************************************************************************************************
    MODAL NEW LIST
************************************************************************************************/

$('.js-new-list').on('click', function(){
    var t = $(this);
    var csrf = t.data('csrf');
    var movie = t.data('movie');
    var url = t.data('url');
    var html = 
        `<form method="POST" action="` + url + `" class="modal-new-list" data-movie="` + movie + `">
            <input type="hidden" name="_token" value="` + csrf + `">
            <h3>Nueva lista</h3>
            <div class="errors"></div>
            <input type="text" name="name" maxlength="48" placeholder="Nombre">           
            <textarea name="description" rows="3" maxlength="500" placeholder="Descripción"></textarea>
            <div class="btn-group">
                <button type="submit" class="btn">Crear</button>
                <button type="button" class="btn btn-cancel propagation">Cancelar</button>
            </div>
            <div class="checkbox">
                <input id="check-description" type="checkbox" name="check-description">
                <label class="lbl-check" for="check-description">Añadir descripción</label>
            </div>
            <div class="checkbox">
                <input id="check-ordered" type="checkbox" name="check-ordered">
                <label class="lbl-check" for="check-ordered">Lista numerada</label>
            </div>
        </form>`;
    $('.modal .inner').html(html);
    $('.modal-wrap').fadeIn(500).css('display','table');
    $('.modal input').focus();
});


/************************************************************************************************
    CREATE NEW LIST
************************************************************************************************/

$('.modal').on('submit', '.modal-new-list', function(e){
    e.preventDefault(e);
    var t = $(this);
    var url = t.attr('action');
    var movie = t.data('movie');
    var name = t.find('input[type="text"]').val();
    var description = t.find('textarea').val();
    var ordered = t.find('#check-ordered').is(":checked") ? 1 : 0;
    $.ajax({
        url: url,
        type: 'POST',
        data: { 'name': name, 'movie': movie, 'description': description, 'ordered': ordered }
    })
    .done(function(data) {
        if (!data.state) {
            t.find('.errors').text(data.message);
        } else {
            $('.modal-wrap').fadeOut(500);
            if (movie) { /*Si estamos añadiendo desde single movie será el id de la película, si añadimos desde user-lists movie será 0*/
                var html = '<li><span class="disable-add-list recent-list">' + data.name + '<i class="icon-check-list fa fa-check"></i></span></li>';  
                //para que funcione el efecto lo cargamos previamente
                var new_item = $(html).hide();
                $('.my-lists').append(new_item);
                new_item.show('slow');
            } else {
                //que hacer cuando añadimos desde user-lists
                var html= `
                    <article class="new-grid">
                        <a class="list" href="` + $('.js-new-list').data('path') + `lista/` + data.slugname + `/` + data.id + `">
                            <div class="list-image relative">
                                <div class="loop-no-image"></div>
                            </div>
                            <div class="meta">
                                <span>
                                    <span>No hay nada </span>
                                    <i class="separator">·</i>
                                    <span class="no-wrap"> ahora</span>
                                </span>
                            </div>
                            <div class="loop-title">
                                <h3>` + data.name + `</h3>
                            </div>
                        </a>
                    </article>`;
                $('.loop').prepend(html);
            }
        }
    })
    .fail(function(data) {
        var parsed = $.parseJSON(data.responseText).name;
        t.find('.errors').text(parsed);
    });
});



if($('body').is('.list222-page')){


/************************************************************************************************
    SETTINGS RUBAXA SORTABLE
************************************************************************************************/

    var rubitems = document.getElementById('js-loop');

    var sortable = Sortable.create(rubitems, {
        disabled: true,
        animation: 150,
        handle: ".medium-image",
        chosenClass: "js-chosen",  // Clase mientras arrastramos
        filter: ".js-ignore-edit",
        onUpdate: function () { //reordenamos en la etiqueta order
            var i = 1;
            $('.order').each(function (index) {
                var t = $(this);
                var old = t.data('current');
                if (old > i) {
                    t.html(i + '<i class="icon-order-up fa fa-arrow-up-fi"></i>');
                } else if (old < i) {
                    t.html(i + '<i class="icon-order-down fa fa-arrow-down"></i>');
                } else {
                    t.html(i);
                }
                i++;
            });
        },
    });


/************************************************************************************************
    ACTIVAR MODO EDICIÓN
************************************************************************************************/

    $('.js-on-edit').on('click', function() {
        sortable.option('disabled', false);
        $('.info-default').fadeOut(300).promise().done(function(){
            $('.info-edit').fadeIn(300);
        });
        $('.loop article').addClass('article-edit');
        $('.medium-image').append('<i class="delete-movie fa fa-times"></i>')
        $('.movie').on('click', function(e) { //la película no es clickable en modo edit
            e.preventDefault();
        })
    });

/************************************************************************************************
    SALIR MODO EDICIÓN
************************************************************************************************/
    var offedit = function() {
        sortable.option('disabled', true);
        $('.info-edit').fadeOut(300).promise().done(function(){
            $('.info-default').fadeIn(300);
        });
        $('.loop article').removeClass('article-edit');
        $('.delete-movie').remove();
        $('.movie').unbind('click'); /*Recupera su acción por defecto (inhabilita preventdefault)*/            
    }

    $('.js-off-edit').on('click', function() {
        offedit();
    });

/************************************************************************************************
    EDITAR INFO
************************************************************************************************/

$('.js-edit-list').on('click', function(){
    var t = $(this);
    var name = $('.info-edit .name').text();
    var description = $('.info-edit .description').text();
    var order = t.data('order');
    var html = 
        `<form class="modal-edit-list">
            <h3>Editar lista</h3>
            <div class="errors"></div>
            <input type="text" name="name" maxlength="48" value="` + name + `">           
            <textarea name="description" rows="3" maxlength="500" placeholder="Descripción" ` + (description ? "style='display:block;'" : "")  + `>` + description + `</textarea>
            <div class="btn-group">
                <button type="submit" class="btn">Actualizar</button>
                <button type="button" class="btn btn-cancel propagation">Cancelar</button>
            </div>
            <div class="checkbox">
                <input id="check-description" type="checkbox" name="check-description" ` + (description ? "checked" : "")  + `>
                <label class="lbl-check" for="check-description">Añadir descripción</label>
            </div>
            <div class="checkbox">
                <input id="check-ordered" type="checkbox" name="check-ordered" ` + (order == 1 ? "checked" : "")  + `>
                <label class="lbl-check" for="check-ordered">Lista numerada</label>
            </div>
        </form>`;
    $('.modal .inner').html(html);
    $('.modal-wrap').fadeIn(500).css('display','table');
    $('.modal input').focus();
});

$('.modal').on('submit', '.modal-edit-list', function(e){
    e.preventDefault(e);
    var name = $('input[name=name]').val();
    if ($('#check-description').is(":checked"))   
        var description = $('textarea[name=description]').val();
    else
        var description = "";
    $('.info-edit .name').text(name);
    $('.info-edit .description').text(description);
    $('.modal-wrap').fadeOut(500);
});

/************************************************************************************************
    VALIDAR EDICIÓN
************************************************************************************************/

$('.edit-submit').on('click', function() {
    var t = $(this);
    var url = t.data('url');
    var list = t.data('id');
    var title = $('.info-edit .name').text();
    var description = $('.info-edit .description').text();
    var movies = [];
        $('.movie').each(function (index) {
            var id = $(this).data('id');
            movies[index] = id;
        });
    $.ajax({
        url: url,
        type: 'POST',
        data: { 'list': list, 'movies': movies, 'title': title, 'description': description }
    })
    .done(function(data) {
        offedit();
        $('.original-name').text(title);
        //existia <h2>description</h2>? SI
        if ($('.original-description').length) {
            //existe ahora description?
            if (description) {
                $('.original-description').text(description);
            } else {
                $('.original-description').remove();
            }
        //existia <h2>description</h2>? NO
        } else {
            //existe ahora description?
            if (description) {
                $('.original-name').after('<h2 class="original-description">' + description + '</h2>');
            }
        }    
        $('.time').text('Actualizada ahora');
    })
    .fail(function(data) {
        console.log('error');
    });      

});

/************************************************************************************************
    BORRAR LISTA
************************************************************************************************/

$('.edit-delete').on('click', function() {
    var name = $(this).data('name');
    var html=`<div>Vas a borrar la lista</div>
        <h3>`+ name + `</h3>
        <div class="btn-group-alt">
            <span class="btn btn-cancel propagation">Cancelar</span>
            <span class="btn btn-alert edit-delete-confirm propagation">Borrar definitivamente</span>
        </div>`;
    $('.modal .inner').html(html);
    $('.modal-wrap').fadeIn(500).css('display','table');
});


$('.modal').on('click', '.edit-delete-confirm', function() {
    var id = $('.edit-delete').data('id');
    var url = $('.edit-delete').data('url');
    var redirect_url = $('.edit-delete').data('redirect');
    $.ajax({
        url: url,
        type: 'POST',
        data: { 'id': id }
    })
    .done(function(data) {
        console.log(data);
        window.location.href = redirect_url;
    })
    .fail(function(data) {
        console.log('error');
    });      
});



/************************************************************************************************
    GUARDAR LISTA EN MIS LISTAS
************************************************************************************************/

$('.info').on('click', '.js-add-to-mylists', function(){
    var t = $(this);
    var list = t.data('id');
    var url = t.data('url');
    $.ajax({
        url: url,
        type: 'POST',
        data: { 'list': list }
    })
    .done(function(data) {
        t.removeClass('js-add-to-mylists').addClass('btn-success btn-single').html('¡Guardada en mis listas!');
    })
    .fail(function(data) {
        console.log('error');
    });        
});


/************************************************************************************************
    BORRAR LISTA DE MIS LISTAS
************************************************************************************************/

$('.info').on('click', '.js-del-from-mylists', function(){
    var t = $(this);
    var list = t.data('id');
    var url = t.data('url');
    $.ajax({
        url: url,
        type: 'POST',
        data: { 'list': list }
    })
    .done(function(data) {
        t.siblings('.btn-success').remove();
        t.removeClass('js-del-from-mylists btn-double').addClass('btn-success btn-single').text('¡Borrada de mis listas!');
    })
    .fail(function(data) {
        console.log('error');
    });        
});


}/*endif is .list-page*/

/************************************************************************************************
    SUMMARY
************************************************************************************************/

/*OCULTAR LISTAS DE REPARTO DEMASIADO LARGAS*/
/*$('.js-characters a:lt(10)').show();
$('.more').on('click', function() {
	$('.js-characters a').fadeIn();
	$(this).fadeOut();
});*/

/*MENU SUMMARY MOBILE*/
$('.summary-menu').on('click', '.launch-menu', function() {
    var t = $(this);
    var selector = '.' + t.data('launch');
    $('.summary-part').fadeOut(200);
    $(selector).fadeIn(200);
    $('.summary-menu .active').removeClass('active').addClass('launch-menu');
    t.removeClass('launch.menu').addClass('active');
});




/************************************************************************************************
    new SHOW TEXTAREA DESCRIPTION
************************************************************************************************/

$('.modal').on('change', '#check-description', function() {
    if($('#check-description').is(":checked"))   
        $(".modal textarea").fadeIn(300);
    else
        $(".modal textarea").hide();
});


/************************************************************************************************
    new MODALS GENERIC
************************************************************************************************/


$('.modal-wrap').on('click', function () {
    $(this).removeClass('display');
});

$('.modal-inner').on('click', function (e) {
    //hacemos que con el boton modal-close si se cierre el modal
    if(!$(e.target).is('.propagation')){
      e.stopPropagation();
    } 
});

$('.btn-launch-lists').on('click', function() {
    $('.modal-wrap-add-to-list').addClass('display');
});

$('.btn-new-list').on('click', function() {

    //¿donde estamos?: info, add-to-list o edit-list
        var position = $(this).data('position');
        $('.position').val(position);

    //si viene de add-to-list lo ocultamos
        $('.modal-wrap-add-to-list').removeClass('display');

    //si viene de edit list rellenamos los campos
        if (position == 'edit-list') {
            var title = $('h1').text();
            var description = $('.list-description').text();
            $('input[name=name]').val(title);
            if (description) {
                $('textarea[name=description]').val(description);
                $(".modal textarea").show();
                $('#check-description').prop('checked', true);
            }
            $('.modal-wrap-new-list h3').text('Editar lista');
            $('.modal-wrap-new-list button[type="submit"]').text('Editar');
        } else {

    //si no los vaciamos por si previamente se han rellenado
            $('input[name=name]').val('');
            $(".modal textarea").hide();
            $('textarea[name=description]').val('');
            $('#check-description').prop('checked', false);
            $('#check-ordered').prop('checked', false);
            $('.modal-wrap-new-list h3').text('Crear nueva lista');
            $('.modal-wrap-new-list button[type="submit"]').text('Crear');
            
        }

    //y mostramos
        $('.modal-wrap-new-list').addClass('display');
});

$('.btn-launch-filters').on('click', function() {
    $('.modal-wrap-filters').addClass('display');
});

$('.btn-delete').on('click', function() {
    var t = $(this);
    var id = t.data('id');
    var type = t.data('type');
    var text = t.data('text');
    $('.form-delete input[name="id"]').val(id);
    $('.form-delete input[name="type"]').val(type);
    $('.modal-wrap-confirm h3').text(text);
    $('.modal-wrap-confirm').addClass('display');
});




/************************************************************************************************
    AÑADIR PELÍCULA A UNA LISTA
************************************************************************************************/

    $('.add-to-list').on('click', '.add-to-list-active', function(){
        var t = $(this);
        var addtolist = $('.add-to-list');
        var list = t.data('id');
        var ordered = t.data('ordered');
        var movie = addtolist.data('movie');
        var url = addtolist.data('url');
        //icono de espera
        t.append('<div class="wait"><i class="fa fa-circle-o-notch"></i></div>');
        $.ajax({
            url: url,
            type: 'POST',
            data: { 'list': list, 'movie': movie, 'ordered': ordered }
        })
        .done(function(data) {
            t.find('.wait').remove();
            t.removeClass('add-to-list-active').addClass('add-to-list-disable');
            var count = parseInt(t.find('.item-count').text()) + 1;
            t.find('.item-count').text(count);
            $('.item-list').find('li[data-id=' + list +'] .item-count').text(count);
        })
        .fail(function(data) {
            t.find('.wait').remove();
        });        
    });

/*BORRAR PELÍCULA DE LISTA*/

    $('.add-to-list').on('click', '.add-to-list-disable', function(){
        var t = $(this);
        var addtolist = $('.add-to-list');
        var list = t.data('id');
        var movie = addtolist.data('movie');
        var url = addtolist.data('alturl');
        //icono de espera
        t.append('<div class="wait"><i class="fa fa-circle-o-notch"></i></div>');
        $.ajax({
            url: url,
            type: 'POST',
            data: { 'list': list, 'movie': movie }
        })
        .done(function(data) {
            t.find('.wait').remove();
            t.removeClass('add-to-list-disable').addClass('add-to-list-active');
            var count = parseInt(t.find('.item-count').text()) - 1;
            t.find('.item-count').text(count);
            $('.item-list').find('li[data-id=' + list +'] .item-count').text(count);
        })
        .fail(function(data) {
            t.find('.wait').remove();
        });   
    });


/************************************************************************************************
    CREAR LISTAS O EDITAR SU INFO
************************************************************************************************/

    $('.form-new-list').submit(function(e) {
        e.preventDefault(e);
        var t = $(this);
        var name = t.find('input[name="name"]').val();
        var description = t.find('textarea[name="description"]').val();
        var ordered = t.find('#check-ordered').is(":checked") ? 1 : 0;
        var movie = t.data('movie');
        var position = $('.position').val();

        //SI ESTAMOS CREANDO UNA LISTA DESDE INFO
        if (position == 'info') {
            var url = t.data('actionnew');
            $.ajax({
                url: url,
                type: 'POST',
                data: { 'name': name, 'movie': movie, 'description': description, 'ordered': ordered, 'position': position }
            })
            .done(function(data) {
                $('.modal-wrap-new-list').removeClass('display');
                /*var html = `<li data-id="` + id + `"><a href="` + link + `"><span>` + name + `</span><span class="item-count">` + count + `</span></a></li>`;
                $('.my-likes').append(html).hide().fadeIn(300);*/
            })
            .fail(function(data) {
                console.log(data);
            });    

        //SI ESTAMOS CREANDO UNA LISTA DESDE ADD-TO-LIST
        } else if (position == 'add-to-list') {
            var url = t.data('actionnew');
            $.ajax({
                url: url,
                type: 'POST',
                data: { 'name': name, 'movie': movie, 'description': description, 'ordered': ordered, 'position': position }
            })
            .done(function(data) {
                $('.modal-wrap-new-list').removeClass('display');
                    $('.modal-wrap-add-to-list').addClass('display');    
                    var html = `<li><div class="add-to-list-disable lbl-check">` + data.name + 
                    `<span class="item-count">1</span>`;
                    $('.add-to-list li:first').before(html);
            })
            .fail(function(data) {
                console.log(data);
            });    

        //SI LA ESTAMOS EDITANDO
        } else if (position == 'edit-list') {
            var url = t.data('actionedit');
            var id = $('h1').data('id');
            $.ajax({
                url: url,
                type: 'POST',
                data: { 'name': name, 'description': description, 'ordered': ordered, 'id': id }
            })
            .done(function(data) {
                $('h1').hide().text(name).fadeIn();
                $('.list-description').hide().text(description).fadeIn();
                $('.modal-wrap-new-list').removeClass('display');
            })
            .fail(function(data) {
                console.log(data);
            });
        }
    });

/************************************************************************************************
    BORRAR PELICULAS O LISTAS DESDE EL MODAL DE CONFIRMACIÓN (EN EDITLIST)
************************************************************************************************/

$('.form-delete').on('submit', function(e) {
    e.preventDefault(e);
    var type = $('.form-delete input[name="type"]').val();
    var movieId = $('.form-delete input[name="id"]').val();
    var listId = $('h1').data('id');

    //SI ES UNA PELÍCULA
    if (type == 'delete-movie') {
        var url = $(this).data('actionmovie');
        $.ajax({
            url: url,
            type: 'POST',
            data: { 'list': listId, 'movie': movieId }
        })
        .done(function(data) {
            $('.modal-wrap').removeClass('display');
            var selector = "article[data-id=" + movieId + "]";
            $(selector).remove();
        })
        .fail(function(data) {
            console.log(data);
        });      


    //SI ES UNA LISTA
    } else if (type == 'delete-list') {
        var url = $(this).data('actionlist');
        var redirect = $(this).data('redirect');
        $.ajax({
            url: url,
            type: 'POST',
            data: { 'listid': listId }
        })
        .done(function(data) {
            $('.modal-wrap').removeClass('display');
            console.log(data);
            window.location.href = redirect;
        })
        .fail(function(data) {
            console.log(data);
        });  
    }

});




/************************************************************************************************
    ME GUSTA A LISTA
************************************************************************************************/


$('.list-info').on('click', '.btn-launch-like', function() {
    var h1 = $('h1');
    var t = $(this);
    var id = h1.data('id');
    var url = t.data('url');
    var alturl = $(this).data('alturl');
    console.log(url);
    $.ajax({
        url: url,
        type: 'POST',
        data: { 'id': id }
    })
    .done(function(data) {
        //transformamos boton de like a dislike
            t.removeClass('launch-like btn-launch-like').addClass('launch-dislike btn-launch-dislike');
            t.find('i').removeClass('fa-heart-full-outline').addClass('fa-check');
            t.data('url', alturl).data('alturl', url);
        //creamos la lista en info-lista
            var link = h1.data('link');
            var name = h1.text();
            var count = h1.data('counter');
            var html = `<li data-id="` + id + `"><a href="` + link + `"><span>` + name + `</span><span class="item-count">` + count + `</span></a></li>`;
            $('.my-likes').append(html).hide().fadeIn(300);
    })
    .fail(function(data) {

    });  
});


/*YA NO ME GUSTA*/
    
$('.list-info').on('click', '.btn-launch-dislike', function() {
    var t = $(this);
    var id = $('h1').data('id');
    var url = t.data('url');
    var alturl = $(this).data('alturl');
    console.log(url);
    $.ajax({
        url: url,
        type: 'POST',
        data: { 'id': id }
    })
    .done(function(data) {
        //transformamos boton de dislike a like
            t.removeClass('launch-dislike btn-launch-dislike').addClass('launch-like btn-launch-like');
            t.find('i').removeClass('fa-check').addClass('fa-heart-full-outline');
            t.data('url', alturl).data('alturl', url);
        //borramos de la info-lista
            $('.my-likes').find('li[data-id=' + id + ']').fadeOut(300, function() { $(this).remove(); });
    })
    .fail(function(data) {

    });  
});


/************************************************************************************************
    new BUSCAR
************************************************************************************************/

/*$('.search-launch').on('click', function() {
    $('.search').addClass('visible');
    $('.input-search').focus();
});

$('.search .close').on('click', function() {
    $('.search').removeClass('visible');
});

$('.input-search').focusout(function() {
    $('.search-results, .search-results-wrap').fadeOut(300);
});         

$('.input-search').focusin(function() {
    //para que solo aparezcan si hay resultados
    if ($('.search-item').length && $(this).length) {
        $('.search-results, .search-results-wrap').fadeIn(300);
    }
});*/



$('.input-search').bind('paste keyup', function() {
    var t = $(this);
    var string = t.val();
    var ilength = string.length;
    var url = t.data('url'); 
    var path = t.data('path');
    if (ilength > 2) {  
        $.ajax({
            url: url,
            type: 'POST',
            data: { 'string': string }
        }).done(function(data) {
            if (data.response == true) { /*si hay resultados*/
                $('.search-results').html('<div class="inner"></div>');
                /*$('.loop').html('');*/
                $.each(data.result, function(key,val) {
                    if (val.check_poster) {
                        var fullImgPath = path + `assets/movieimages/posters/sml/` + val.slug + `.jpg`;
                    } else {
                        var fullImgPath = path + `assets/images/no-poster-small.png`;
                    }
                    
                    var html = 
                    `<div class="search-item">
                        <a href="` + path + val.slug + `">
                            <img src="` + fullImgPath + `" width="30" height="45"> 
                        </a>
                        <div class="search-item-data">
                            <a class="title" href="` + path + val.slug + `">` + val.title + `</a>
                            <div class="loop-features">`
                                + val.year + 
                                `<div class="country country-` + val.country + `" title="` + val.country + `"></div>
                                <div class="stars stars-` + val.avg + `"></div>
                            </div>
                        </div>
                    </div>`;
                    $('.search-results .inner').append(html);
                });
            } else {
                $('.search-results').html('');
            }
        }).fail(function() {
            console.log('no se envia');
        });
    } else { //si tiene menos de 3 carácteres
        $('.search-results').html('');
    }
});


/************************************************************************************************
    new DESPLEGAR ACTORES
************************************************************************************************/

$('.more').on('click', function() {
    $('.actors .hide').addClass('show-inline');
    $(this).addClass('hide');
});

});
