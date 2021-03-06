<?php

/**
 * Created by PhpStorm.
 * User: WuweiHuang
 * Date: 2019/6/23
 * Time: 14:34.
 */

declare(strict_types=1);

/*
 * This file is part of the uuk020/logistics.
 *
 * (c) WuweiHuang<wuwei.huangw@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Wuwei\Logistics\Channel;

use Wuwei\Logistics\Exceptions\HttpException;

/**
 * 通过百度物流查询.
 */
class BaiduChannel extends Channel
{
    /**
     * JiSuChannel constructor.
     */
    public function __construct()
    {
        $this->url = 'https://express.baidu.com/express/api/express';
    }

    /**
     * 请求
     *
     * @throws \Wuwei\Logistics\Exceptions\HttpException
     */
    public function request(string $code, string $company = '', array $extend = []): array
    {
        try {
            $config = $this->getChannelConfig();
            $params = ['appid' => $config['app_secret'], 'tokenV2' => $config['app_key'], 'com' => $company, 'nu' => $code];
            if ($extend) $params = array_merge($params, $extend);
            $response = $this->get($this->url, $params);
            $this->toArray($response);
            $this->format();

            return $this->response;
        } catch (HttpException $exception) {
            throw new HttpException($exception->getMessage());
        }
    }

    /**
     * 统一物流信息.
     */
    protected function format()
    {
        if (!empty($this->response['data'])) {
            $formatData = [];
            foreach ($this->response['data'] as $datum) {
                $formatData[] = ['time' => $datum['time'], 'description' => $datum['status']];
            }
            $this->response['data'] = $formatData;
        }
    }

    /**
     * 转为数组.
     *
     * @param array|string $response
     */
    protected function toArray($response)
    {
        $jsonToArray = \json_decode($response, true);
        if (empty($jsonToArray)) {
            $this->response = [
                'status' => 0,
                'message' => '请求发生不知名错误, 查询不到物流信息',
                'error_code' => 0,
                'data' => [],
                'logistics_company' => '',
            ];
        } else {
            if (0 === $jsonToArray['status']) {
                $this->response = [
                    'status' => 1,
                    'message' => 'ok',
                    'error_code' => 0,
                    'data' => $jsonToArray['data']['info']['context'],
                    'logistics_company' => '',
                ];
            } else {
                $this->response = [
                    'status' => 0,
                    'message' => $jsonToArray['msg'],
                    'error_code' => $jsonToArray['status'],
                    'data' => [],
                    'logistics_company' => '',
                ];
            }
        }
    }
}
