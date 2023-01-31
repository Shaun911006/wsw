<?php
/**
 * 业务报文（企业登录、选择办税主体、选择办税人员会用到）
 * Author:Shaun·Yang
 * Date:2023/1/30
 * Time:下午1:41
 * Description:业务报文（企业登录、选择办税主体、选择办税人员会用到）
 */

namespace wsw\xml;

use wsw\XmlUtil;

class BusinessContent
{
    private array $xmlArr = [
        'taxML' => [
            'body' => [
                'param' => []
            ]
        ]
    ];

    public function __construct($params)
    {
        $this->setParams($params);
    }
    /**
     * @param array $params
     * @return BusinessContent
     */
    public function setParams(array $params): BusinessContent
    {
        $this->xmlArr['taxML']['body']['param'] = $params;
        return $this;
    }

    public function buildXml(): string
    {
        return XmlUtil::encode($this->xmlArr);
    }

    public static function getXml($params): string
    {
        $obj = new BusinessContent($params);
        return $obj->buildXml();
    }
}