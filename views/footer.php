</main><!-- End #main -->
<?php 
//Si hay algún mensaje emergente, lo mostramos
if (isset($params['resultado']) and $params['resultado']!="") {
	echo '<div id="emergente">'.$params['resultado'].'</div>';
}
?>
  <!-- Vendor JS Files -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" defer></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

  
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>


  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <!-- Mis JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
  <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
	<script type="text/javascript" src="js/jquery.lightbox_me.js"></script>
  <script type="text/javascript" src="js/jquery.magnific-popup.min.js"></script>
	<script type="text/javascript" src="js/script.js?ver=1.7"></script>
	<script src="js/alertify.min.js"></script>

    <script>
    $(document).ready(function() {
      // Función para mostrar el overlay
      function showLoadingOverlay() {
        $('#loading-overlay').css('display', 'flex');
      }

      // Agregar event listener a todos los enlaces del header y sidebar
      $('#header a, #sidebar a').on('click', function(e) {
        const href = $(this).attr('href');

        // Solo mostrar el spinner si el enlace tiene un href válido y no es un toggle o ancla
        if (href &&
            href !== '#' &&
            href !== '' &&
            !$(this).attr('data-bs-toggle') &&
            !href.startsWith('javascript:')) {
          showLoadingOverlay();
        }
      });
    });
  </script>
</body>

</html>