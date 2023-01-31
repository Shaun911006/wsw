<?php
/**
 * Author:Shaun·Yang
 * Date:2023/1/31
 * Time:上午9:50
 * Description:会员数据结构
 */

namespace wsw;

class Member
{
    /**
     * 会员登记类型（01|个人、02|个体工商户、03|个人独资企业）
     * @var string
     */
    public string $hyDjlx;
    /**
     * 会员经营地点
     * @var string
     */
    public string $hyJydd;
    /**
     * 状态（01-新增、02-变更、03-注销）
     * @var string
     */
    public string $operateZt;
    /**
     * 会员手机号码
     * @var string
     */
    public string $hySjhm;
    /**
     * 会员身份证件有效期起
     * @var string
     */
    public string $hySfzjYxqq;
    /**
     * 会员身份证件有效期止
     * @var string
     */
    public string $hySfzjYxqz;
    /**
     * 会员性别
     * @var string
     */
    public string $hySfxxXb;
    /**
     * 会员身份证地址
     * @var string
     */
    public string $hySfzjSfzdz;
    /**
     * 会员主要经营范围
     * @var string
     */
    public string $hyZyjyfw;
    /**
     * 会员纳税人识别号（如果会员登记类型为01，则该字段不需要录入）
     * @var string
     */
    public string $hyNsrsbh;
    /**
     * 会员纳税人名称（如果会员登记类型为01，则该字段不需要录入）
     * @var string
     */
    public string $hyNsrmc;
    /**
     * 会员身份证件号码
     * @var string
     */
    public string $hySfxxZjhm;
    /**
     * 会员姓名
     * @var string
     */
    public string $hySfxxMc;
    /**
     * 会员身份证件类型（201：居民身份证）
     * @var string
     */
    public string $hySfxxZjlx;
    /**
     * 会员身份证件签发机关
     * @var string
     */
    public string $hySfzjQfjg;
    /**
     * 会员银行卡卡号
     * @var string
     */
    public string $hyYhkKh;
    /**
     * 会员银行卡开户银行
     * @var string
     */
    public string $hyYhkKhyh;
    /**
     * 会员开户银行名称
     * @var string
     */
    public string $hyYhkYhmc;
    /**
     * 会员银行开户姓名
     * @var string
     */
    public string $hyYhkXm;

    /**
     * @param string $hyDjlx 会员登记类型（01|个人、02|个体工商户、03|个人独资企业）
     * @param string $hyJydd 会员经营地点
     * @param string $operateZt 状态（01-新增、02-变更、03-注销）
     * @param string $hySjhm 会员手机号码
     * @param string $hySfzjYxqq 会员身份证件有效期起
     * @param string $hySfzjYxqz 会员身份证件有效期止
     * @param string $hySfxxXb 会员性别
     * @param string $hySfzjSfzdz 会员身份证地址
     * @param string $hyZyjyfw 会员主要经营范围
     * @param string $hyNsrsbh 会员纳税人识别号（如果会员登记类型为01，则该字段不需要录入）
     * @param string $hyNsrmc 会员纳税人名称（如果会员登记类型为01，则该字段不需要录入）
     * @param string $hySfxxZjhm 会员身份证件号码
     * @param string $hySfxxMc 会员姓名
     * @param string $hySfxxZjlx 会员身份证件类型（201：居民身份证）
     * @param string $hySfzjQfjg 会员身份证件签发机关
     * @param string $hyYhkKh 会员银行卡卡号
     * @param string $hyYhkKhyh 会员银行卡开户银行
     * @param string $hyYhkYhmc 会员开户银行名称
     * @param string $hyYhkXm 会员银行开户姓名
     */
    public function __construct(
        string $hyDjlx = '',
        string $hyJydd = '',
        string $operateZt = '',
        string $hySjhm = '',
        string $hySfzjYxqq = '',
        string $hySfzjYxqz = '',
        string $hySfxxXb = '',
        string $hySfzjSfzdz = '',
        string $hyZyjyfw = '',
        string $hyNsrsbh = '',
        string $hyNsrmc = '',
        string $hySfxxZjhm = '',
        string $hySfxxMc = '',
        string $hySfxxZjlx = '',
        string $hySfzjQfjg = '',
        string $hyYhkKh = '',
        string $hyYhkKhyh = '',
        string $hyYhkYhmc = '',
        string $hyYhkXm = ''
    )
    {
        $this->hyDjlx      = $hyDjlx;
        $this->hyJydd      = $hyJydd;
        $this->operateZt   = $operateZt;
        $this->hySjhm      = $hySjhm;
        $this->hySfzjYxqq  = $hySfzjYxqq;
        $this->hySfzjYxqz  = $hySfzjYxqz;
        $this->hySfxxXb    = $hySfxxXb;
        $this->hySfzjSfzdz = $hySfzjSfzdz;
        $this->hyZyjyfw    = $hyZyjyfw;
        $this->hyNsrsbh    = $hyNsrsbh;
        $this->hyNsrmc     = $hyNsrmc;
        $this->hySfxxZjhm  = $hySfxxZjhm;
        $this->hySfxxMc    = $hySfxxMc;
        $this->hySfxxZjlx  = $hySfxxZjlx;
        $this->hySfzjQfjg  = $hySfzjQfjg;
        $this->hyYhkKh     = $hyYhkKh;
        $this->hyYhkKhyh   = $hyYhkKhyh;
        $this->hyYhkYhmc   = $hyYhkYhmc;
        $this->hyYhkXm     = $hyYhkXm;
    }

    /**
     * 创建一个会员对象
     * @param string $status 状态（01-新增、02-变更、03-注销）
     * @param string $name 姓名
     * @param string $mobile 手机号码
     * @param string $idCardNum 身份证号码
     * @param string $idCardDateFrom 身份证有效期起
     * @param string $idCardDateTo 身份证有效期止
     * @param string $idCardAddress 身份证地址
     * @param string $idCardIssue 身份证发证机关
     * @param string $bankNum 银行卡号
     * @param string $bankName 银行名称
     * @param string $openBank 开户行
     * @param string $businessLocation 经营地址
     * @param string $businessScope 经营范围
     * @return Member
     */
    public static function create(
        string $status,
        string $name,
        string $mobile,
        string $idCardNum,
        string $idCardDateFrom,
        string $idCardDateTo,
        string $idCardAddress,
        string $idCardIssue,
        string $bankNum,
        string $bankName,
        string $openBank,
        string $businessLocation,
        string $businessScope
    ): Member
    {
        return new Member(
            '01',
            $businessLocation,
            $status,
            $mobile,
            $idCardDateFrom,
            $idCardDateTo,
            self::parseSexByIdCard($idCardNum),
            $idCardAddress, $businessScope,
            '',
            '',
            $idCardNum,
            $name,
            '201',
            $idCardIssue,
            $bankNum,
            $bankName,
            $openBank,
            $name
        );
    }

    /**
     * 将对象转换为json字符串
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 将对象转换为数组
     * @return array
     */
    public function toArray(): array
    {
        return json_decode($this->toJson(),true);
    }

    /**
     * 根据身份证号返回性别（男、女）
     * @param string $idCardNum
     * @return string
     */
    private static function parseSexByIdCard(string $idCardNum): string
    {
        $sexint = (int)substr($idCardNum, strlen($idCardNum) - 2, 1);

        return 0 === $sexint % 2 ? '女' : '男';
    }
}