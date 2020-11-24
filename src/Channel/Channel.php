<?php
/**
 * Created by PhpStorm.
 * User: WuweiHuang
 * Date: 2018/12/24
 * Time: 21:32.
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

use Wuwei\Logistics\Config;
use Wuwei\Logistics\Traits\HttpRequest;

abstract class Channel
{
    /*
     * HTTP 请求
     */
    use HttpRequest;

    /**
     * 渠道URL.
     *
     * @var string
     */
    protected $url;

    /**
     * 请求资源.
     *
     * @var array
     */
    protected $response;

    /**
     * 请求选项.
     *
     * @var array
     */
    protected $option = [];

    /**
     * 设置请求选项.
     *
     * @return \Wuwei\Logistics\Channel\Channel
     */
    public function setRequestOption(array $option): self
    {
        if (!empty($this->option)) {
            if (isset($option['header']) && isset($this->option['header'])) {
                $this->option['header'] = array_merge($this->option['header'], $option['header']);
            }
            if (isset($option['proxy'])) {
                $this->option['proxy'] = $option['proxy'];
            }
        } else {
            $this->option = $option;
        }

        return $this;
    }

    /**
     * 获取实例化的类名称.
     */
    protected function getClassName(): string
    {
        $className = basename(str_replace('\\', '/', (get_class($this))));

        return preg_replace('/Channel/', '', $className);
    }

    /**
     * 获取配置.
     */
    protected function getChannelConfig(): array
    {
        return (new Config())->getConfig(strtolower($this->getClassName()));
    }

    /**
     * 调用查询接口.
     */
    abstract public function request(string $code, string $company = '', array $extend = []): array;

    /**
     * 转换为数组.
     *
     * @param string|array $response
     */
    abstract protected function toArray($response);

    /**
     * 格式物流信息.
     *
     * @return mixed
     */
    abstract protected function format();
}
