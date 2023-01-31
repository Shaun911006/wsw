<?php
/**
 * XML工具类
 * Author:Shaun·Yang
 * Date:2020/6/15
 * Time:上午9:12
 * Description:XML工具类
 */

namespace wsw;

use wsw\exceptions\WswException;

class XmlUtil
{
    /**
     * XML编码
     * @param mixed $data 数据
     * @param string $encoding 数据编码
     * @param bool $root 根节点名
     * @return string
     */
    public static function encode($data, string $encoding = 'UTF-8', bool $root = true): string
    {
        if ($root) {
            $xml = '<?xml version="1.0" encoding="' . $encoding . '"?>';
        } else {
            $xml = '';
        }
        $xml .= self::data_to_xml($data);
        return $xml;
    }

    /**
     * 数组转xml
     * @param array $data
     * @return string
     */
    public static function data_to_xml(array $data): string
    {
        $xml = '';
        foreach ($data as $key => $val) {
            if (is_array($val) || is_object($val)) {
                if (!self::is_assoc($val)) {
                    foreach ($val as $item) {
                        $xml .= "<$key>" . self::data_to_xml($item) . "</$key>";
                    }
                } else {
                    $xml .= "<$key>" . self::data_to_xml($val) . "</$key>";
                }
            } else {
                if ($val === '') {
                    $xml .= "<$key/>";
                } else {
                    $xml .= "<$key>$val</$key>";
                }
            }
        }
        return $xml;
    }

    /**
     * list转xml
     * @param array $list
     * @return string
     */
    public static function list_to_xml(array $list): string
    {
        $xml = '';
        foreach ($list as $row) {
            $xml .= '<row>';
            $xml .= self::data_to_xml($row);
            $xml .= '</row>';
        }
        return $xml;
    }

    /**
     * xml转数组
     * @param $xml
     * @return array
     */
    public static function decode($xml): array
    {
        if ($xml == '') return [];
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA), JSON_UNESCAPED_UNICODE), true);
    }

    /**
     * 判断数组是否为索引数组
     * @param $arr
     * @return bool
     */
    public static function is_assoc($arr): bool
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}