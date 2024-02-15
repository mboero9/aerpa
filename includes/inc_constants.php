<?php
// Errores posibles de login
define("ERRL_OK", "OK");
define("ERRL_USUARIO", "USUARIO");
define("ERRL_PASSWORD", "PASSWORD");
define("ERRL_BLOQUEADO", "BLOQUEADO");
define("ERRL_INHABILITADO", "INHABILITADO");
define("ERRL_YAINGRESADO", "YAINGRESADO");
define("ERRL_BAJA", "BAJA");
define("ERRL_CAMBIOPASS", "CAMBIOPASS");

// Constantes para sesion
define("SES_DURACION", 5);	// minutos
define("SES_COOKIE", "aerpa_sesion");
define("SES_BLOQUEAR", 10);
define("SES_HISTORIA", 5);	// dias

// Mantenimiento de log
//CAMBIADO POR PARAMETRO EN TABLA
//define("LOG_HISTORIA", 15);	// dias

// Maximo de Fecha de Entrega de Tramites
define("MAX_ENTREGA", 2);	// dias

//cantidad de dias para pasar a Historico
define("DIAS_HISTORICO",60);

// Constantes para ABMs
define("ABM_VIEW", 0);
define("ABM_NEW", 1);
define("ABM_EDIT", 2);
define("ABM_DEL", 3);
define("ABM_SETDEFAULT", 4);
define("ABM_REHABILITAR", 5);

define("ABMLBL_VIEW", "Ver");
define("ABMLBL_NEW", "Confirmar");
define("ABMLBL_EDIT", "Modificar");
define("ABMLBL_DEL", "Borrar");
define("ABMLBL_SETDEFAULT", "Fijar predeterminado");
define("ABMLBL_REHABILITAR", "Rehabilitar");

define("CONFIRMO", "Confirmar");
define("CANCELO", "Cancelar");
define("GRABO", "GRABADO EXITOSAMENTE");
define("EXPORTAR", "Generar Archivo");
define("IMPRIMIR", "Imprimir");
define("VOLVER", "Volver");
define("ANULAR","Anular");

define("RECS_PER_PAGE", 20);
define("PAGE_LINKS", 7);

// Tipos de permiso
define("TPERM_NORMAL", "");
define("TPERM_EDIT", "E");

define("PERM_ALL", " ");
define("PERM_VIEW", "V");
define("PERM_EDIT", "E");

define("PERMLBL_ALL", "Completo");
define("PERMLBL_VIEW", "Ver");
define("PERMLBL_EDIT", "Editar");

// Tipos de alta de usuario
define("ALTAUSR_MANUAL", "M");
define("ALTAUSR_LOTE", "L");

// Nombres de parametros
define("PAR_SES_DURACION", "DURACION_SESION");
define("PAR_SES_BLOQUEO", "INTENTOS_LOGIN_BLOQUEO");
define("PAR_USRIMPORT_CAMBIARPW", "USRIMPORT_CAMBIARPW");
define("PAR_REMITO_PERIODO", "REMITO_PERIODO");
define("PAR_REMITO_ANTIGUEDAD", "REMITO_ANTIGUEDAD");
define("PAR_REMITO_ESTADO_GEN", "GENERADO");
define("PAR_PLAZO_ENTREGA", "PLAZO_ENTREGA");
define("PAR_ALARMA_FROM", "ALARMA_FROM");

// Procesos batch
define("PROC_CRONTAB", "CRONTAB");
define("PROC_ALARMAS", "ALARMAS");
define("PROC_DEPURAR_SESIONES", "DEPURACION_SESIONES");
define("PROC_DEPURAR_LOG", "DEPURACION_LOG");
define("PROC_HISTORICO_DEP", "Pasaje a Historico & Dep");
define("PROC_DEPURAR_HIST", "DEPURACION DE HISTORICOS");
define("PROC_ENVIO_ARCHIVOS", "ENVIO_ARCHIVOS");

// Parametros de alarmas
define("PALAR_SISTEMA", "MAIL_ERROR_SIS");
define("PALAR_PROC", "PROC_NO_RUN");

// Importaciones desde CSV
define("CSV_MAX_LINE", 2000);
define("CSV_REL_FIELD", "##RELFIELD##");
define("CSV_REL_FLD_INT", "int");
define("CSV_REL_FLD_STRING", "string");
define("CSV_REL_FLD_DATE", "date");

// Exportaciones a CSV
// tipos de totalizado de campos
define("CSV_EXP_CUENTA", "C");
define("CSV_EXP_SUMA", "S");
define("CSV_EXP_PROMEDIO", "P");

// Formatos de fecha
define("FMT_DATE_DB", "d/m/Y");
define("FMT_DATE_CAL", "%d/%m/%Y");
define("FMT_DATETIME_DB", "d/m/Y H:i:s");
define("FMT_DATETIME_CAL", "%d/%m/%Y %H:%i:%s");
define("FMT_DATE_ISO", "Y-m-d H:i:s");
define("FMT_DATE", "Ymd");

// Tipos de registro
define("TREG_DESTINO", "D");
define("TREG_AMBOS", "A");

define("TREGLBL_DESTINO", "Destino");
define("TREGLBL_AMBOS", "Ambos");

//Ruta de procesos
define("RUTA_PROCESOS","..\\procesos\\");
//Numero de remito origen y destino
define("NRO_ORIGEN","PAR_NRO_REMITO_ORIGEN");
define("NRO_DESTINO","PAR_NRO_REMITO_DESTINO");
define("NRO_DEVOLUCION","PAR_NRO_REMITO_DEVOLUCION");

//Estado remitos
define("ANULADO","ANULADO");
define("CERRADO","CERRADO");
define("ENTREGADO","ENTREGADO");
//Tipo de Remito
define("ORIGEN","origen");
define("DESTINO","destino");
define("DEVOLUCION","devolucion");
?>
