<?php
/**
 * 微税务客户端
 * Author:Shaun·Yang
 * Date:2023/1/30
 * Time:下午3:22
 * Description:
 */

namespace wsw;

use SoapClient;
use SoapFault;
use WsdlToPhp\WsSecurity\WsSecurity;
use wsw\exceptions\WswException;
use wsw\xml\Biz;
use wsw\xml\BusinessContent;

class WswClient
{
    /**
     * 参数配置
     * @var array
     */
    private array $conf = [
        'wsdl'       => '',
        'desKey'     => '',
        'username'   => '',
        'password'   => '',
        'nsrsbh'     => '',
        'yhm'        => '',
        'mm'         => '',
        'bsrysfjzhm' => '',    //办税人员身份证号码
        'log'        => false,
        'log_f_path' => '',    //日志文件路径配置 wsw_log文件夹的父目录
        'client_num' => '11111111111' //客户端编号11位
    ];

    /**
     * DES加解密工具
     * @var DESUtil
     */
    private DESUtil $desUtil;

    /**
     * Soap对象
     * @var SoapClient
     */
    private SoapClient $soapClient;

    /**
     * 接口id：企业登录
     */
    public const DOLOGIN = 'SWZJ.DZSWJ.FJGGFW.DL.DOLOGIN';

    /**
     * 接口id：选择办税主体
     */
    public const CHOOSEBSZT = 'SWZJ.DZSWJ.FJGGFW.DL.CHOOSEBSZT';

    /**
     * 接口id：选择办税人员
     */
    public const CHOOSEBSRY = 'SWZJ.DZSWJ.FJGGFW.DL.CHOOSEBSRY';

    /**
     * 接口id：授权校验
     */
    public const CXPTQYSQXX = 'SWZJ.DZSWJ.PTQYSSGL.CXPTQYSQXX';

    /**
     * 接口id：会员信息导入接口
     */
    public const HYXXBG = 'SWZJ.DZSWJ.PTQYSSGL.HYXXBG';

    /**
     * 接口id：会员明细查询
     */
    public const CXHYMXXX = 'SWZJ.DZSWJ.PTQYSSGL.CXHYMXXX';

    /**
     * ERROR_RESPONSE_PARSE:响应报文解析失败
     */
    public const ERROR_RESPONSE_PARSE = 500;
    /**
     * ERROR_RESPONSE:响应报文报错
     */
    public const ERROR_RESPONSE = 501;
    /**
     * ERROR_BUSINESS_PARSE:业务报文解析失败
     */
    public const ERROR_BUSINESS_PARSE = 400;
    /**
     * ERROR_BUSINESS:业务报错
     */
    public const ERROR_BUSINESS = 401;

    /**
     * 最近一次返回信息，调试用
     *
     * @var array
     */
    public $lastRes;

    /**
     * @param array $conf 配置
     * @throws SoapFault
     */
    public function __construct(array $conf = [])
    {
        $this->conf    = array_merge($this->conf, $conf);
        $this->desUtil = new DESUtil($conf['desKey']);

        $username = $conf['username'];
        $password = $this->desUtil->encrypt($conf['password'] . '_' . time() . '000');

        $header = WsSecurity::createWsSecuritySoapHeader($username, $password,
            false, 0, 0, true, true, null, null, false
        );

        $this->soapClient = new SoapClient($conf['wsdl']);
        $this->soapClient->__setSoapHeaders($header);
    }

    /**
     * 登录过程 分为三步 封装到一起 返回 sessionId
     * @param array $codes 三个流水编号最大8位，例如["71","72","73"]
     * @return string sessionId
     * @throws WswException
     */
    public function loginProcess(array $codes): string
    {
        //第一步，企业登录
        list($sessionid, $djxh) = $this->doLogin($codes[0] ?? 0);
        //第二步，选择办税主体
        $bsry = $this->chooseBszt($codes[1] ?? 0, $sessionid, $djxh);
        //第三步，选择办税人员
        if (!in_array($this->conf['bsrysfjzhm'], array_column($bsry, 'sfzjhm'))) {
            throw new WswException('配置的办税员不在登记范围内');
        }
        $this->chooseBsry($codes[2] ?? 0, $sessionid, $this->conf['bsrysfjzhm']);
        return $sessionid;
    }

