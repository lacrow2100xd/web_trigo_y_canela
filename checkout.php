<?php
require_once 'config/config.php';
require_once 'config/database.php';
$db = new Database();
$con = $db->conectar();


$productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;

$lista_carrito = array();

if($productos != null){
    foreach($productos as $clave => $producto){

        $sql = $con->prepare("SELECT id, nombre, imagen, precio, descuento, $producto AS cantidad FROM productos WHERE 
        id=? AND activo=1");
        $sql->execute([$clave]);
        $lista_carrito[] = $sql->fetch(PDO::FETCH_ASSOC);
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trigo y canela</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="css/checkout.css">
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
              <a href="productos.php" class="nav-link active btn rounded-pill">Productos</a>
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
        
        <a href="checkout.php" class="btn bg-transparent border border-light position-relative me-2">
          <i class="bi bi-bag "></i><span id="num_cart" class="bagde bg-secundary"><?php echo $num_cart?></span>
        </a>

        <?php if(isset($_SESSION['user_id'])){ ?> 
          <div class="dropdown">
            <button class="btn rounded-pill inicioSesion dropdown-toggle" type="button" id="btn_session" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fa-solid fa-user"></i> 
              <?php echo $_SESSION['user_name']; ?></a>
            </button>
            <div class="dropdown-menu" aria-labelledby="btn_session">
              <a class="dropdown-item" href="logout.php">Cerrar sesión</a>
              <a class="dropdown-item" href="compras.php">Mis compras</a>
            </div>
          </div>
         
        <?php } else { ?>
          <a href="login.php" class="btn rounded-pill inicioSesion"> Iniciar sesión</a>
        <?php } ?>
         
        
      </div>



    </div>
  </div>
</header>

<main>
    <div class="container">
        <div class="table-responsive">
            <table class="table table-hover">
                <tr>
                    <th>Producto</th>  
                    <th>Precio</th> 
                    <th>Cantidad</th> 
                    <th>Subtotal</th> 
                    <th></th> 
                </tr>
            
            <tbody>
                <?php if($lista_carrito == null){
                    echo '<tr><td class="colspan="5" class="text-center"> <b> Lista vacia</b></td></tr>';
                }else{
                    $total = 0;
                    foreach($lista_carrito as $producto){
                        $_id = $producto['id'];
                        $nombre = $producto['nombre'];
                        $precio = $producto['precio'];
                        $descuento = $producto['descuento'];
                        $cantidad = $producto['cantidad'];
                        $precio_desc = $precio - (($precio * $descuento)/100);
                        $subtotal = $cantidad * $precio_desc;
                        $total += $subtotal;
                        ?>
               
                <tr>
                    <td><?php echo $nombre; ?></td>
                    <td><?php echo MONEDA . number_format($precio_desc, 0, '.', ','); ?></td>
                    <td>
                        <input type="number" min="1" max="10" step="1" value="<?php echo $cantidad ?>"
                        size="5" id="cantidad_<?php echo $_id;?>" onchange="actualizaCantidad(this.value,<?php echo $_id; ?>)">
                    </td>
                    <td>
                        <div id="subtotal_<?php echo $_id; ?>" name="subtotal[]"><?php echo MONEDA . 
                        number_format($subtotal, 0, '.', ','); ?> </div>
                    </td>
                    <td><a href="#" id="eliminar" class="btn btn-warning btn-sm" data-bs-id="<?php echo
                    $_id; ?>" data-bs-toggle="modal" data-bs-target="#eliminaModal"><i class="fa-solid fa-trash-can"></i></a></td>
                    
                </tr>
                <?php } ?>

                <tr>
                    <td colspan="3"></td>
                    <td colspan="2">
                        <p class="h4" id="total">
                            <?php echo MONEDA . number_format($total, 0, '.', ','); ?>
                        </p>
                    </td>
                </tr>

            </tbody>

            <?php } ?>
            </table> 
        </div>
        <?php if($lista_carrito != null){ ?>
        <div class="row">
            <div class="col-md-5 offset-md-7 d-grid gap-2">
                <?php 
                if(isset($_SESSION['user_cliente'])){?>
                <a href="pago.php" class="btn btn-lg">Realizar pago</a>
                <?php } else { ?>
                    <a href="login.php?pago" class="btn btn-lg">Realizar pago</a>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
      
    </div>

</main>


<div class="modal fade" id="eliminaModal" tabindex="-1" role="dialog" aria-labelledby="eliminaModallLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="eliminaModallLabel">Alerta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         
        </button>
      </div>
      <div class="modal-body">
         ¿Desea eliminar el producto de la lista?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button id="btn-elimina" type="button" class="btn btn-danger" onclick="eliminar()">Eliminar</button>
      </div>
    </div>
  </div>
</div>




<script>

    let eliminaModal = document.getElementById('eliminaModal')
    eliminaModal.addEventListener('show.bs.modal', function(event){
        let button = event.relatedTarget
        let id = button.getAttribute('data-bs-id')
        let buttonElimina = eliminaModal.querySelector('.modal-footer #btn-elimina')
        buttonElimina.value = id
    })

    function actualizaCantidad(cantidad,id){
        let url = 'clases/actualizar_carrito.php'
        let formData = new FormData()
        formData.append('action','agregar')
        formData.append('id',id)
        formData.append('cantidad',cantidad)

        fetch(url, {
            method: 'POST',
            body: formData,
            mode: 'cors'
        }).then(response => response.json())
        .then(data =>{
            if(data.ok){
                let divsubtotal = document.getElementById('subtotal_' + id)
                divsubtotal.innerHTML = data.sub

                let total = 0.000
                let list = document.getElementsByName('subtotal[]')

                for(let i = 0; i < list.length; i++){
                    total += parseFloat(list[i].innerHTML.replace(/[$,]/g, ''))
                }

                total = new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: 0
                }).format(total)
                document.getElementById('total').innerHTML = '<?php echo MONEDA; ?>' + total
            }else{
                let inputCantidad = document.getElementById('cantidad_' + id)
                inputCantidad.value = data.cantidadAnterior
                toastr.error('No hay existencias disponibles');
            }
        })
    }

    function eliminar(){

        let botonElimina = document.getElementById('btn-elimina')
        let id = botonElimina.value

        let url = 'clases/actualizar_carrito.php'
        let formData = new FormData()
        formData.append('action','eliminar')
        formData.append('id',id)

        fetch(url, {
            method: 'POST',
            body: formData,
            mode: 'cors'
        }).then(response => response.json())
        .then(data =>{
            if(data.ok){
                location.reload()
            }
        })
    }

</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>



<!-- Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>
