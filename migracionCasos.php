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
	global $mysqli_old;

	$SERVER_ORIGEN   =$_ENV['MYSQL_DB_HOST_OLD'];
	$SERVER_DESTINO  =$_ENV['SERVER'];

	//usuarios
	/*
	$CLIENTE_ID_NCUE	=$_ENV['CLIENTE_ID_NCUE'];
	$CLIENTE_SECRET_NCUE=$_ENV['CLIENTE_SECRET_NCUE'];
	$USER_NCUE			=$_ENV['USER_NCUE'];
	$PASS_NCUE			=$_ENV['PASS_NCUE'];
	*/

	$PROCESS_ORIGEN 		= $_POST['app'];
	$PROCESS_ORIGEN_NOMBRE	= $_POST['nom_proc'];
	$CANT_REGISTROS  	= $_POST['cantReg'];
	//$PROCESS_ORIGEN 	= '19507620058583ce85d8dd9088244228'; //PIN mULticanal
	//$PROCESS_ORIGEN 	='2028198245526c37cd8d3f8027808880';  //QUejas
	//$CANT_REGISTROS  	= 1;
	//$APPLICATION_ORIGEN = '';


//1.- SELECCIONAR LOS CASOS A MIGRAR
	$QUERY_CASOS = "SELECT  APP.APP_UID APP_UID, APP.PRO_UID, APP.APP_NUMBER CASO, APP.APP_CREATE_DATE,  APP.APP_STATUS ESTADO, APP.APP_DATA as DATOS, APP.APP_INIT_USER USUARIO_INI, APP.APP_CUR_USER USUARIO 
					FROM wf_workflow.APPLICATION APP 
					WHERE  app_status = 'to_do' AND PRO_UID='".$PROCESS_ORIGEN."' AND MIGRADO = 0 limit $CANT_REGISTROS ";   

	$resultCasos  = $mysqli_old->query($QUERY_CASOS);				  
	foreach ($resultCasos as $resCaso) {
		$CASO_NUEVO = array();
		if ($resCaso !=NULL) {
//2.- Obtener las variables del caso
			$data          = $resCaso['DATOS'];
			$data          = str_replace('\"', '"', $data);
			$data          = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $data);			 
			$data          = unserialize($data);    
 
			$CASO_NUEVO['APPLICATION_ORIGEN'] = $resCaso['APP_UID'];
			$CASO_NUEVO['NROCASO_ORIGEN'] 	= $resCaso['CASO'];
			$USURIOCASORIGEN=$resCaso['USUARIO']; 

			if (isset($data['txt_ficIntervencion'])) $CASO_NUEVO['DE_FICHA'] = ucwords(strtolower($data['txt_ficIntervencion']));     
			else $CASO_NUEVO['DE_FICHA'] ="";
			if (isset($data['txt_cod_std'])) $CASO_NUEVO['NRO_EXPEDIENTE'] = ucwords(strtolower($data['txt_cod_std']));     
			else $CASO_NUEVO['NRO_EXPEDIENTE'] ="";
			if (isset($data['txt_fecRegistro'])) {
				$FE_REGISTRO = ucwords(strtolower($data['txt_fecRegistro']));     
				$CASO_NUEVO['FE_REGISTRO'] = Date('Y-m-d H:i:s', strtotime($FE_REGISTRO));
			}else $CASO_NUEVO['FE_REGISTRO'] ="";
			if (isset($data['fec_Recepcion'])) $CASO_NUEVO['FE_RECEPCION'] = ucwords(strtolower($data['fec_Recepcion']));     
			else $CASO_NUEVO['FE_RECEPCION'] ="";
			if (isset($data['cbo_tiposolicitud'])) $CASO_NUEVO['TIPO_SOLICITUD'] = ucwords(strtolower($data['cbo_tiposolicitud']));     
			else $CASO_NUEVO['TIPO_SOLICITUD'] ="";
			if (isset($data['cbo_tipotramite'])) $CASO_NUEVO['CANAL_INGRESO'] = ucwords(strtolower($data['cbo_tipotramite']));     
			else $CASO_NUEVO['CANAL_INGRESO'] ="";
			//solicitante
			if (isset($data['cbo_tipoPersona'])) $CASO_NUEVO['SOL_TIPO_PER'] = ucwords(strtolower($data['cbo_tipoPersona']));     
			else $CASO_NUEVO['SOL_TIPO_PER'] ="";
			if($CASO_NUEVO['SOL_TIPO_PER'] == 1){//p. natural
				if (isset($data['cbo_tipoDocumento'])) $CASO_NUEVO['SOL_TIPO_DOC'] = ucwords(strtolower($data['cbo_tipoDocumento']));     
				else $CASO_NUEVO['SOL_TIPO_DOC'] ="";
				if($CASO_NUEVO['SOL_TIPO_DOC'] == 1){ //DNI
					if (isset($data['txt_numDocumento'])) $CASO_NUEVO['SOL_DOC'] = ucwords(strtolower($data['txt_numDocumento']));     
					else $CASO_NUEVO['SOL_DOC'] ="";
				} else {	
					if (isset($data['txt_NumDcto'])) $CASO_NUEVO['SOL_DOC'] = ucwords(strtolower($data['txt_NumDcto']));     
					else $CASO_NUEVO['SOL_DOC'] ="";
				}
				if (isset($data['txt_Nombres'])) $CASO_NUEVO['SOL_NOMBRE'] =ucwords(strtolower($data['txt_Nombres']));   
				else $CASO_NUEVO['SOL_NOMBRE'] ="";
				if (isset($data['txt_Apellidos'])) $CASO_NUEVO['SOL_APE_PATERNO'] =ucwords(strtolower($data['txt_Apellidos']));   
				else $CASO_NUEVO['SOL_APE_PATERNO'] ="";
				if (isset($data['txt_ApeMaterno'])) $CASO_NUEVO['SOL_APE_MATERNO'] =ucwords(strtolower($data['txt_ApeMaterno']));   
				else $CASO_NUEVO['SOL_APE_MATERNO'] ="";
				if (isset($data['txt_Edad'])) $CASO_NUEVO['SOL_EDAD'] =ucwords(strtolower($data['txt_Edad']));   
				else $CASO_NUEVO['SOL_EDAD'] ="";
				if (isset($data['fec_Nacimiento'])) $CASO_NUEVO['SOL_FE_NACIMIENTO'] =ucwords(strtolower($data['fec_Nacimiento']));   
				else $CASO_NUEVO['SOL_FE_NACIMIENTO'] ="";
				if (isset($data['cbo_Sexo'])) $CASO_NUEVO['SOL_GENERO'] =ucwords(strtolower($data['cbo_Sexo']));   
				else $CASO_NUEVO['SOL_GENERO'] ="";
			} else if($CASO_NUEVO['SOL_TIPO_PER'] == 2){ //p. juridica
				if (isset($data['cbo_TipoDocumento1'])) $CASO_NUEVO['SOL_TIPO_DOC_PJ'] = ucwords(strtolower($data['cbo_TipoDocumento1']));     
				else $CASO_NUEVO['SOL_TIPO_DOC_PJ'] ="";
				if (isset($data['txt_NumRUC'])) $CASO_NUEVO['SOL_RUC'] = ucwords(strtolower($data['txt_NumRUC']));     
				else $CASO_NUEVO['SOL_RUC'] ="";
				if (isset($data['txt_Solicitante'])) $CASO_NUEVO['SOL_RSOCIAL'] = ucwords(strtolower($data['txt_Solicitante']));     
				else $CASO_NUEVO['SOL_RSOCIAL'] ="";
			}
			if (isset($data['txt_Direccion'])) $CASO_NUEVO['SOL_DIRECCION'] = ucwords(strtolower($data['txt_Direccion']));     
			else $CASO_NUEVO['SOL_DIRECCION'] ="";
			if (isset($data['cbo_Departamento'])) $CASO_NUEVO['SOL_DEPARTAMENTO'] = ucwords(strtolower($data['cbo_Departamento']));     
			else $CASO_NUEVO['SOL_DEPARTAMENTO'] ="";
			if (isset($data['cbo_Provincia'])) $CASO_NUEVO['SOL_PROVINCIA'] = ucwords(strtolower($data['cbo_Provincia']));     
			else $CASO_NUEVO['SOL_PROVINCIA'] ="";
			if (isset($data['cbo_Distrito'])) $CASO_NUEVO['SOL_DISTRITO'] = ucwords(strtolower($data['cbo_Distrito']));     
			else $CASO_NUEVO['SOL_DISTRITO'] ="";
			if (isset($data['txt_direcAdicional'])) $CASO_NUEVO['DIRECCION_NOTI'] = ucwords(strtolower($data['txt_direcAdicional']));     
			else $CASO_NUEVO['DIRECCION_NOTI'] ="";
			if (isset($data['cbo_Departamento1'])) $CASO_NUEVO['DEPARTAMENTO_NOTI'] = ucwords(strtolower($data['cbo_Departamento1']));     
			else $CASO_NUEVO['DEPARTAMENTO_NOTI'] ="";
			if (isset($data['cbo_Provincia1'])) $CASO_NUEVO['PROVINCIA_NOTI'] = ucwords(strtolower($data['cbo_Provincia1']));     
			else $CASO_NUEVO['PROVINCIA_NOTI'] ="";
			if (isset($data['cbo_Distrito1'])) $CASO_NUEVO['DISTRITO_NOTI'] = ucwords(strtolower($data['cbo_Distrito1']));     
			else $CASO_NUEVO['DISTRITO_NOTI'] ="";
			if (isset($data['txt_correoElectronico'])) $CASO_NUEVO['SOL_CORREO'] = ucwords(strtolower($data['txt_correoElectronico']));     
			else $CASO_NUEVO['SOL_CORREO'] ="";
			if (isset($data['txt_Telefono'])) $CASO_NUEVO['SOL_TELEFONO'] = ucwords(strtolower($data['txt_Telefono']));     
			else $CASO_NUEVO['SOL_TELEFONO'] ="";
			if (isset($data['txt_Celular'])) $CASO_NUEVO['SOL_CELULAR'] = ucwords(strtolower($data['txt_Celular']));     
			else $CASO_NUEVO['SOL_CELULAR'] ="";
			if (isset($data['cbo_medioRpta'])) $CASO_NUEVO['MEDIO_RESPUESTA'] = ucwords(strtolower($data['cbo_medioRpta']));     
			else $CASO_NUEVO['MEDIO_RESPUESTA'] ="";
			//afectado			
			if (isset($data['cbo_tipoDocumento1'])) $CASO_NUEVO['AFEC_TIPO_DOC'] = ucwords(strtolower($data['cbo_tipoDocumento1']));     
			else $CASO_NUEVO['AFEC_TIPO_DOC'] ="";
			if($CASO_NUEVO['AFEC_TIPO_DOC'] == 1){ //DNI
				if (isset($data['txt_numDocumento1'])) $CASO_NUEVO['AFEC_DOC'] = ucwords(strtolower($data['txt_numDocumento1']));     
				else $CASO_NUEVO['AFEC_DOC'] ="";
			} else {	
				if (isset($data['txt_numDcto2'])) $CASO_NUEVO['AFEC_DOC'] = ucwords(strtolower($data['txt_numDcto2']));     
				else $CASO_NUEVO['AFEC_DOC'] ="";
			}
			if (isset($data['txt_Nombres1'])) $CASO_NUEVO['AFEC_NOMBRE'] = ucwords(strtolower($data['txt_Nombres1']));     
			else $CASO_NUEVO['AFEC_NOMBRE'] ="";
			if (isset($data['txt_Apellidos1'])) $CASO_NUEVO['AFEC_APE_PATERNO'] = ucwords(strtolower($data['txt_Apellidos1']));     
			else $CASO_NUEVO['AFEC_APE_PATERNO'] ="";
			if (isset($data['txt_ApeMaterno1'])) $CASO_NUEVO['AFEC_APE_MATERNO'] = ucwords(strtolower($data['txt_ApeMaterno1']));     
			else $CASO_NUEVO['AFEC_APE_MATERNO'] ="";
			if (isset($data['cbo_Sexo1'])) $CASO_NUEVO['AFEC_GENERO'] = ucwords(strtolower($data['cbo_Sexo1']));     
			else $CASO_NUEVO['AFEC_GENERO'] ="";
			if (isset($data['txt_Edad1'])) $CASO_NUEVO['AFEC_EDAD'] = ucwords(strtolower($data['txt_Edad1']));     
			else $CASO_NUEVO['AFEC_EDAD'] ="";
			if (isset($data['txt_Direccion2'])) $CASO_NUEVO['AFEC_DIRECCION'] = ucwords(strtolower($data['txt_Direccion2']));     
			else $CASO_NUEVO['AFEC_DIRECCION'] ="";
			if (isset($data['cbo_Departamento2'])) $CASO_NUEVO['AFEC_DEPARTAMENTO'] = ucwords(strtolower($data['cbo_Departamento2']));     
			else $CASO_NUEVO['AFEC_DEPARTAMENTO'] ="";
			if (isset($data['cbo_Provincia2'])) $CASO_NUEVO['AFEC_PROVINCIA'] = ucwords(strtolower($data['cbo_Provincia2']));     
			else $CASO_NUEVO['AFEC_PROVINCIA'] ="";
			if (isset($data['cbo_Distrito2'])) $CASO_NUEVO['AFEC_DISTRITO'] = ucwords(strtolower($data['cbo_Distrito2']));     
			else $CASO_NUEVO['AFEC_DISTRITO'] ="";
			if (isset($data['txt_hcl'])) $CASO_NUEVO['AFEC_HCL'] = ucwords(strtolower($data['txt_hcl']));     
			else $CASO_NUEVO['AFEC_HCL'] ="";
			if (isset($data['cbo_tipoSeguro'])) $CASO_NUEVO['AFEC_TIPO_SEGURO'] = ucwords(strtolower($data['cbo_tipoSeguro']));     
			else $CASO_NUEVO['AFEC_TIPO_SEGURO'] ="";
			if (isset($data['fec_Ocurrencia'])) $CASO_NUEVO['FE_OCURRENCIA'] = ucwords(strtolower($data['fec_Ocurrencia']));     
			else $CASO_NUEVO['FE_OCURRENCIA'] ="";
			//detalle			
			if (isset($data['txa_Descripcin'])) $CASO_NUEVO['DE_DETALLE'] = ucwords(strtolower($data['txa_Descripcin']));     
			else $CASO_NUEVO['DE_DETALLE'] ="";
			if (isset($data['txa_solicitudUsusario'])) $CASO_NUEVO['DE_SOLICITUD_USUARIO'] = ucwords(strtolower($data['txa_solicitudUsusario']));     
			else $CASO_NUEVO['DE_SOLICITUD_USUARIO'] ="";
			//Poblaciones vulnerables
			if(isset($data['chb_poblacionesVulnerables'])) $CASO_NUEVO['CHK_POBLACIONESVULNERABLES'] = $data['chb_poblacionesVulnerables']; 
			else $CASO_NUEVO['CHK_POBLACIONESVULNERABLES'] =""; 
			
			//instituciones
			$dataInst = array();
			if (isset($data['grid_Instituciones'])) { 
				if ($data['grid_Instituciones'] != "[]") {
				  $_nINST = count($data['grid_Instituciones']);
				  for ($i = 1; $i <= $_nINST; $i++) {
				    $CO_INST        = '';
				    if(isset($data['grid_Instituciones'][$i]['CO_INST'])){
				    $CO_INST    = $data['grid_Instituciones'][$i]['CO_INST'];}
				    else {$CO_INST="";}

				    $CO_INST_BD     = '';
				    if(isset($data['grid_Instituciones'][$i]['CO_INST_BD'])){
				    $CO_INST_BD    = $data['grid_Instituciones'][$i]['CO_INST_BD'];}
				    else {$CO_INST_BD="";}

				    $DE_NOMBRE = '';
				    if(isset($data['grid_Instituciones'][$i]['DE_NOMBRE'])){
				    $DE_NOMBRE    = $data['grid_Instituciones'][$i]['DE_NOMBRE'];}
				    else{$DE_NOMBRE="";}

				    $DE_UBIGEO = '';
				    if(isset($data['grid_Instituciones'][$i]['DE_UBIGEO'])){
				    $DE_UBIGEO    = $data['grid_Instituciones'][$i]['DE_UBIGEO'];}
				    else{$DE_UBIGEO="";}

				    $DE_TIPO    = '';
				    if(isset($data['grid_Instituciones'][$i]['DE_CLASIFICACION'])){
				    $DE_TIPO    = $data['grid_Instituciones'][$i]['DE_CLASIFICACION'];}
				    else {$DE_TIPO="";}

				    $DE_CLASIFICACION   = '';
				    if(isset($data['grid_Instituciones'][$i]['DE_NATURALEZA'])){
				    $DE_CLASIFICACION    = $data['grid_Instituciones'][$i]['DE_NATURALEZA'];}
				    else {$DE_CLASIFICACION="";}                   

				    $dataInst[$i] =  array(
				      'CO_INST_BD'        => $CO_INST_BD,
				      'DE_CODIGO'         => $CO_INST,
				      'DE_NOMBRE'         => $DE_NOMBRE,
				      'DE_UBIGEO'         => $DE_UBIGEO,
				      'DE_TIPO'           => $DE_TIPO,
				      'DE_NATURALEZA'     => $DE_NATURALEZA,
				      'DE_CLASIFICACION'  => $DE_CLASIFICACION 
				    );                     
				  }                    
				}  				
				$CASO_NUEVO['grid_institucionesInvolucradas'] =$dataInst;      

			}
       		//if(isset($data['dataInst'])) $CASO_NUEVO['grid_institucionesInvolucradas'] = $data['dataInst']; 
			//else $CASO_NUEVO['grid_institucionesInvolucradas'] =""; 
			
			//gestiones
			$dataGestiones = array();
			if (isset($data['grid_Gestiones'])) { 
				if ($data['grid_Gestiones'] != "[]") {
				  $_nGest = count($data['grid_Gestiones']);
				  for ($i = 1; $i <= $_nGest; $i++) {
				    $FEC_REGISTRO        = '';
				    if(isset($data['grid_Gestiones'][$i]['FEC_REGISTRO'])){
				      $FEC_REGISTRO    = $data['grid_Gestiones'][$i]['FEC_REGISTRO'];}
				    else {$FEC_REGISTRO="";}

				    $NOMUSER        = '';
				    if(isset($data['grid_Gestiones'][$i]['NOMUSER'])){
				      $NOMUSER    = $data['grid_Gestiones'][$i]['NOMUSER'];}
				    else {$NOMUSER="";} 

				    $TXA_RESPONSABLE        = '';
				    if(isset($data['grid_Gestiones'][$i]['TXA_RESPONSABLE'])){
				      $TXA_RESPONSABLE    = $data['grid_Gestiones'][$i]['TXA_RESPONSABLE'];}
				    else {$TXA_RESPONSABLE="";} 

				    $CBO_LUGARGST        = '';
				    if(isset($data['grid_Gestiones'][$i]['CBO_LUGARGST'])){
				      $CBO_LUGARGST    = $data['grid_Gestiones'][$i]['CBO_LUGARGST'];}
				    else {$CBO_LUGARGST="";} 

				    $CBO_LUGARGST_label        = '';
				    if(isset($data['grid_Gestiones'][$i]['CBO_LUGARGST_label'])){
				      if ($data['grid_Gestiones'][$i]['CBO_LUGARGST_label']=="Seleccione"){
				      $CBO_LUGARGST_label="";}
				      else{
				        $CBO_LUGARGST_label    = $data['grid_Gestiones'][$i]['CBO_LUGARGST_label'];
				      }
				    }
				    else {
				      $CBO_LUGARGST_label="";
				    }  
				  
				    $TXA_DESCRIPCION        = '';
				    if(isset($data['grid_Gestiones'][$i]['TXA_DESCRIPCION'])){
				      $TXA_DESCRIPCION    = $data['grid_Gestiones'][$i]['TXA_DESCRIPCION'];}
				    else {$TXA_DESCRIPCION="";}      

				    $dataGestiones[$i] = array(
				      'FE_REGISTRO_GESTION'     => $FEC_REGISTRO,
				      'CO_USUARIO'              => $NOMUSER,
				      'DE_USUARIO'              => $TXA_RESPONSABLE,
				      'CO_LUGAR'                => $CBO_LUGARGST,
				      'CO_LUGAR_GESTION'        => $CBO_LUGARGST_label,
				      'DE_DESCRIPCION'          => $TXA_DESCRIPCION             
				    );     
				  }                    
				}  
				$CASO_NUEVO['grid_gestiones'] = $dataGestiones;             
			}

			/*if(isset($data['dataGestiones'])) $CASO_NUEVO['grid_gestiones'] = $data['dataGestiones']; 
			else $CASO_NUEVO['grid_gestiones'] =""; */
			
			//hechos
			 $dataHechosVul =array();
			if (isset($data['grid_HechosVulneratorios1'])) {
				if ($data['grid_HechosVulneratorios1'] != "[]") {
				  $_nHV = count($data['grid_HechosVulneratorios1']);  
				  $numHechos = 0; 
				  for ($i = 1; $i <= $_nHV; $i++) {
				    $nivel1_label = "";
				    $nivel1 = "";
				    if(isset($data['grid_HechosVulneratorios1'][$i]['cbo_Clasificacion1_label'])){ 
				      $nivel1_label = $data['grid_HechosVulneratorios1'][$i]['cbo_Clasificacion1_label']; 
				      $nivel1 = $data['grid_HechosVulneratorios1'][$i]['cbo_Clasificacion1']; 
				    }

				    $nivel2_label = "";
				    $nivel2 = "";
				    if(isset($data['grid_HechosVulneratorios1'][$i]['cbo_hechoVulneratorio_label'])){ 
				      $nivel2_label = $data['grid_HechosVulneratorios1'][$i]['cbo_hechoVulneratorio_label']; 
				      $nivel2 = $data['grid_HechosVulneratorios1'][$i]['cbo_hechoVulneratorio']; 
				    }

				    $nivel3 = "";
				    $nivel3_label = "";
				    if(isset($data['grid_HechosVulneratorios1'][$i]['cbo_Descripcion_label'])){ 
				      $nivel3_label = $data['grid_HechosVulneratorios1'][$i]['cbo_Descripcion_label'];
				      $nivel3 = $data['grid_HechosVulneratorios1'][$i]['cbo_Descripcion'];
				    } 

				    $dataHechosVul[$i] = array(
				      'CO_NIVEL1'   => $nivel1, 
				      'DE_NIVEL1'   => $nivel1_label,
				      'CO_NIVEL2'   => $nivel2, 
				      'DE_NIVEL2'   => $nivel2_label,
				      'CO_NIVEL3'   => $nivel3, 
				      'DE_NIVEL3'   => $nivel3_label                           
				    );                    
				  }                                
				}
				$CASO_NUEVO['grid_hechosVulneratorios'] = $dataHechosVul;
			}
			/*if(isset($data['dataHechosVul'])) $CASO_NUEVO['grid_hechosVulneratorios'] = $data['dataHechosVul']; 
			else $CASO_NUEVO['grid_hechosVulneratorios'] =""; */

			//titulo caso
			
			if($data['cbo_tipoPersona'] == '1'){
			    $CASO_NUEVO['TITULO_CASO'] = $data['txt_ficIntervencion']." Sol. ".$data['txt_Apellidos'].' '.$data['txt_ApeMaterno'].' '.$data['txt_Nombres'];
			}else{
			    $CASO_NUEVO['TITULO_CASO'] = $data['txt_ficIntervencion']." Sol. ".$data['txt_Solicitante'].' - '.$data['txt_Apellidos'].' '.$data['txt_Nombres'];   
			} 
		}
	// aqui va para el poreach }
