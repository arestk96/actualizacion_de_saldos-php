//funcion para obtener inpc historico
	function obtener_inpc_hist( $fecharq,$serie,$token,$url)
	{
	  $datos_banxico_hist = json_decode(file_get_contents($url."/".$serie."/datos/".$fecharq."/".$fecharq."?token=".$token), true );

	  foreach ($datos_banxico_hist as $bmx_hist => $series_hist)
	  {
		foreach ($series_hist as $serie_hist => $serieId_hist)
		{
		  foreach ($serieId_hist as $serieDatos_hist)
		  {
			echo "<br> <b>ID SERIE: </b>".$serieDatos_hist['idSerie']. ', <b><br>TITULO: </b>'. $serieDatos_hist['titulo'].'<br>';
			foreach ($serieDatos_hist['datos'] as $datoINPC_hist)
			{
			  $inpc_hist_array = array('fecha' => $datoINPC_hist['fecha'], 'inpc' => $datoINPC_hist['dato']);
			}
		  }
		}
	  }
	  //echo "<b>fecha historico: </b>$datoINPC_hist[fecha] <br> <b>dato historico: </b>$datoINPC_hist[inpc]";
	  return $inpc_hist_array;
	}

	//funcion para obtener actual/ oportuno
	function obtener_inpc_opo($serie,$token,$url)
	{
		$datos_banxico_opo = json_decode(file_get_contents($url."/".$serie."/datos/oportuno?token=".$token), true );

		foreach ($datos_banxico_opo as $bmx_hist => $series_opo)
		{
		foreach ($series_opo as $serie_opo => $serieId_opo)
		{
			foreach ($serieId_opo as $serieDatos_opo)
			{
			foreach ($serieDatos_opo['datos'] as $datoINPC_opo)
			{
				$inpc_opo_array = array('fecha' => $datoINPC_opo['fecha'], 'inpc' => $datoINPC_opo['dato']);
			}
			}
		}
		}
		return $inpc_opo_array;
	}

	//Seccion boton para generar Req
		function generarRequerimiento(){
		wp_reset_query();
		$serie = 'SP1';
		$token = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
		$url = 'https://www.banxico.org.mx/SieAPIRest/service/v1/series';

		//  $valueRq = $_POST['selectColoniarq'];
			// if(isset($_POST['submit'])){
			// 	if ($_POST["dropColonia"]) {

			//Muestra publicaciones del tipo 'my_custom_post_type', ordenadas por 'ptb_predio_notifica_colonia',
			//y filtradas para mostrar solo la Colonia 01 (usando meta_query).
			$args = array(
				'post_type' => 'predio_test',
				'meta_key' => 'ptb_predio_test_expediente',
				'order'	=> 'ASC',
				'posts_per_page ' => 400
 				// 'meta_query' => array(
				// 	array(
				// 		'key' => 'ptb_predio_notifica_colonia',
				// 		'value' => $valueRq,

				// 	),
				// ),
			);
			// $numero = 1;
			$loop = new WP_Query($args);  // Se genera un loop (ciclo) con los CPT que sean 'predio'
			if($loop->have_posts()) {
				while($loop->have_posts()) : $loop->the_post();
					$propiedad_ID = get_the_ID();
					$cuenta_predial = get_the_title();
					$propitario = get_post_meta( get_the_ID(), 'ptb_predio_propietario', TRUE );
					$calle = get_post_meta( get_the_ID(), 'ptb_predio_notifica_calle', TRUE );
					$numero = get_post_meta( get_the_ID(), 'ptb_predio_notifica_numero_exterior', TRUE );
					$coloniaD = get_post_meta( get_the_ID(), 'ptb_predio_notifica_colonia', TRUE );
					$estado = get_post_meta( get_the_ID(), 'ptb_predio_notifica_estado', TRUE );
					$rez = get_post_meta(get_the_ID(), 'ptb_predio_rezago', TRUE);
					//$rec = get_post_meta(get_the_ID(), 'ptb_predio_recargos', TRUE);
					$corr = get_post_meta(get_the_ID(), 'ptb_predio_test_corriente', TRUE);
					$bmd = get_post_meta(get_the_ID(), 'ptb_predio_bimestre_desde', TRUE);
					$bmh = get_post_meta(get_the_ID(), 'ptb_predio_bimestre_hasta', TRUE);
					$fecharq = get_post_meta(get_the_ID(), 'ptb_predio_test_fecha_adeudo', TRUE);
					$numExp = get_post_meta(get_the_ID(), 'ptb_predio_test_expediente', TRUE);


					$rec = $rez * 0.014 * 5;

					$adeudo = $rez + $rec + $corr;
					$porcentaje = 0.02;
					$calculo = $adeudo * $porcentaje;
					if ($calculo < 173.76)
					{
						$h_cob = 173.76;
					}else { $h_cob = $calculo;}
					$g_dis = 0.0;
					$h_val = 0.0;
					/******************actualizacion*****************/
					//$valores_hist = obtener_inpc_hist($fecharq,$serie,$token,$url);
					//$valores_opo = obtener_inpc_opo($serie,$token,$url);
					$diferencia = 1.2411; //round($valores_opo[inpc],2) / round($valores_hist[inpc],2);
					$actualizacion = $adeudo * round($diferencia,2);
					/***********fin actualizacion******************/
					$adeudo2 = $adeudo + $actualizacion + $h_cob;
						// $numExp = $numero.'/2020';
						date_default_timezone_set('America/Mexico_City');
						$hoy = date('Y') . '-' . date('m') . '-' . date('d');
					$dir = array($calle,$numero,$estado);
					$id = wp_insert_post(array(
						'post_title'=>$cuenta_predial,
						'post_status'=>'publish',
						'post_author'=>1,
						'posts_per_page ' => 400,
						'post_type'=>'requerimiento'));
						add_post_meta($id,'ptb_requerimiento_relacion_a_predio', $propiedad_ID);
						add_post_meta($id,'ptb_requerimiento_contibuyente', $propitario);
						add_post_meta($id,'ptb_requerimiento_direccion', $dir);
						add_post_meta($id,'ptb_requerimiento_rezago', $rez);
						add_post_meta($id,'ptb_requerimiento_recargos', $rec);
						add_post_meta($id,'ptb_requerimiento_bimestre_desde', $bmd);
						add_post_meta($id,'ptb_requerimiento_bimestre_hasta', $bmh);
						add_post_meta($id,'ptb_requerimiento_actualizacion', round($actualizacion,2));
						add_post_meta($id,'ptb_requerimiento_corriente', $corr);
						add_post_meta($id,'ptb_requerimiento_adeudo', round($adeudo2,2));
						add_post_meta($id,'ptb_requerimiento_g_distancia', $g_dis);
						add_post_meta($id,'ptb_requerimiento_h_cobranza', $h_cob);
						add_post_meta($id,'ptb_requerimiento_h_valuacion', $h_val);
						add_post_meta($id,'ptb_requerimiento_exprediente', $numExp);
						add_post_meta($id,'ptb_requerimiento_fecha_entregado', $hoy);

						// $numero++;

				endwhile;
			}
		}
		//hook de scripts
		// add_shortcode('print','generarRequerimiento');
		add_action( 'wp_enqueue_scripts', 'ajax_pruebamerq' );

		function ajax_pruebamerq() {
		  wp_enqueue_script( 'testrq', get_stylesheet_directory_uri().'/prueba2.js', array('jquery'),'1.0',true);
		}
		//hook de action/listener
		add_action('wp_ajax_nopriv_pruebamerq', 'generarRequerimiento'); //funcion para cuando no ocupas estar logueado
		add_action('wp_ajax_pruebamerq', 'generarRequerimiento');

		// add_action('wp_ajax_btnrequerimiento', 'generarRequerimiento');
		// add_action('wp_ajax_nopriv_btnrequerimiento', 'generarRequerimiento');
		/**************************frin Rq******************************************/
