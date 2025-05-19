<?php 
include_once("header.php");
?>
	<div class="pagetitle">
    <h1><?php echo $lang['home2']; ?></h1>
  </div>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
    </ol>
  </nav>
  <section class="section">


    <div class="card">
      <div class="card-body">
        <br>
        <h5><?php echo "Bienvenido al portal ". $_SESSION['nombre_user_surexport_appreclu']; ?></h5>
      </div>
    </div>



    <!-- <div class="row"> -->
    <!-- Contrataciones mensuales -->

      <!-- <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <br>
              <h4><?php echo $lang['home4']; ?></h4>
            <br>
            <div class="col-md-12" id="columnChart"></div>
            <script>
              document.addEventListener("DOMContentLoaded", () => {
                new ApexCharts(document.querySelector("#columnChart"), {
                  series: [{
                    name: 'Trabajadores',
                    data: [<?php 
                      foreach ($params['total_cont'] as $value) {
                        echo $value['Total'].",";
                      }
                      ?>]
                  }],
                  chart: {
                    type: 'bar',
                    height: 350
                  },
                  plotOptions: {
                    bar: {
                      horizontal: false,
                      columnWidth: '65%',
                      endingShape: 'rounded'
                    },
                  },
                  dataLabels: {
                    enabled: false
                  },
                  stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                  },
                  xaxis: {
                    categories: [<?php 
                      foreach ($params['total_cont'] as $value) {
                        echo "'".$meses[$value['Mes']]." ".$value['Ano']."',";
                      }
                      ?>],
                  },
                  fill: {
                    opacity: 1
                  },
                  tooltip: {
                    y: {
                      formatter: function(val) {
                        return val
                      }
                    }
                  }
                }).render();
              });
            </script>
          </div>
        </div>
      </div> -->
    <!-- END Contrataciones mensuales -->





    <!-- Trabajadores por sociedad -->
    <?php  
      // $datos = '';
      // foreach ($params['sociedad_trab'] as $socie => $result) {
      //     $datos .= $result['CANTIDAD'] . ($socie < count($params['sociedad_trab']) - 1 ? ', ' : '');
      // }
      // $sociedades = '';
      // foreach ($params['sociedades'] as $result) {
      //   // Append the DESC_CENTRO and add comma between them
      //   $sociedades .= ($sociedades ? ', ' : '') . "'" . $result['DESC_CENTRO'] . "'";
      // }           
    ?>
      <!-- <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <br>
              <h4><?php echo $lang['home5']; ?></h4>
            <br>
            <div id="pieChart"></div>
            <script>
              document.addEventListener("DOMContentLoaded", () => {
                new ApexCharts(document.querySelector("#pieChart"), {
                  series: [<?php echo $datos ?>],
                  chart: {
                    height: 400,
                    type: 'pie',
                    toolbar: {
                      show: true
                    }
                  },
                  labels: [<?php echo $sociedades; ?>]
                }).render();
              });
            </script>
          </div>
        </div>
      </div> -->
    <!-- End Trabajadores por sociedad -->

    
    <!-- Trabajadores Activos -->
      <?php  
        // $datos = '';
        // $sociedades = '';
        // foreach ($params['trabajadoresAct'] as $key => $result) {
        //   $datos .= $result['emp_activos'] . ($result === end($params['trabajadoresAct']) ? '' : ',');
        //   $sociedades .= ($sociedades ? ', ' : '') . "'" . $result['DESC_CENTRO'] . "'";
        // }
      ?>
        <!-- <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
            <br>
              <h4><?php echo $lang['home6']; ?></h4>
            <br>
              <div id="pieChart2"></div>

              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  new ApexCharts(document.querySelector("#pieChart2"), {
                    series: [<?php echo $datos; ?>],
                    chart: {
                      height: 400,
                      type: 'pie',
                      toolbar: {
                        show: true
                      }
                    },
                    labels: [<?php echo $sociedades; ?>]
                  }).render();
                });
              </script>
            </div>
          </div>
        </div> -->
      <!-- End Trabajadores activos -->  
    <!-- </div> -->
  </section>
<?php include_once("footer.php"); ?>