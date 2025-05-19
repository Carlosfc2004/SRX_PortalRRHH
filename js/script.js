//Actualizamos una actividad 
function actu_emp(id){
	document.getElementById(id).submit();
}

function validNumber(evt){	
	var code = (evt.which) ? evt.which : evt.keyCode;
	
	if(code==8) { // backspace.
	  return true;
	} else if(code>=48 && code<=57) { // is a number.
	  return true;
	} else{ // other keys.
	  return false;
	}
}



$(document).ready(function() {
	$(".loader").fadeOut("slow");

	var currentUrl = window.location.href;
	$('ul.nav-content').each(function() {
		var elemento = $(this);
		elemento.find('a').each(function() {
		    var linkUrl = $(this).attr('href');
		    if (currentUrl.includes(linkUrl)) {
		        elemento.addClass('show');
		        $(this).addClass('active');
		    }
		});
	});
	
	//Mostramos los usuarios seleccionados
	// $(".user_remesas").on('click', function(event) {
	// 	var checked = $(".user_remesas:checked").length;
	// 	if (checked>0) {
	// 		$("#user_selec").html("("+checked + " candidatos seleccionados)");
	// 		$("#generar_rem").css({'display' : 'inline-block'});
	// 	}else{
	// 		$("#user_selec").html("");
	// 		$("#generar_rem").css({'display' : 'none'});
	// 	}
    // });

    // $('#txt_bus_rem').keyup(function(){
	// 	var nombres = $('.nombre_user_rem');
	// 	var buscando = $(this).val().toLowerCase();
	// 	var cont = 0;
	// 	if (buscando.length>2) {
	// 		for( var i = 0; i < nombres.length; i++ ){
	// 			if($(nombres[i]).html().toLowerCase().indexOf(buscando) > -1 ){
	// 				cont++;
	// 				$(nombres[i]).css({'background-color' : '#224738'});
	// 				$(nombres[i]).css({'color' : 'white'});
	// 			}else{
	// 				$(nombres[i]).css({'background-color' : 'transparent'});
	// 				$(nombres[i]).css({'color' : 'initial'});
	// 			}
	// 		}
	// 		$("#cant_resul_bus_rem").html(cont + " Resultados");
	// 	}else{
	// 		$('.nombre_user_rem').css({'background-color' : 'transparent'});
	// 		$('.nombre_user_rem').css({'color' : 'initial'});
	// 		$("#cant_resul_bus_rem").html("");
	// 	}
  	// });

	//Cambiamos el idioma
	
	
	$(".idioma").on('click', function(event) {
		event.preventDefault();  // Prevents the default anchor behavior (page navigation)
		var idioma = $(this).attr('data-idioma');
		console.log(idioma);
		$.get("auto.php?idioma=" + idioma);  // Sends the selected language code to auto.php
		location.reload();  // Reloads the page
	});

	
	//Obtenemos la sociedad cuando cambia de valor en informe campo
		// Función para actualizar el contenido basado en los valores de sociedad y division
		function actualizarContenido() {
			var sociedad = $('#sociedad').val();
			var division = $('#division').val();
			
			// Actualiza el contenido de #fincas_informe
			$('#fincas_informe').load('auto.php?load_fincas_soc=' + sociedad + '&division=' + division);
			
			// Actualiza el contenido de #operario_centro
			$('#operario_centro').load('auto.php?load_operarios=' + sociedad + '&division=' + division);
		}

		// Manejador de eventos para el cambio en #sociedad
		$('#sociedad').change(function() {
			actualizarContenido();
		});

		// Manejador de eventos para el cambio en #division
		$('#division').change(function() {
			actualizarContenido();
		});

		// Llamar a la función al cargar la página, si es necesario
		$(document).ready(function() {
			actualizarContenido();
		});
	
	// Puedes también querer cargar el contenido inicial en #operario_centro
	// si #sociedad tiene un valor por defecto
	$(document).ready(function() {
		var sociedad = $('#sociedad').val();
		if (sociedad) {
			$('#operario_centro').load('auto.php?load_operarios=' + sociedad + '&division=' + division);
		}
	});


	// Función para actualizar el select de ubicación cuando cambia el select de sedev en informe oficina
		function actualizarUbicacion() {
			var sede = $('#nombre_sede').val(); // Obtener el valor seleccionado de sede
			
			// Usar encodeURIComponent para evitar errores con caracteres especiales
			$('#nombre_ubi').load('auto.php?load_ubicaciones=' + encodeURIComponent(sede));
		}
	
		// Manejador de eventos para el cambio en el select de sede
		$('#nombre_sede').change(function() {
			actualizarUbicacion(); // Llamar a la función cada vez que cambie el valor de sede
		});

		// Llamar a la función al cargar la página, si es necesario
		$(document).ready(function() {
			actualizarUbicacion();
		});

	



	//Ventanas emergentes
	$("#emergente").lightbox_me({centered: true});

    //Ampliamos las imagenes de los productos
    $('.image-popup-vertical-fit').magnificPopup({
        type: 'image',
        closeOnContentClick: true,
        mainClass: 'mfp-img-mobile',
        image: {
            verticalFit: true
        }
        
    });
    $('.image-popup-fit-width').magnificPopup({
        type: 'image',
        closeOnContentClick: true,
        image: {
            verticalFit: false
        }
    });
    $('.image-popup-no-margins').magnificPopup({
        type: 'image',
        closeOnContentClick: true,
        closeBtnInside: false,
        fixedContentPos: true,
        mainClass: 'mfp-no-margins mfp-with-zoom',
        image: {
            verticalFit: true
        },
        zoom: {
            enabled: true,
            duration: 300
        }
    });




	// Select para filtrar por mas de un trabajador en auditoria
	if (document.getElementById('pernr_nom_trab')) {
		$(document).ready(function() {
			$('#pernr_nom_trab').select2({
				placeholder: "Seleccione uno o varios trabajadores",
				allowClear: true,
				width: '100%',
				minimumInputLength: 3,
				language: {
					inputTooShort: function() {
						return 'Por favor, ingrese 3 o más caracteres';
					},
					noResults: function() {
						return 'No se encontraron resultados';
					},
					searching: function() {
						return 'Buscando...';
					}
				},
			});
		})
	};


	// Select para filtrar por mas de un trabajador en auditoria
	if (document.getElementById('pernr_trab_alm')) {
		$(document).ready(function() {
			$('#pernr_trab_alm').select2({
				placeholder: "Seleccione uno o varios trabajadores",
				allowClear: true,
				width: '100%',
				closeOnSelect: false,
				minimumInputLength: 3,
				language: {
					inputTooShort: function() {
						return 'Por favor, ingrese 3 o más caracteres';
					},
					noResults: function() {
						return 'No se encontraron resultados';
					},
					searching: function() {
						return 'Buscando...';
					}
				},
			});
		});
	}


	// Select para filtrar por mas de un trabajador en solicitud
	if (document.getElementById('pernr_nom_sol')) {
		$(document).ready(function() {
			$('#pernr_nom_sol').select2({
				placeholder: "Seleccione uno o varios trabajadores",
				allowClear: true,
				width: '100%',
				minimumInputLength: 3,
				closeOnSelect: false,
				language: {
					inputTooShort: function() {
						return 'Por favor, ingrese 3 o más caracteres';
					},
					noResults: function() {
						return 'No se encontraron resultados';
					},
					searching: function() {
						return 'Buscando...';
					}
				},
			});
		})
	};

		

});