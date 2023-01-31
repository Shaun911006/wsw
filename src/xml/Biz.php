<?php
/**
 * bizXml结构体，一般不需要改动
 * Author:Shaun·Yang
 * Date:2023/1/30
 * Time:上午8:49
 * Description:bizXml结构体，一般不需要改动
 */

namespace wsw\xml;

use wsw\XmlUtil;

class Biz
{
    private array $xmlArr = [
        'tiripPackage' => [
            'sessionId' => '',
            'service' => [
                'serviceId' => '',
                'tranSeq' => '',
                'repeatFlag' => '',
                'tranReqDate' => '',
            ],
            'identity' => [
                'application' => [
                    'applicationId' => '',
                    'supplier' => '',
                    'version' => '',
                    'authenticateType' => '',
                    'cert' => '',
                    'password' => '',
                ],
                'customer' => [
                    'nsrsbh' => '',
                ]
            ],
            'contentControl' => [
                'control' => [
                    'id' => 1,
                    'type' => 'code',
                    'impl' => 'BASE64',
                ]
            ],
            'businessContent' => [
                'subPackage' => [
                    'id' => '',
                    'content' => ''
                ]
            ]
        ]
    ];

    public function buildXml(): string
    {
        return XmlUtil::encode($this->xmlArr);
    }

    public function setSessionId($sessionId): Biz
    {
        $this->xmlArr['tiripPackage']['sessionId'] = $sessionId;
        return $this;
    }

    public function setServiceId($serviceId): Biz
    {
        $this->xmlArr['tiripPackage']['service']['serviceId'] = $serviceId;
        return $this;
    }
    public function setTranSeq($tranSeq): Biz
    {
        $this->xmlArr['tiripPackage']['service']['tranSeq'] = $tranSeq;
        return $this;
    }
    public function setRepeatFlag($repeatFlag): Biz
    {
        $this->xmlArr['tiripPackage']['service']['repeatFlag'] = $repeatFlag;
        return $this;
    }
    public function setTranReqDate($tranReqDate): Biz
    {
        $this->xmlArr['tiripPackage']['service']['tranReqDate'] = $tranReqDate;
        return $this;
    }

    public function setApplicationId($applicationId): Biz
    {
        $this->xmlArr['tiripPackage']['identity']['application']['applicationId'] = $applicationId;
        return $this;
    }
    public function setSupplier($supplier): Biz
    {
        $this->xmlArr['tiripPackage']['identity']['application']['supplier'] = $supplier;
        return $this;
    }
    public function setVersion($version): Biz
    {
        $this->xmlArr['tiripPackage']['identity']['application']['version'] = $version;
        return $this;
    }
    public function setAuthenticateType($authenticateType): Biz
    {
        $this->xmlArr['tiripPackage']['identity']['application']['authenticateType'] = $authenticateType;
        return $this;
    }
    public function setCert($cert): Biz
    {
        $this->xmlArr['tiripPackage']['identity']['application']['cert'] = $cert;
        return $this;
    }
    public function setPassword($password): Biz
    {
        $this->xmlArr['tiripPackage']['identity']['application']['password'] = $password;
        return $this;
    }
    public function setNsrsbh($nsrsbh): Biz
    {
        $this->xmlArr['tiripPackage']['identity']['customer']['nsrsbh'] = $nsrsbh;
        return $this;
    }

    public function setContent($content): Biz
    {
        $this->xmlArr['tiripPackage']['businessContent']['subPackage']['content'] = $content;
        return $this;
    }

    //自动填充默认值
    public function autoSet(): Biz
    {
        return $this->setRepeatFlag(0)
            ->setTranReqDate(date('Ymd'))
            ->setapplicationId('SFJR')
            ->setversion('1')
            ->setauthenticateType('2');
    }
}