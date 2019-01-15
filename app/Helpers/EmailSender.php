<?php
declare(strict_types=1);

namespace App\Helpers;

use DB;

/**
 * Class EmailSender
 * @package App\CustomHelpers
 */
class EmailSender
{
    /**
     * @var
     */
    protected $templateData;

    /**
     * @var
     */
    protected $type;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * EmailSender constructor.
     * @param $type
     * @param $params
     */
    public function __construct(int $type, array $params)
    {
        $this->type = $type;
        $this->params = $params;

        $this->setTemplateData();
    }

    /**
     *
     */
    protected function setTemplateData(): void
    {
        $this->templateData = DB::table('email_templates')->where('type', $this->type)->first();

        $params = $this->params;
        $this->format($params);
    }

    /**
     * @param array $params
     * @return mixed
     */
    protected function format(array $params)
    {
        foreach ($params as $name => $value) {
            $this->templateData->body = str_ireplace('[' . $name . ']', $value, $this->templateData->body);
        }
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->templateData->body;
    }
}
