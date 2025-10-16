<?php

//Configuración conexiones, datos de acceso de la BBDD [SUREXPORT_WEBAPP]

	class ConfigAppReclutamiento{
		static public $bdsrx_hostname = "172.21.0.38";
		// static public $bdsrx_nombre   = "AppReclutamiento";
		static public $bdsrx_nombre   = "AppReclutamiento_test";
		static public $bdsrx_usuario  = "prosadoc";
		static public $bdsrx_clave    = "Prosadoc2020";
	}


	class ConfigMuleSoft{
		static public $bdsrx_hostname = "172.21.0.38";
		// static public $bdsrx_nombre   = "Mulesoft";
		static public $bdsrx_nombre   = "MulesoftTest";
		static public $bdsrx_usuario  = "prosadoc";
		static public $bdsrx_clave    = "Prosadoc2020";
	}

	class ConfigMante{
		static public $bdsrx_hostname = "172.21.0.38";
		// static public $bdsrx_nombre   = "SistemaMantenimiento";
		static public $bdsrx_nombre   = "SistemaMantenimiento_test2";
		static public $bdsrx_usuario  = "prosadoc";
		static public $bdsrx_clave    = "Prosadoc2020";
	}

	class ConfigPortalEmpleado{
        static public $bdsrx_hostname = "172.21.0.38";
        // static public $bdsrx_nombre   = "PortalDelEmpleado";
        static public $bdsrx_nombre   = "PortalDelEmpleado_test";
        static public $bdsrx_usuario  = "prosadoc";
        static public $bdsrx_clave    = "Prosadoc2020";
    }

	class ConfigAgromobile{
		// static public $bdsrx_hostname = "192.168.200.202";
		// static public $bdsrx_nombre   = "SUREXPORT_AGROMOBILE";
		static public $bdsrx_nombre   = "SUREXPORT_AGROMOBILE_TEST";
		// static public $bdsrx_usuario  = "prosadoc";
		// static public $bdsrx_clave    = "Prosadoc2020";
	}

	class ConfigWebApp{
		static public $bdsrx_hostname = "192.168.200.202";
		// static public $bdsrx_nombre   = "SUREXPORT_WEBAPP";
		static public $bdsrx_nombre   = "SUREXPORT_WEBAPP_TEST";
		static public $bdsrx_usuario  = "prosadoc";
		static public $bdsrx_clave    = "Prosadoc2020";
	}



//Configuración conexiones, APIs

	class ConfigApiMulesoft{
		// QA
		static public $api_url 			 = "https://e-personal-api-lew5o5.a8uuoc.deu-c1.cloudhub.io/api";
		static public $api_client_id   	 = "8859b0e11f434a10b5e55abaade33a07";
		static public $api_client_secret = "65CF7d7f63504bD295b78F3f9F35c70d";

		// PROD
		// static public $api_url 			 = "https://e-personal-api-prod-eoeim0.a8uuoc.deu-c1.cloudhub.io/api";
		// static public $api_client_id   	 = "8859b0e11f434a10b5e55abaade33a07";
		// static public $api_client_secret = "65CF7d7f63504bD295b78F3f9F35c70d";
	}
?>