//3.- Crear el nuevo caso
	$clientId     = 'KKUMWXPRBOLHXAGEDOLWZNNQXMZATAJE';
	$clientSecret = '4148906335cdb3522c55569072819848';
	$username     = 'eleal';
	$password     = '$palomo2601'; 
	$oToken = pmRestLogin($clientId,$clientSecret,$username,$password);

	$pmServer = "http://192.168.50.53";                                                                                     
	$pmWorkspace = 'workflow';

	$aCaseVars=array($CASO_NUEVO);
	//$aCaseVars= $CASO_NUEVO;
	//consultar el usuario del proceso 2.5 y verificar que exista en el 3.2
  	
  	$QUERYUSER = "SELECT USR_UID AS USR_ID, USR_USERNAME AS USERNAME FROM USERS WHERE USR_UID='".$USURIOCASORIGEN."'";
	$resultUser  = $mysqli_old->query($QUERYUSER);
	$row = $resultUser->fetch_assoc();
	$UserN = $row['USERNAME'];

	$QUERYUSERN = "SELECT USR_UID AS USR_ID, USR_USERNAME AS USERNAME, USR_FIRSTNAME AS NOMBRE, USR_LASTNAME AS APELLIDO, USR_STATUS AS ESTATUS FROM USERS WHERE USR_USERNAME='".$UserN."' AND USR_STATUS ='ACTIVE'";
	$resultUserN  = $mysqli->query($QUERYUSERN);
	$row2 = $resultUserN->fetch_assoc();
	if ($row2 !=null){	
		$USERID = $row2['USR_ID'];
		$USEROLD = $row2['USERNAME'];
		$NOMBRE = $row2['NOMBRE'];
		$APELLIDO = $row2['APELLIDO'];
		$NOMBRE = $NOMBRE." ".$APELLIDO;
		
		$USUARIO_UID=$USERID;
		$USUARIO=$NOMBRE ;
	}
	else {
		$USUARIO = 'Usuario Migrador';
		$USUARIO_UID='6612660975d02b24f6bd703092871273';
	}
    print "USuario ".$USUARIO; 

	$aVars = array(
     'pro_uid'   => '3978577155cf6a916a98047021975914', //1285689605b8079da655d95096839847
     'tas_uid'   => '9137475095cf6a9436c3bc7072604846', //1286238335b8079dadcd6c5063467032
     'usr_uid'   => $USUARIO_UID,
     'variables' => $aCaseVars       
  	);
  	

  	$oRet = pmRestRequest('POST', '/api/1.0/workflow/cases/impersonate', $aVars);
	if ($oRet->status == 200) {
		$DE_FICHA = $CASO_NUEVO['DE_FICHA'];
		//$APP =$APP_UID;
		$APP = $CASO_NUEVO['APPLICATION_ORIGEN'];
		$TAREA_UID ='9137475095cf6a9436c3bc7072604846';
		$TAREA_DES ="Evaluar expediente";
		$APP_NUMBER=$oRet->response->app_number;           
		//$USUARIO = 'Usuario Migrador';
		$USUARIO=$USUARIO;
		$USUARIO_UID=$USUARIO_UID;
		//$USUARIO_UID='6612660975d02b24f6bd703092871273';
		$DE_PROCESO_UID=$PROCESS_ORIGEN;
		$DE_PROCESO=$PROCESS_ORIGEN_NOMBRE;
		print "New Case {$oRet->response->app_number} created.\n";
		$respuesta = array();
	 	$respuesta['CO_RESPUESTA'] = 1; 
	 	$respuesta['MENSAJE'] = 'Caso creado'; 
	 	echo json_encode($resultado); 
		$APP_UIDN=$oRet->response->app_uid;
		echo "app:  ".$APP;
		echo "   nuevo app:".$APP_UIDN;
		//Actualizar datos en oracle 
		$queryTramite=" UPDATE BPM_TC_TRAMITE_PAC_REPORTE
						  SET 
						  DE_APPLICATION = '".$APP_UIDN."',
						  DE_TAREA ='".$TAREA_DES."',
						  DE_NROCASO='".$APP_NUMBER."',
						  DE_USUARIO_ACTUAL='".$USUARIO."', 
						  DE_USUARIO_ACTUAL_UID='".$USUARIO_UID."',
						  DE_PROCESO_UID='".$DE_PROCESO_UID."',  
						  DE_PROCESO='".$DE_PROCESO."'
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

		$queryM=" UPDATE APPLICATION
		          SET MIGRADO = '1'
		          WHERE APP_UID = '".$APP."'";  

		$resultCasosQueryX=mysqli_query($mysqli_old,$queryM);
		print "query-->> ".$queryM;

		// 4.- Subir documentos y asignarlos al nuevo caso
		$URLDesarrollo 	= "http://192.168.50.47:80/sysworkflow/es/classic/services/wsdl2";

		$client = new SoapClient($URLDesarrollo);
		$userServerAntiguo='admin';
		$pasServerAntiguo ='$U$AlUd2018BPMPAC';
		$result_message='Error';
		$result_code='1';
		$result_doc='';

		if($userServerAntiguo!="" and $pasServerAntiguo!=""){
		//login
		    $params = array(array('userid'=>$userServerAntiguo, 'password'=>$pasServerAntiguo));
		    $result = $client->__SoapCall('login', $params);

		    $result_code = $result->status_code;
		    $result_message = $result->message;

		    if ($result->status_code == 0) {
		        $sessionId = $result->message;//Guardar el MENSAJE de la respuesta en la variable SessionID
		        //$caseId = '5153571075d35eb39ce20d5020520660';
		        $caseId = $resCaso['APP_UID'];
		        print "CAsoID ".$caseId;

				$sqlDoc = "select DISTINCT
						  APP_DOCUMENT.APP_DOC_UID AS APP,
						  CONTENT.CON_VALUE AS ARC,
						  CONCAT(USERS.USR_FIRSTNAME,' ',USERS.USR_LASTNAME) AS NOM,
						  APP_DOCUMENT.APP_DOC_CREATE_DATE AS FEC,APP_DOCUMENT.APP_DOC_TYPE,
						  CASE WHEN APP_DOCUMENT.APP_DOC_TYPE = 'INPUT' THEN 'Adjuntado' 
						  WHEN APP_DOCUMENT.APP_DOC_TYPE = 'ATTACHED' THEN 'Adjuntado' 
						  ELSE 'Generado' END AS TIPO_DOC
						  FROM
						  APP_DOCUMENT
						  INNER JOIN USERS ON (APP_DOCUMENT.USR_UID = USERS.USR_UID)
						  INNER JOIN CONTENT ON (APP_DOCUMENT.APP_DOC_UID = CONTENT.CON_ID)
						WHERE
						  CONTENT.CON_CATEGORY = 'APP_DOC_FILENAME' AND APP_DOCUMENT.APP_DOC_STATUS ='ACTIVE' AND  
						  (APP_DOCUMENT.APP_DOC_TYPE = 'ATTACHED' OR APP_DOCUMENT.APP_DOC_TYPE = 'INPUT' OR APP_DOCUMENT.APP_DOC_TYPE = 'OUTPUT') AND
						  APP_DOCUMENT.APP_UID IN ('".$caseId."') ORDER BY APP_DOCUMENT.APP_DOC_CREATE_DATE ASC";	    

			    $rests = array();
			    $result  = $mysqli_old->query($sqlDoc);				  

			    $doc_rest = array();

			    foreach ($result as $resDocu) {

			    	if ($resDocu['APP_DOC_TYPE'] == "OUTPUT") {
						$path = "http://192.168.50.47/sysworkflow/es/classic/cases/cases_ShowOutputDocument?a=" . $resDocu['APP'] . "&v=&ext=doc" ;
				    } else {
				    	$path = "http://192.168.50.47/sysworkflow/es/classic/cases/cases_ShowDocument?a=" . $resDocu['APP'] ;
					}
					$nombre = $resDocu['ARC'] ;

					// incrementamos el limite de ejecucion para este script
					ini_set('max_execution_time', 600);
					 
					// definimos la URL del archivo a descargar
					$ArchivoRemoto = $path;
					 
					// definimos el nombre de la copia local
					$pathNuevo = '/data/processmaker/workflow/public_html/webservices/ws_procesos/documentos_migrados/';
					$ArchivoLocal = $pathNuevo.$nombre;
					 
					// Leemos el archivo remoto
					$datos = file_get_contents($ArchivoRemoto)
					    or die("No se piede leer el archivo remoto");
					 
					// Escribimos los datos en el archivo local
					$restFile = file_put_contents($ArchivoLocal, $datos)
					    or die("No se puede escribir el archivo local");

					//validamos el resultado
					if ($restFile !== FALSE) {
						$restsx='entrooooooooooooooooooo';
						echo json_encode($restsx);	
						//cagar el archivo al nuevo caso
						$ch = curl_init($path); 	          
						$params = array (
									   'ATTACH_FILE'  => (phpversion() >= "5.5") ? new CurlFile($ArchivoLocal) : '@'.$ArchivoLocal,
									   //'ATTACH_FILE'  => '@'.$path,
									   'APPLICATION'  => $APP_UIDN,
									   'INDEX'        => 1,
									   'USR_UID'      => $USUARIO_UID,
									   'DOC_UID'      => '-1',
									   'APP_DOC_TYPE' => 'INPUT',
									   'TITLE'        => $nombre,
									   'COMMENT'      => 'migración de archivo 50.47');
						    
						curl_setopt($ch, CURLOPT_URL, 'http://192.168.50.53/sysworkflow/es/neoclassic/services/upload');
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_POST, 1);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $params); 
						echo curl_exec($ch);
					}			 
					// Mostramos un mensaje de confirmacion
					//echo "El archivo [$ArchivoRemoto] fue copiado a [$ArchivoLocal]";
				}
		    }
		    $result_final->path=$path;
		    //$result_final->contents=$contents;
		    $json = array("result"=>$result_final);
		    $json   = json_encode($json);
		    print_r($json);
		} else{    
		    header('HTTP/1.0 403 Forbidden');
		    die('Forbidden');
		} 
	}
	else { 	 
	 $respuesta = array();
	 $respuesta['CO_RESPUESTA'] = 0; 
	 $respuesta['MENSAJE'] = 'No se creo el caso'; 
	 echo json_encode($resultado); 
	}
} //nuevo ojo
	print "$APP_UID anterior ".$APP_UID;
	echo json_encode($oToken);




