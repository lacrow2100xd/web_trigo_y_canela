<?php
require 'config/config.php';
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

$sql = $con->prepare("SELECT id, nombre, imagen, precio FROM productos WHERE activo=1");
$sql->execute();
$resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trigo y canela</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/productos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
</head>
<body>
    
<header data-bs-theme="white">
  
  <div class="navbar navbar-expand-lg navbar-white bg-white ">
    <div class="container">
      <a href="index.php" class="navbar-brand">
        <div class="logo-name">
            <img src="Img/Diseño_sin_título__1_-removebg-preview.png" alt="Logo Panadería">
            <strong>Trigo y canela</strong>
        </div>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarHeader">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
              <a href="index.php" class="nav-link  btn rounded-pill">Inicio</a>
          </li>
          <li class="nav-item">
              <a href="#" class="nav-link active btn rounded-pill">Productos</a>
          </li>
          <li class="nav-item">
              <a href="#" class="nav-link btn rounded-pill">Nosotros</a>
          </li>
          <li class="nav-item">
              <a href="#" class="nav-link btn rounded-pill ">Servicios</a>
          </li>
          <li class="nav-item">
              <a href="#" class="nav-link btn rounded-pill">Tiendas</a>
          </li>
        </ul>
        
         <a href="checkout.php" class="btn bg-transparent border border-light position-relative">
          <i class="bi bi-bag "></i><span id="num_cart" class="bagde bg-secundary"><?php echo $num_cart?></span>
        </a>
        
      </div>



    </div>
  </div>
</header>

<main>
    <div class="container">
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3" id="productos_a_mostrar">
        <?php foreach($resultado as $row) {?>
        <div class="col ">
          <div class="card shadow-sm ">
            <img src="Img/productos/<?php echo $row['imagen']?>.jpg" class="card-img-top img-fluid" alt="Imagen responsiva">
            <div class="card-body"> 
              <h5 class="card-title"><?php echo $row['nombre']?> </h5>
              <p class="card-text">$<?php echo number_format($row['precio'], 3, '.',',');?></p>
              <div class="d-flex justify-content-between align-items-center">
                <div class="btn-group">
                <a href="detalles.php?id=<?php echo $row['id']; ?>&token=<?php echo
                hash_hmac('sha1',$row['id'],KEY_TOKEN); ?>" class="btn rounded-pill detalles">Detalles</a>
                </div>
                <button class="btn rounded-pill agregar" id="agregarCarrito" type="button" onclick="addProducto(<?php 
                    echo $row['id']; ?>, '<?php echo hash_hmac('sha1',$row['id'],KEY_TOKEN); ?>')">Agregar</button>                         
              </div>          
            </div>
          </div>         
        </div>  
        <?php } ?>
      </div>
    </div>

</main>




<script>

    function addProducto(id,token){
        let url = 'clases/carrito.php'
        let formData = new FormData()
        formData.append('id',id)
        formData.append('token',token)

        fetch(url, {
            method: 'POST',
            body: formData,
            mode: 'cors'
        }).then(response => response.json())
        .then(data =>{
            if(data.ok){
                let elemento = document.getElementById("num_cart")
                elemento.innerHTML = data.numero
            }
        })
    }

</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="Util/js/jquery.min.js"></script>
<script src="Util/js/jquery.validate.min.js"></script>

</body>

</html>