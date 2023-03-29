<?php
/**
 * Author:Shaun·Yang
 * Date:2023/1/29
 * Time:下午3:39
 * Description:使用示例
 */

use wsw\Member;
use wsw\WswClient;

require './vendor/autoload.php';

try {

    $conf = [
        'wsdl'                     => '',
        'desKey'                   => '',
        'username'                 => '',
        'password'                 => '',
        'nsrsbh'                   => '',
        'yhm'                      => '',
        'mm'                       => '',
        'bsrysfjzhm'               => '',
        'log'                      => '',
        'log_f_path'               => '',
        'member_business_location' => '',
        'member_business_scope'    => '',
        'client_num'               => '', //客户端编号 11位
        'hybsryxm'                 => '',
        'hybsryzjlx'               => '',
        'hybsryzjhm'               => '',
        'hybsryyddh'               => '',
    ];

    $code = 4; //顺序号
    $wsw  = new WswClient($conf);
    //登录并获取sessionId
    $sessionId = $wsw->loginProcess([$code++, $code++, $code++]);
    var_export($sessionId);
    //查询企业授权信息
    $timeArr = $wsw->cxptqysqxx($code++, $sessionId);
    var_export($timeArr);
    //报告会员信息
    $member = Member::create(
        '01',
        '杨某某',
        '17788888888',
        '131025111111111111',
        '2015-04-30',
        '2025-04-30',
        '河北省廊坊市大城县X',
        '大城县公安局',
        '6226622800000000',
        '光大银行',
        '中国光大银行股份有限公司廊坊金光道支行',
        $conf['member_business_location'],
        $conf['member_business_scope'],
        '2099-12-31',
        'Y',
        $conf['hybsryxm'],
        $conf['hybsryzjlx'],
        $conf['hybsryzjhm'],
        $conf['hybsryyddh'],

    );

    $memberAddRes = $wsw->hyxxbg($code++, $sessionId, [$member->toArray()]);
    var_export($memberAddRes);
    //查询会员
    $memberDetail = $wsw->cxhymxxx($code++, $sessionId, '杨某某', '131025111111111111', 1, 20);
    var_export($memberDetail);

} catch (\Exception $e) {
    print_r([
            $e->getCode(),
            $e->getLine(),
            $e->getTraceAsString(),
            $e->getMessage()]
    );
}