//Funciones para consumir los servicios d eprocessmaker	
function pmRestLogin($clientId, $clientSecret, $username, $password) { 
	$pmServer = "http://192.168.50.53";                                                         
	$pmWorkspace = 'workflow';   
	$postParams = array(
		'grant_type'    => 'password',
		'scope'         => '*',       
		'client_id'     => $clientId,
		'client_secret' => $clientSecret, 
		'username'      => $username,
		'password'      => $password 
	);
     
	$ch = curl_init("$pmServer/$pmWorkspace/oauth2/token");
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$oToken = json_decode(curl_exec($ch));
	$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ($httpStatus != 200) {
		print "Error in HTTP status code: $httpStatus\n" ;
		return null;
	}
	elseif (isset($oToken->error)) {
		print "Error logging into $pmServer:\n" .
		"Error:       {$oToken->error}\n" .
		"Description: {$oToken->error_description}\n";
	}
	else {    
		setcookie("access_token",  $oToken->access_token,  time() + 86400);
		setcookie("refresh_token", $oToken->refresh_token); //refresh token doesn't expire
		setcookie("client_id",     $clientId);
		setcookie("client_secret", $clientSecret);   
	} 
	return $oToken;    
}

                                                                 
function pmRestRequest($method, $endpoint, $aVars = null, $accessToken = null) {
$pmServer = "http://192.168.50.53";                                                                                     
$pmWorkspace = 'workflow';      

	if (empty($accessToken) and isset($_COOKIE['access_token']))
	  $accessToken = $_COOKIE['access_token'];

	if (empty($accessToken)) { 
		header("Location: loginForm.php"); 
		die();
	}

 
	if (!empty($endpoint) and $endpoint[0] != "/")
    	$endpoint = "/" . $endpoint;

	$ch = curl_init($pmServer . $endpoint);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . $accessToken));
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$method = strtoupper($method);

	switch ($method) {
	case "GET":
	  break;
	case "DELETE":
	  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	  break;
	case "PUT":
	  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	case "POST":
	  curl_setopt($ch, CURLOPT_POST, 1);
	  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($aVars));
	  break;
	default:
	   throw new Exception("Error: Invalid HTTP method '$method' $endpoint");
	   return null;
	}

	$oRet = new StdClass;
	$oRet->response = json_decode(curl_exec($ch));
	$oRet->status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	if ($oRet->status == 401) { 
		header("Location: loginForm.php"); 
		die();
	}
  	elseif ($oRet->status != 200 and $oRet->status != 201) { 
	    if ($oRet->response and isset($oRet->response->error)) {
	       print "Error in $pmServer:\nCode: {$oRet->response->error->code}\n" .
	             "Message: {$oRet->response->error->message}\n";
	    }
	    else {
	       print "Error: HTTP status code: $oRet->status\n";
	    }
  	}
 return $oRet;
}
			
/*
	  if (isset($data['txt_TipoDocumento1'])){
	    $txt_TipoDocumento1 =ucwords(strtolower($data['txt_TipoDocumento1']));   
	  }else{$txt_TipoDocumento1 ="";}
	  if (isset($data['NUMERO_PAC'])){
	    $NUMERO_PAC =ucwords(strtolower($data['NUMERO_PAC']));   
	  }else{$NUMERO_PAC ="";}
*/
//area y region inicial 
/*
	  $CO_REGION = "1";
	  $DE_REGION = "Lima";
	  $CO_MACRO_REGIONAL ="1";
	  $DE_MACRO_REGIONAL ="Macro Regional Lima";
	  $MACRO_REGION_INICIAL=$CO_MACRO_REGIONAL;
	  $MACRO_REGION_INICIAL_label = $DE_MACRO_REGIONAL;

	  $CO_AREA ="1";
	  $CO_GRUPO_BPM="PLATAFORMA";        
	  $AREA_INICIAL ="PLATAFORMA";
	  $AREA_INICIAL_label="Atención en plataforma";    
*/

		
	








?>