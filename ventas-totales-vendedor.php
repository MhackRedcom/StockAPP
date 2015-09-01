<?php session_start();
include ('sistema/configuracion.php');
$usuario->LoginCuentaConsulta();
$usuario->VerificacionCuenta();
$fecha	= FechaActual();

?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<title><?php echo TITULO ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<link rel="shortcut icon" href="<?php echo ESTATICO ?>tema/<?php echo TEMA ?>/img/favicon.ico">
	<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/plug-ins/1.10.7/integration/bootstrap/3/dataTables.bootstrap.css">
	<?php include(MODULO.'Tema.CSS.php');?>
</head>
<body>
	<?php
	// Menu inicio
	if($usuarioApp['id_perfil']==2){
		include (MODULO.'menu_vendedor.php');
	}elseif($usuarioApp['id_perfil']==1){
		include (MODULO.'menu_admin.php');
	}else{
		echo'<meta http-equiv="refresh" content="0;url='.URLBASE.'cerrar-sesion"/>';
	}
	//Menu Fin
	?>
    <div class="container">

		<div class="page-header" id="banner">
			<div class="row">
				<div class="col-lg-8 col-md-7 col-sm-6">
					<h1>Registro de Ventas</h1>
				</div>
			</div>
		</div>
		<?php include (MODULO.'contador.php'); ?>
		<?php
		if(isset($_POST['CancelarFactura'])){
			$Idfactura = $_POST['Idfactura'];
			$actulizarFactura = $db->Conectar()->query("UPDATE `factura` SET `habilitado` = '0' WHERE `id` = '{$Idfactura}'");
			$actulizarNumeros = $db->Conectar()->query("UPDATE `ventas` SET `habilitada` = '0' WHERE `idfactura` = '{$Idfactura}'");
			if($actulizarFactura == true AND $actulizarNumeros==true){
				echo'
				<div class="alert alert-dismissible alert-success">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>&iexcl;Bien hecho!</strong> La Factura ha sido cancelada con exito.
				</div>
				<meta http-equiv="refresh" content="2;url='.URLBASE.'ventas-totales-vendedor"/>';
			}else{
				echo'
				<div class="alert alert-dismissible alert-danger">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>&iexcl;Lo Sentimos!</strong> A ocurrido un error al cancelar la factura, intentalo de nuevo.
				</div>
				<meta http-equiv="refresh" content="2;url='.URLBASE.'ventas-totales-vendedor"/>';
			}
		}
		?>
		<div class="row">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#dia" data-toggle="tab">Facturas D&iacute;a</a></li>
				<li><a href="#noche" data-toggle="tab">Facturas Noche</a></li>
			</ul>
			<div id="myTabContent" class="tab-content">
				<div class="tab-pane fade active in" id="dia">
					<div class="col-sm-12">
						<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-condensed" id="ventasnoche" data-sort-name="id" data-sort-order="desc">
							<thead>
								<tr>
									<td><strong>Id Factura</strong></td>
									<td><strong>Total</strong></td>
									<td><strong>Fecha</strong></td>
									<td><strong>Estado</strong></td>
									<td><strong>Comprobante</strong></td>
								</tr>
							</thead>
							<tbody>
							<?php
							$cajatmpSql = $db->Conectar()->query("SELECT * FROM factura WHERE id AND usuario='{$usuarioApp['id']}' AND tipo='0' AND fecha='{$fecha}' ORDER BY id ASC");
							while($cajatmp	= $cajatmpSql->fetch_array()){
							?>
							<tr>
								<td data-sort-order="desc"> <?php echo $cajatmp['id']; ?></td>
								<td data-name="price" data-pk="undefined" data-value="&cent;0">&cent;<?php echo $cajatmp['total']; ?></td>
								<td> <?php echo $cajatmp['fecha'].' '.$cajatmp['hora']; ?></td>
								<td> <?php if($cajatmp['habilitado'] == 1){
									echo'<span class="label label-success">Activa</span>';
								}else{
									echo'<span class="label label-danger">Cancelada</span>';
								}?>
								</td>
								<td>
								<a href="<?php echo URLBASE ?>reimprimir/<?php echo $cajatmp['id']; ?>" class="btn btn-primary btn-sm">Ver venta</a>
								<?php
								if($cajatmp['habilitado']==1){
								?>
								<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#CancelarApuesta<?php echo $cajatmp['id']; ?>" <?php DesabilitarVentaNoche(); ?>>Cancelar Factura</button>
								<!-- Modal -->
								<div class="modal fade" id="CancelarApuesta<?php echo $cajatmp['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								  <div class="modal-dialog">
									<div class="modal-content">
									  <div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title" id="myModalLabel">Cancelar Factura</h4>
									  </div>
									  <div class="modal-body">
										<form class="form-horizontal" method="post" action="">
											<input type="hidden" name="Idfactura" value="<?php echo $cajatmp['id']; ?>">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="input-group">
														¿Est&aacute; seguro que desea cancelar la factura #<?php echo $cajatmp['id']; ?>?
													</div>
												</div>
											</div>
											<div class="form-group">
												<div class="col-sm-12">
												   <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<button type="submit" name="CancelarFactura" class="btn btn-primary">Si, Cancelar</button>
												</div>
											</div>
										</form>
									  </div>
									</div>
								  </div>
								</div>
								<!-- Modal Final -->
								<?php
								}else{
								?>
								<button type="button" class="btn btn-primary btn-sm disabled" data-toggle="modal">Factura Cancelada</button>
								<?php
								}
								?>
								</td>
							</tr>
							<?php
							}
							?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="tab-pane fade" id="noche">
					<div class="col-sm-12">
						<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-condensed" id="ventasdia" data-sort-name="id" data-sort-order="desc">
							<thead>
								<tr>
									<td><strong>Id Factura</strong></td>
									<td><strong>Total</strong></td>
									<td><strong>Fecha</strong></td>
									<td><strong>Estado</strong></td>
									<td><strong>Comprobante</strong></td>
								</tr>
							</thead>
							<tbody>
							<?php
							$cajatmpSql = $db->Conectar()->query("SELECT * FROM factura WHERE id AND usuario='{$usuarioApp['id']}' AND tipo='1' AND fecha='{$fecha}' ORDER BY id ASC");
							while($cajatmp	= $cajatmpSql->fetch_array()){
							?>
							<tr>
								<td data-sort-order="desc"> <?php echo $cajatmp['id']; ?></td>
								<td data-name="price" data-pk="undefined" data-value="&cent;0">&cent;<?php echo $cajatmp['total']; ?></td>
								<td> <?php echo $cajatmp['fecha'].' '.$cajatmp['hora']; ?></td>
								<td> <?php if($cajatmp['habilitado'] == 1){
									echo'<span class="label label-success">Activa</span>';
								}else{
									echo'<span class="label label-danger">Cancelada</span>';
								}?>
								</td>
								<td>
								<a href="<?php echo URLBASE ?>reimprimir/<?php echo $cajatmp['id']; ?>" class="btn btn-primary btn-sm">Ver venta</a>
								<?php
								if($cajatmp['habilitado']==1){
								?>
								<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#CancelarApuesta<?php echo $cajatmp['id']; ?>" <?php DesabilitarVentaNoche(); ?>>Cancelar Factura</button>
								<!-- Modal -->
								<div class="modal fade" id="CancelarApuesta<?php echo $cajatmp['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								  <div class="modal-dialog">
									<div class="modal-content">
									  <div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title" id="myModalLabel">Cancelar Factura</h4>
									  </div>
									  <div class="modal-body">
										<form class="form-horizontal" method="post" action="">
											<input type="hidden" name="Idfactura" value="<?php echo $cajatmp['id']; ?>">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="input-group">
														¿Est&aacute; seguro que desea cancelar la factura #<?php echo $cajatmp['id']; ?>?
													</div>
												</div>
											</div>
											<div class="form-group">
												<div class="col-sm-12">
												   <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<button type="submit" name="CancelarFactura" class="btn btn-primary">Si, Cancelar</button>
												</div>
											</div>
										</form>
									  </div>
									</div>
								  </div>
								</div>
								<!-- Modal Final -->
								<?php
								}else{
								?>
								<button type="button" class="btn btn-primary btn-sm disabled" data-toggle="modal">Factura Cancelada</button>
								<?php
								}
								?>
								</td>
							</tr>
							<?php
							}
							?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<?php include (MODULO.'footer.php'); ?>
    </div>
	<!-- Cargado archivos javascript al final para que la pagina cargue mas rapido -->
	<?php include(MODULO.'Tema.JS.php');?>
	<script type="text/javascript" language="javascript" src="<?php echo ESTATICO ?>tema/<?php echo TEMA ?>/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="<?php echo ESTATICO ?>tema/<?php echo TEMA ?>/js/dataTables.bootstrap.js"></script>
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {
			$('#ventasdia').dataTable({"order":[0, 'desc']});
		} );
		$(document).ready(function() {
			$('#ventasnoche').dataTable({"order":[0, 'desc']});
		} );
	</script>
	<!-- Cargado archivos javascript al final para que la pagina cargue mas rapido Fin -->
</body>
</html>