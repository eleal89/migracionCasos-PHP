<?

	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers:Content-Type, X-PINGOTHER, X-File-Name, Cache-Control');
	header('Access-Control-Allow-Methods: POST');
	header('Access-Control-Allow-Credentials: true');
	header('Content-Type: application/json');
	ini_set('display_errors', 'Off');
	ini_set('display_errors',0);

	require_once __DIR__ . '/../api_bpm/vendor/autoload.php';
	$dotenv = new Dotenv\Dotenv(__DIR__ . '/../api_bpm');
	$dotenv->load();

	include("../app/cn/cn.php");

	        $TIPO_SOLICITUD      = $_POST['TIPO_SOLICITUD'];               
            $CANAL_INGRESO          = $_POST['CANAL_INGRESO']; 

            $SOL_TIPO_PER    = $_POST['SOL_TIPO_PER'];               
            $SOL_DOC             = $_POST['SOL_DOC'];                            
            $SOL_TIPO_DOC  = $_POST['SOL_TIPO_DOC'];                 
            $SOL_TIPO_PER_PJ = $_POST['SOL_TIPO_PER_PJ'];            
            $SOL_TIPO_DOC_PJ = $_POST['SOL_TIPO_DOC_PJ'];                                
            $SOL_RUC             = $_POST['SOL_RUC'];                
            $SOL_RSOCIAL   = $_POST['SOL_RSOCIAL'];            
        
            $SOL_NOMBRE          = $_POST['SOL_NOMBRE'];             
            $SOL_APE_PATERNO = $_POST['SOL_APE_PATERNO'];            
            $SOL_APE_MATERNO = $_POST['SOL_APE_MATERNO'];            
          
            $SOL_GENERO          = $_POST['SOL_GENERO'];             
        
            $SOL_FE_NACIMIENTO   = $_POST['SOL_FE_NACIMIENTO'];            
            $SOL_EDAD                  = $_POST['SOL_EDAD'];               
            $SOL_DIRECCION             = $_POST['SOL_DIRECCION'];                
            $SOL_DEPARTAMENTO    = $_POST['SOL_DEPARTAMENTO'];             
            $SOL_PROVINCIA             = $_POST['SOL_PROVINCIA'];                
            $SOL_DISTRITO        = $_POST['SOL_DISTRITO'];                 
            $SOL_TELEFONO        = $_POST['SOL_TELEFONO'];                 
            $SOL_CELULAR         = $_POST['SOL_CELULAR'];            
            $SOL_CORREO                = $_POST['SOL_CORREO'];             
            $MEDIO_RESPUESTA     = $_POST['MEDIO_RESPUESTA'];              
            $DIRECCION_NOTI      = $_POST['DIRECCION_NOTI'];               
            $DEPARTAMENTO_NOTI   = $_POST['DEPARTAMENTO_NOTI'];            
            $PROVINCIA_NOTI            = $_POST['PROVINCIA_NOTI'];               
            $DISTRITO_NOTI             = $_POST['DISTRITO_NOTI'];                
            $REFERENCIA_NOTI     = $_POST['REFERENCIA_NOTI'];              
                  
            $AFEC_TIPO_DOC             = $_POST['AFEC_TIPO_DOC'];                
            $AFEC_DOC                  = $_POST['AFEC_DOC'];               
            $AFEC_NOMBRE         = $_POST['AFEC_NOMBRE'];            
            $AFEC_APE_PATERNO    = $_POST['AFEC_APE_PATERNO'];             
            $AFEC_APE_MATERNO    = $_POST['AFEC_APE_MATERNO'];             
            
            $AFEC_EDAD                 = $_POST['AFEC_EDAD'];              
            $AFEC_GENERO         = $_POST['AFEC_GENERO'];            
            $AFEC_FE_NACIMIENTO  = $_POST['AFEC_FE_NACIMIENTO'];                 
            $AFEC_DIRECCION      = $_POST['AFEC_DIRECCION'];               
            $AFEC_DEPARTAMENTO   = $_POST['AFEC_DEPARTAMENTO'];            
            $AFEC_PROVINCIA            = $_POST['AFEC_PROVINCIA'];               
            $AFEC_DISTRITO             = $_POST['AFEC_DISTRITO'];                
            $AFEC_TELEFONO             = $_POST['AFEC_TELEFONO'];                
            $AFEC_CELULAR        = $_POST['AFEC_CELULAR'];                 
            $AFEC_CORREO         = $_POST['AFEC_CORREO'];            
            $AFEC_TIPO_SEGURO    = $_POST['AFEC_TIPO_SEGURO'];             
            $AFEC_HCL                  = $_POST['AFEC_HCL'];               
            $FE_OCURRENCIA             = $_POST['FE_OCURRENCIA'];                
            $DE_DETALLE                = $_POST['DE_DETALLE'];             
            $DE_SOLICITUD_USUARIO = $_POST['DE_SOLICITUD_USUARIO']; 

            $APP = $_POST['DE_APP']; 





	$queryTramite=" UPDATE BPM_TC_TRAMITE_PAC_REPORTE
						  SET 
							DE_TIPO_SOLICITUD      = '".$TIPO_SOLICITUD."',              
            				CO_TIPO_TRAMITE          = '".$CANAL_INGRESO."',
            				DE_TIPO_TRAMITE			= '".$CANAL_INGRESO_label."',
            				SOL_TIPO_PER    = '".$SOL_TIPO_PER."',              
            				SOL_DOC             = '".$SOL_DOC."',                           
            				DE_SOLICITANTE_TIPO_DOC  = '".$SOL_TIPO_DOC."',                
            				SOL_TIPO_PER_PJ = '".$SOL_TIPO_PER_PJ."',           
            				SOL_TIPO_DOC_PJ = '".$SOL_TIPO_DOC_PJ."',                               
            				DE_SOLICITANTE_RUC             = '".$SOL_RUC."',               
            				DE_SOLICITANTE_RSOCIAL   = '".$SOL_RSOCIAL."',                  
            				SOL_NOMBRE          = '".$SOL_NOMBRE."',            
            				SOL_APE_PATERNO = '".$SOL_APE_PATERNO."',           
            				SOL_APE_MATERNO = '".$SOL_APE_MATERNO."',                    
            				DE_SOLICITANTE_GENERO          = '".$SOL_GENERO."',                   
            				FE_SOLICITANTE_NAC   = '".$SOL_FE_NACIMIENTO."',           
            				DE_SOLICITANTE_EDAD                  = '".$SOL_EDAD."',              
            				DE_SOLICITANTE_DIRECCION             = '".$SOL_DIRECCION."',               
            				DE_SOLICITANTE_DEPARTAMENTO    = '".$SOL_DEPARTAMENTO."',            
            				DE_SOLICITANTE_PROVINCIA             = '".$SOL_PROVINCIA."',               
            				DE_SOLICITANTE_DISTRITO        = '".$SOL_DISTRITO."',                
            				DE_SOLICITANTE_TELEFONO        = '".$SOL_TELEFONO."',                
            				DE_SOLICITANTE_CELULAR         = '".$SOL_CELULAR."',           
            				DE_SOLICITANTE_CORREO                = '".$SOL_CORREO."',            
            				DE_MEDIO_RPTA     = '".$MEDIO_RESPUESTA."',             
            				DE_DIRECCION_NOTI      = '".$DIRECCION_NOTI."',              
            				DE_DEPARTAMENTO_NOTI   = '".$DEPARTAMENTO_NOTI."',           
            				DE_PROVINCIA_NOTI            = '".$PROVINCIA_NOTI."',              
            				DE_DISTRITO_NOTI             = '".$DISTRITO_NOTI."',               
            				REFERENCIA_NOTI     = '".$REFERENCIA_NOTI."',

           					AFEC_TIPO_DOC             = '".$AFEC_TIPO_DOC."',               
            				AFEC_DOC                  = '".$AFEC_DOC."',              
            				DE_AFECTADO         = '".$AFEC_NOMBRE."',           
            				AFEC_APE_PATERNO    = '".$AFEC_APE_PATERNO."',            
            				AFEC_APE_MATERNO    = '".$AFEC_APE_MATERNO."',                        
            				AFEC_EDAD                 = '".$AFEC_EDAD."',             
            				DE_AFECTADO_GENERO         = '".$AFEC_GENERO."',           
            				FE_AFECTADO_NAC  = '".$AFEC_FE_NACIMIENTO."',                
            				DE_AFECTADO_DIRECCION      = '".$AFEC_DIRECCION."',              
            				DE_AFECTADO_DEPARTAMENTO   = '".$AFEC_DEPARTAMENTO."',           
            				DE_AFECTADO_PROVINCIA            = '".$AFEC_PROVINCIA."',              
            				DE_AFECTADO_DISTRITO             = '".$AFEC_DISTRITO."',               
            				DE_AFECTADO_TELEFONO             = '".$AFEC_TELEFONO."',               
            				DE_AFECTADO_CELULAR        = '".$AFEC_CELULAR."',                
            				DE_AFECTADO_CORREO         = '".$AFEC_CORREO."',           
            				AFEC_TIPO_SEGURO    = '".$AFEC_TIPO_SEGURO."',            
            				AFEC_HCL                  = '".$AFEC_HCL."',              
            				FE_OCURRENCIA             = '".$FE_OCURRENCIA."',               
            				DE_DETALLE                = '".$DE_DETALLE."',            
            				DE_SOLICITUD_USUARIO   = '".$DE_SOLICITUD_USUARIO."', 
						  WHERE DE_APPLICATION = '".$APP."' ";
	$stid = oci_parse($conn,$queryTramite);
	$r = oci_execute($stid);

	if (!$r) {
		$ex = oci_error($stid);
		$rests = array("codigo"=>"0003","mensaje"=>"Error en la operación BPM_TC_TRAMITE_PAC_REPORTE");
		echo json_encode($rests);	  
	}
	else{
		$rests = array("codigo"=>"0000","mensaje"=>"Operación Existosa BPM_TC_TRAMITE_PAC_REPORTE");
		echo json_encode($rests);	  
	}


	$queryInst="  UPDATE BPM_PAC_INST_REPORTE
					  SET 
					  DE_APPLICATION = '".$APP_UIDN."',      
					  DE_NROCASO='".$APP_NUMBER."'     
					  WHERE DE_APPLICATION = '".$APP."' ";
		$stid = oci_parse($conn,$queryInst);
		$r = oci_execute($stid);    
		if (!$r) {
			$ex = oci_error($stid);
			$rests = array("codigo"=>"0003","mensaje"=>"Error en la operación BPM_PAC_INST_REPORTE");
			echo json_encode($rests);      
		}
		else{
			$rests = array("codigo"=>"0000","mensaje"=>"Operación Existosa BPM_PAC_INST_REPORTE");
			echo json_encode($rests);      
		}

		$queryHechos=" UPDATE BPM_PAC_HECHOS_REPORTE
						SET 
						DE_APPLICATION = '".$APP_UIDN."',      
						DE_NROCASO='".$APP_NUMBER."'      
						WHERE DE_APPLICATION = '".$APP."' ";
		$stid = oci_parse($conn,$queryHechos);
		$r = oci_execute($stid);

		if (!$r) {
			$ex = oci_error($stid);
			$rests = array("codigo"=>"0003","mensaje"=>"Error en la operación BPM_PAC_HECHOS_REPORTE");
			echo json_encode($rests);      
		}
		else{
			$rests = array("codigo"=>"0000","mensaje"=>"Operación Existosa BPM_PAC_HECHOS_REPORTE");
			echo json_encode($rests);      
		}

		$queryPobla="  UPDATE BPM_PAC_POBLACIONES_REPORTE
						SET 
						DE_APPLICATION = '".$APP_UIDN."',      
						DE_NROCASO='".$APP_NUMBER."'      
						WHERE DE_APPLICATION = '".$APP."' ";
		$stid = oci_parse($conn,$queryPobla);
		$r = oci_execute($stid);

		if (!$r) {
			$ex = oci_error($stid);
			$rests = array("codigo"=>"0003","mensaje"=>"Error en la operación BPM_PAC_POBLACIONES_REPORTE");
			echo json_encode($rests);      
		}
		else{
			$rests = array("codigo"=>"0000","mensaje"=>"Operación Existosa BPM_PAC_POBLACIONES_REPORTE");
			echo json_encode($rests);      
		}
	


?>