<?php
/**
 * AbstractZadarmaIntegration
 * @author Volkov Alexander
 */

namespace lib;

class AbstractZadarmaIntegration
{
    protected $crm_name = null;

    protected $zadarma_config = [];
    protected $crm_config = [];

    public $cCrm = null;
    /** @var \Zadarma_API\Client|null  */
    public $cZadarma = null;

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        define('ROOT_DIR', sprintf('%s/./../', __DIR__));

        $this->zadarma_config = include ROOT_DIR . '/config/zadarma.php';
        $crm_config_file = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, sprintf('%s/config/%s.php', ROOT_DIR, $this->crm_name));

        if(file_exists($crm_config_file)) {
            $this->crm_config = include $crm_config_file;
        } else throw new \Exception(sprintf('Configuration file for "%s" not found', $this->crm_name));

        try {
            $this->initZadarmaClient();
            $this->initCrmClient();
        } catch (\Exception $e) {
            throw new \Exception('Can\'t initialize api-clients');
        }
    }

    /**
     * @param string $from
     * @param string $to
     * @param string $sip
     * @param bool $predicted
     *
     * @return mixed
     */
    public function makeCallbackToPhone($from, $to, $sip = null, $predicted = false)
    {
        $result = null;

        try {
            $result = $this->cZadarma->call('/v1/request/callback/', [
                'from' => $from,
                'to' => $to,
                'sip' => $sip,
                'predicted' => $predicted
            ]);
        } catch (\Exception $e) {
            echo 'Error callback';
        }

        return $result;
    }

    protected function initZadarmaClient()
    {
        $this->cZadarma = new \Zadarma_API\Client(
            $this->zadarma_config['key'], $this->zadarma_config['secret']
        );
    }

    public function getCallRecord($call_id, $pbx_call_id, $lifetime = 5184000)
    {
        $result = null;

        $response = json_decode($this->cZadarma->call('/v1/pbx/record/request/', [
            'call_id' => $call_id,
            'pbx_call_id' => $pbx_call_id,
            'lifetime' => $lifetime
        ], 'get'), true);

        if(!empty($response) && $response['status'] === 'success') {
            $result = (isset($response['links']) && count($response['links']) === 1) ? $response['links'][0] : null;
        }

        return $result;
    }

    protected function initCrmClient() {}

    protected function parseRequestFromCrm() {}

    const ZD_CALLBACK_EVENT_START = 'NOTIFY_START';
    const ZD_CALLBACK_EVENT_INTERNAL = 'NOTIFY_INTERNAL';
    const ZD_CALLBACK_EVENT_END = 'NOTIFY_END';
    const ZD_CALLBACK_EVENT_OUT_START = 'NOTIFY_OUT_START';
    const ZD_CALLBACK_EVENT_OUT_END = 'NOTIFY_OUT_END';
}