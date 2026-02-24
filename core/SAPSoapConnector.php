<?php

// Clase para conectar con SAP usando SOAP y llamar a funciones RFC
class SAPSoapConnector {
    private $host;
    private $port;
    private $client;
    private $user;
    private $password;
    private $timeout;
    private $ssl = false;

    public function __construct(
        $host     = 'srvsaptes.surexport.es',
        $port     = 44300,
        $client   = '300',
        $user     = 'DEVSXT0',
        $password = 'FSP2027+',
        $timeout  = 30,
        $ssl      = true
    ) {
        $this->host     = $host;
        $this->port     = $port;
        $this->client   = $client;
        $this->user     = $user;
        $this->password = $password;
        $this->timeout  = $timeout;
        $this->ssl      = $ssl;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MÉTODOS PRIVADOS DE INFRAESTRUCTURA
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Centraliza la llamada CURL para no repetir el bloque en cada método.
     */
    private function curlRequest($soapBody)
    {
        $protocol = $this->ssl ? 'https' : 'http';
        $url = "{$protocol}://{$this->host}:{$this->port}/sap/bc/soap/rfc?sap-client={$this->client}";

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $soapBody,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERPWD        => $this->user . ':' . $this->password,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: urn:sap-com:document:sap:rfc:functions',
            ],
        ]);
        $response = curl_exec($ch);
        return $response;
    }

    /**
     * Construye el envelope SOAP completo a partir del nombre de función y los parámetros XML.
     */
    private function buildEnvelope($functionName, $paramsXml)
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"'
            . ' xmlns:ns1="urn:sap-com:document:sap:rfc:functions">'
            . '<SOAP-ENV:Body>'
            . "<ns1:{$functionName}>"
            . $paramsXml
            . "</ns1:{$functionName}>"
            . '</SOAP-ENV:Body>'
            . '</SOAP-ENV:Envelope>';
    }

    /**
     * Llama a cualquier función RFC genérica y devuelve la respuesta como array.
     * Usado para funciones cuya respuesta son campos simples (DELETE, etc.).
     */
    private function callRFC($functionName, $params = [])
    {
        $paramsXml = '';
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $paramsXml .= "<{$key}>";
                foreach ($value as $subKey => $subVal) {
                    $paramsXml .= "<{$subKey}>" . htmlspecialchars($subVal) . "</{$subKey}>";
                }
                $paramsXml .= "</{$key}>";
            } else {
                $paramsXml .= "<{$key}>" . htmlspecialchars($value) . "</{$key}>";
            }
        }

        $response = $this->curlRequest($this->buildEnvelope($functionName, $paramsXml));

        if (!$response || trim($response) === '') return [];

        $xml = preg_replace('/(<\/?)[\w]+:/', '$1', $response);
        $xml = preg_replace('/\s+xmlns[^=]*="[^"]*"/', '', $xml);
        $doc = new DOMDocument();
        if (!@$doc->loadXML($xml, LIBXML_NOERROR | LIBXML_NOWARNING)) return [];

        // Buscar el nodo de respuesta: <FunctionName.Response> o <FunctionNameResponse>
        $nodes = $doc->getElementsByTagName($functionName . '.Response');
        if ($nodes->length === 0) {
            $nodes = $doc->getElementsByTagName($functionName . 'Response');
        }
        if ($nodes->length === 0) {
            $bodyNodes = $doc->getElementsByTagName('Body');
            if ($bodyNodes->length === 0) return [];
            $responseNode = $bodyNodes->item(0)->firstChild;
        } else {
            $responseNode = $nodes->item(0);
        }

        return $this->nodeToArray($responseNode);
    }

    /**
     * Convierte un nodo XML a array asociativo, manejando tablas y estructuras anidadas.
     */
    private function nodeToArray($node)
    {
        $result = [];
        if (!$node->hasChildNodes()) {
            return [$node->nodeName => $node->nodeValue];
        }
        foreach ($node->childNodes as $child) {
            if ($child->nodeType !== XML_ELEMENT_NODE) continue;
            $name = $child->nodeName;

            if ($child->hasChildNodes() && $child->firstChild->nodeType === XML_ELEMENT_NODE) {
                $firstChildName = $child->firstChild->nodeName;
                $isTable = true;
                $items   = [];
                foreach ($child->childNodes as $grandChild) {
                    if ($grandChild->nodeType !== XML_ELEMENT_NODE) continue;
                    if ($grandChild->nodeName !== $firstChildName) { $isTable = false; break; }
                    $items[] = $this->nodeToArray($grandChild);
                }
                $result[$name] = ($isTable && count($items) > 0) ? $items : $this->nodeToArray($child);
            } else {
                $value = trim($child->nodeValue);
                if (isset($result[$name])) {
                    if (!is_array($result[$name]) || !isset($result[$name][0])) {
                        $result[$name] = [$result[$name]];
                    }
                    $result[$name][] = $value;
                } else {
                    $result[$name] = $value;
                }
            }
        }
        return $result;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MÉTODOS PÚBLICOS: ZHR_GESTION_DOC_API
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * LIST — Lista los documentos de un empleado.
     * Usa curlRequest directamente porque nodeToArray no maneja bien los nodos <item>.
     *
     * @param  string $pernr  Número de personal (8 dígitos).
     * @return array          Array de documentos con campos: DOKNR, FILENAME, DESCRIPTION,
     *                        MIMETYPE, FILESIZE, CREATED_BY, CREATED_AT.
     */
    public function docList($pernr)
    {
        $paramsXml = '<I_ACTION>LIST</I_ACTION>'
            . '<I_PERNR>' . htmlspecialchars($pernr) . '</I_PERNR>'
            . '<ET_DOCUMENTS></ET_DOCUMENTS>';

        $response = $this->curlRequest($this->buildEnvelope('ZHR_GESTION_DOC_API', $paramsXml));
        if (!$response) return [];

        $xml = preg_replace('/(<\/?)[\w]+:/', '$1', $response);
        $xml = preg_replace('/\s+xmlns[^=]*="[^"]*"/', '', $xml);
        $doc = new DOMDocument();
        if (!@$doc->loadXML($xml)) return [];

        $docs = [];
        foreach ($doc->getElementsByTagName('item') as $item) {
            $row = [];
            foreach ($item->childNodes as $child) {
                if ($child->nodeType === XML_ELEMENT_NODE) {
                    $row[$child->nodeName] = trim($child->nodeValue);
                }
            }
            if (!empty($row)) $docs[] = $row;
        }
        return $docs;
    }

    /**
     * GET — Obtiene el contenido binario de un documento.
     * Usa regex directamente porque el base64 de E_CONTENT rompe DOMDocument.
     *
     * @param  string $doknr  Número de documento SAP.
     * @return array          Array con: E_CONTENT (base64), E_FILENAME, E_MIMETYPE,
     *                        E_FILESIZE, E_SUBRC, E_MESSAGE.
     */
    public function docGet($doknr)
    {
        $paramsXml = '<I_ACTION>GET</I_ACTION>'
            . '<I_DOKNR>' . htmlspecialchars($doknr) . '</I_DOKNR>';

        $response = $this->curlRequest($this->buildEnvelope('ZHR_GESTION_DOC_API', $paramsXml));
        if (!$response) return [];

        $result = [];
        foreach (['E_CONTENT', 'E_FILENAME', 'E_MIMETYPE', 'E_FILESIZE', 'E_SUBRC', 'E_MESSAGE'] as $field) {
            if (preg_match('/<' . $field . '>(.*?)<\/' . $field . '>/s', $response, $m)) {
                $result[$field] = $m[1];
            }
        }
        return $result;
    }

    /**
     * UPLOAD — Sube un documento a SAP asociado a un empleado.
     * Usa regex igual que docGet porque I_CONTENT (base64) puede ser muy grande.
     *
     * @param  string $pernr          Número de personal (8 dígitos).
     * @param  string $filename       Nombre del archivo (ej: "contrato.pdf").
     * @param  string $description    Descripción del documento (máx. 40 caracteres).
     * @param  string $contentBase64  Contenido del archivo codificado en base64.
     * @return array                  Array con: E_DOKNR, E_SUBRC, E_MESSAGE.
     */
    public function docUpload($pernr, $filename, $description, $contentBase64)
    {
        // No aplicamos htmlspecialchars al base64 porque solo contiene [A-Za-z0-9+/=]
        $paramsXml = '<I_ACTION>UPLOAD</I_ACTION>'
            . '<I_PERNR>'       . htmlspecialchars($pernr)       . '</I_PERNR>'
            . '<I_FILENAME>'    . htmlspecialchars($filename)    . '</I_FILENAME>'
            . '<I_DESCRIPTION>' . htmlspecialchars($description) . '</I_DESCRIPTION>'
            . '<I_CONTENT>'     . $contentBase64                 . '</I_CONTENT>'
            . '<I_CREATED_BY>'  . htmlspecialchars($this->user)   . '</I_CREATED_BY>';

        $response = $this->curlRequest($this->buildEnvelope('ZHR_GESTION_DOC_API', $paramsXml));
        if (!$response) return [];

        $result = [];
        foreach (['E_DOKNR', 'E_SUBRC', 'E_MESSAGE'] as $field) {
            if (preg_match('/<' . $field . '>(.*?)<\/' . $field . '>/s', $response, $m)) {
                $result[$field] = $m[1];
            }
        }
        return $result;
    }

    /**
     * DELETE — Elimina un documento de SAP.
     * Usa callRFC porque la respuesta es simple (E_SUBRC + E_MESSAGE).
     *
     * @param  string $doknr  Número de documento SAP.
     * @return array          Array con: E_SUBRC, E_MESSAGE.
     */
    public function docDelete($doknr)
    {
        return $this->callRFC('ZHR_GESTION_DOC_API', [
            'I_ACTION' => 'DELETE',
            'I_DOKNR'  => $doknr,
        ]);
    }
}