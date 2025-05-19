<?php 
include_once("header.php");
?>

  <main>
    <div class="container">
      <section class="section error-404 d-flex flex-column align-items-center justify-content-center" style="width: 80%; margin-left: 10%;">
        <h1>404</h1>
        <h2><?php echo $lang['tit_404'];?></h2>
        <a class="btn" href="admin_cont.php?controller=index&action=home"><?php echo $lang['volver_404'];?></a>
        <img src="img/not-found.svg" class="img-fluid py-5" alt="Page Not Found">
      </section>
    </div>
  </main>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>


<?php 
include_once("footer.php");
?>
