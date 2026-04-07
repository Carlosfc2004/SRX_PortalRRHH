<?php
class SAPMuleSoftConnector {
    private $model;

    public function __construct($model = null) {
        if ($model === null) {
            require_once __DIR__ . '/../models/sqlsrvModel.php';
            $this->model = new \sqlsrvModel();
        } else {
            $this->model = $model;
        }
    }

    /**
     * Listar documentos de un empleado
     */
    public function listarDocumentos($pernr) {
        $apiResponse = $this->model->curl_api_mulesoft([], 'GET', "/documentos/" . trim($pernr));

        if (isset($apiResponse['success']) && $apiResponse['success'] === true) {
            return ['success' => true, 'data' => $apiResponse['data']];
        }

        return ['success' => false, 'error' => $apiResponse['message'] ?? 'Error desconocido'];
    }

    /**
     * Obtener contenido binario de un documento (descarga y preview)
     */
    public function obtenerDocumento($pernr, $doknr) {
        $apiResponse = $this->model->curl_api_mulesoft([], 'GET', "/documentos/$pernr/$doknr");

        if (!isset($apiResponse['success']) || $apiResponse['success'] !== true) {
            return ['error' => $apiResponse['message'] ?? 'Error al obtener el documento'];
        }

        $response = $apiResponse['data'];

        return [
            'content'  => hex2bin(base64_decode($response['content'])),
            'mimetype' => $response['mimetype'],
            'filename' => $response['filename'],
        ];
    }

    /**
     * Subir documento
     */
    public function subirDocumento($pernr, $fileContent, $originalName, $descripcion) {
        $tempFile = tempnam(sys_get_temp_dir(), 'doc_');
        file_put_contents($tempFile, $fileContent);

        $payload = [
            'FILENAME'    => $originalName,
            'DESCRIPTION' => $descripcion,
            'DOC'         => new CURLFile($tempFile, 'application/octet-stream', $originalName)
        ];

        $apiResponse = $this->model->curl_api_mulesoft($payload, 'POST', "/documentos/$pernr");
        unlink($tempFile);

        if (!isset($apiResponse['success']) || $apiResponse['success'] !== true) {
            return ['E_SUBRC' => 4, 'E_MESSAGE' => $apiResponse['message'] ?? 'Error de conexión con el servidor'];
        }

        $data = $apiResponse['data'];

        return isset($data['status']) && $data['status'] === true
            ? ['E_SUBRC' => 0, 'E_MESSAGE' => $data['message'] ?? 'Documento subido correctamente']
            : ['E_SUBRC' => 4, 'E_MESSAGE' => $data['message'] ?? 'Error al subir el documento'];
    }

    /**
     * Eliminar documento
     */
    public function eliminarDocumento($pernr, $doknr) {
        $apiResponse = $this->model->curl_api_mulesoft([], 'DELETE', "/documentos/$pernr/$doknr");

        if (!isset($apiResponse['success']) || $apiResponse['success'] !== true) {
            return ['success' => false, 'message' => $apiResponse['message'] ?? 'Error al eliminar el documento'];
        }

        return ['success' => true, 'message' => $apiResponse['data']['message'] ?? 'Documento eliminado correctamente'];
    }
}