    /**
     * 企业登录
     * @param string|int $code 顺序号
     * @return array sessionid和登记序号 [sessionid,djxh]
     * @throws WswException
     */
    private function doLogin($code): array
    {
        $content = BusinessContent::getXml([
            'yhm' => $this->desUtil->encrypt($this->conf['yhm']),
            'mm'  => $this->desUtil->encrypt($this->conf['mm']),
        ]);

        $biz    = new Biz();
        $bizXml = $biz->autoSet()
            ->setServiceId(WswClient::DOLOGIN)
            ->setNsrsbh($this->conf['nsrsbh'])
            ->setTranSeq($this->conf['username'] . $this->conf['client_num'] . '00000' . date('Ymd') . str_pad($code, 8, '0', STR_PAD_LEFT))  //交易流水号为36位长字符串，编码规则为：4位厂商简码 + 11位客户端编号（任意11字字节字符串） + “00000” + 4位年 + 2位月 + 2位日 + 8位顺序号 共36位。
            ->setContent(base64_encode($content))
            ->buildXml();

        $res = $this->getResFromXml($bizXml);
        //登录成功了返回两个参数 sessionid和登记序号djxh
        return [$res['body']['result']['sessionid'], $res['body']['result']['nsrxxinfo']['nsrxx']['djxh']];

    }

    /**
     * 选择办税主体
     * @param string|int $code 顺序号
     * @param string $sessionid sessionId
     * @param string $djxh 登记序号
     * @return array 办税人员列表
     * @throws WswException
     */
    private function chooseBszt($code, string $sessionid, string $djxh): array
    {
        $content = BusinessContent::getXml([
            'sessionid' => $sessionid,
            'djxh'      => $djxh,
        ]);

        $biz    = new Biz();
        $bizXml = $biz->autoSet()
            ->setSessionId($sessionid)
            ->setServiceId(WswClient::CHOOSEBSZT)
            ->setNsrsbh($this->conf['nsrsbh'])
            ->setTranSeq($this->conf['username'] . $this->conf['client_num'] . '00000' . date('Ymd') . str_pad($code, 8, '0', STR_PAD_LEFT))  //交易流水号为36位长字符串，编码规则为：4位厂商简码 + 11位客户端编号（任意11字字节字符串） + “00000” + 4位年 + 2位月 + 2位日 + 8位顺序号 共36位。
            ->setContent(base64_encode($content))
            ->buildXml();

        $res = $this->getResFromXml($bizXml);
        //选择纳税主体成功成功了返回办税人员列表
        return $res['body']['result']['bsry']['bsryxx'];
    }

    /**
     * 选择办税人员
     * @param string|int $code 顺序号
     * @param string $sessionid sessionId
     * @param string $sfzjhm
     * @return void
     * @throws WswException
     */
    private function chooseBsry($code, string $sessionid, string $sfzjhm): void
    {
        $content = BusinessContent::getXml([
            'sessionid' => $sessionid,
            'sfzjlx'    => 201,
            'sfzjhm'    => $sfzjhm,
        ]);

        $biz    = new Biz();
        $bizXml = $biz->autoSet()
            ->setSessionId($sessionid)
            ->setServiceId(WswClient::CHOOSEBSRY)
            ->setNsrsbh($this->conf['nsrsbh'])
            ->setTranSeq($this->conf['username'] . $this->conf['client_num'] . '00000' . date('Ymd') . str_pad($code, 8, '0', STR_PAD_LEFT))  //交易流水号为36位长字符串，编码规则为：4位厂商简码 + 11位客户端编号（任意11字字节字符串） + “00000” + 4位年 + 2位月 + 2位日 + 8位顺序号 共36位。
            ->setContent(base64_encode($content))
            ->buildXml();

        $this->getResFromXml($bizXml);
    }

    /**
     * 查询平台企业授权信息
     * @param string|int $code 顺序号
     * @param string $sessionid sessionId
     * @return array 企业授权起止时间[启用时间，停用时间]
     * @throws WswException
     */
    public function cxptqysqxx($code, string $sessionid): array
    {
        $content = json_encode([
            'sessionid' => $sessionid
        ]);

        $biz    = new Biz();
        $bizXml = $biz->autoSet()
            ->setSessionId($sessionid)
            ->setServiceId(WswClient::CXPTQYSQXX)
            ->setNsrsbh($this->conf['nsrsbh'])
            ->setTranSeq($this->conf['username'] . $this->conf['client_num'] . '00000' . date('Ymd') . str_pad($code, 8, '0', STR_PAD_LEFT))  //交易流水号为36位长字符串，编码规则为：4位厂商简码 + 11位客户端编号（任意11字字节字符串） + “00000” + 4位年 + 2位月 + 2位日 + 8位顺序号 共36位。
            ->setContent(base64_encode($content))
            ->buildXml();

        $res         = $this->getResFromJson($bizXml);
        $res['body'] = json_decode($res['body'], true);
        return [strtotime($res['body']['qysj']), strtotime($res['body']['tysj']) + 86399];
    }

    /**
     * 会员信息报告
     * @param string|int $code 顺序号
     * @param string $sessionid sessionId
     * @param array $members 会员数组（可以是多个）
     * @return bool
     * @throws WswException
     */
    public function hyxxbg($code, string $sessionid, array $members, $debug = false): bool
    {
        $content = json_encode([
            'sessionid' => $sessionid,
            'data'      => $members
        ]);

        $biz    = new Biz();
        $bizXml = $biz->autoSet()
            ->setSessionId($sessionid)
            ->setServiceId(WswClient::HYXXBG)
            ->setNsrsbh($this->conf['nsrsbh'])
            ->setTranSeq($this->conf['username'] . $this->conf['client_num'] . '00000' . date('Ymd') . str_pad($code, 8, '0', STR_PAD_LEFT))  //交易流水号为36位长字符串，编码规则为：4位厂商简码 + 11位客户端编号（任意11字字节字符串） + “00000” + 4位年 + 2位月 + 2位日 + 8位顺序号 共36位。
            ->setContent(base64_encode($content))
            ->buildXml();

        $res = $this->getResFromJson($bizXml);
        if ($debug) {
            $this->lastRes = $res;
        }
        return $res['body']['successFlag'] ?? false;
    }

    /**
     * 查询会员明细信息
     * @param string $code 顺序号
     * @param string $sessionid sessionId
     * @param string $name 姓名
     * @param string $idCardNum 身份证号码
     * @return array 会员登记明细 ["count"=>1,"data" = [[...]]]
     * @throws WswException
     */
    public function cxhymxxx(string $code, string $sessionid, string $name, string $idCardNum, $page = 1, $limit = 20): array
    {
        $content = json_encode([
            'sessionid' => $sessionid,
            'page'      => $page,
            'limit'     => $limit,
            'hySfmc'    => $name,
            'hySfzjhm'  => $idCardNum,
        ]);

        $biz    = new Biz();
        $bizXml = $biz->autoSet()
            ->setSessionId($sessionid)
            ->setServiceId(WswClient::CXHYMXXX)
            ->setNsrsbh($this->conf['nsrsbh'])
            ->setTranSeq($this->conf['username'] . $this->conf['client_num'] . '00000' . date('Ymd') . str_pad($code, 8, '0', STR_PAD_LEFT))  //交易流水号为36位长字符串，编码规则为：4位厂商简码 + 11位客户端编号（任意11字字节字符串） + “00000” + 4位年 + 2位月 + 2位日 + 8位顺序号 共36位。
            ->setContent(base64_encode($content))
            ->buildXml();

        $res = $this->getResFromJson($bizXml);
        return json_decode($res['body'], true);
    }

    /**
     * 请求并获取结果（请求和解析的业务报文为xml）
     * @param string $bizXml
     * @return array
     * @throws WswException
     */
    private function getResFromXml(string $bizXml): array
    {
        $num = rand(1000, 9999);
        $this->log($num, $bizXml, 1);
        $res = $this->soapClient->doService(['bizXml' => $bizXml]);
        $this->log($num, $res, 2);
        //解第一层xml
        $res = XmlUtil::decode($res->return);
        if (!isset($res['head']['rtn_code'])) {
            throw new WswException('响应报文解析失败', self::ERROR_RESPONSE_PARSE);
        } elseif ($res['head']['rtn_code'] != 0) {
            throw new WswException($res['head']['rtn_msg']['Message'] ?? '错误代码:' . $res['head']['rtn_code'], self::ERROR_RESPONSE);
        }
        //再解上述结果中的body拿到返回的业务报文结构
        $res = XmlUtil::decode($res['body']);
        $this->log($num, $res, 3);
        if (!isset($res['body']['result'])) {
            throw new WswException('业务报文解析失败', self::ERROR_BUSINESS_PARSE);
        } elseif ($res['body']['result']['rtnCode'] != 0) {
            //异常或登录失败
            throw new WswException($res['body']['result']['rtnMessage'] ?? '错误代码:' . $res['body']['result']['rtnCode'], self::ERROR_BUSINESS);
        }
        return $res;
    }

    /**
     * 请求并获取结果（请求的业务报文为json，返回的有的是json字符串，有的直接是数组）
     * @param string $bizXml
     * @return array
     * @throws WswException
     */
    private function getResFromJson(string $bizXml): array
    {
        $num = rand(1000, 9999);
        $this->log($num, $bizXml, 1);
        $res = $this->soapClient->doService(['bizXml' => $bizXml]);
        $this->log($num, $res, 2);
        //解第一层xml
        $res = XmlUtil::decode($res->return);
        if (!isset($res['head']['rtn_code'])) {
            throw new WswException('响应报文解析失败', self::ERROR_RESPONSE_PARSE);
        } elseif ($res['head']['rtn_code'] != 0) {
            throw new WswException($res['head']['rtn_msg']['Message'] ?? '错误代码:' . $res['head']['rtn_code'], self::ERROR_RESPONSE);
        }
        //再解上述结果中的body拿到返回的业务报文结构
        $res = json_decode($res['body'], true);
        $this->log($num, $res, 3);
        if (!isset($res['rtnCode'])) {
            throw new WswException('业务报文解析失败', self::ERROR_BUSINESS_PARSE);
        } elseif ($res['rtnCode'] !== '000') {
            //异常或登录失败
            throw new WswException($res['errMsg'] ?? '错误代码:' . $res['rtnCode'], self::ERROR_BUSINESS);
        }
        return $res;
    }

    /**
     * 记录日志
     * @param string $num 请求的编号
     * @param mixed $content 记录内容
     * @param int $type 内容类型 1.请求 2.响应 3.业务报文
     * @return void
     */
    private function log(string $num = '1000', $content = '', int $type = 1)
    {
        if (!$this->conf['log']) {
            return;
        }
        try {
            $dir1 = $this->conf['log_f_path'] . 'wsw_log';
            if (!is_dir($dir1)) {
                mkdir($dir1, 0777, true);
            }
            $dir2 = $dir1 . DIRECTORY_SEPARATOR . date('Y-m');
            if (!is_dir($dir2)) {
                mkdir($dir2, 0777, true);
            }
            $file = $dir2 . DIRECTORY_SEPARATOR . date('d') . '.log';

            $logStr = '[' . date('H:i:s') . ']  (' . $num . ')  ' . ($type === 1 ? 'Request' : ($type === 2 ? 'Response' : ($type === 3 ? 'Business' : ''))) . '   >>>>>>' . PHP_EOL .
                var_export($content, true) . PHP_EOL;
            file_put_contents($file, $logStr . PHP_EOL, FILE_APPEND);
        } catch (\Exception $e) {

        }
    }
}