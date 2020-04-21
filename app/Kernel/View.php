<?php

namespace App\Kernel;

use App\Facades\Auth;
use UnexpectedValueException;
use function array_merge;
use function asset;
use function csrf_token;
use function extract;
use function file_exists;
use function htmlspecialchars;
use function json_encode;
use function ob_get_clean;
use function ob_start;
use function request;
use function str_replace;
use function view_path;

class View
{
    /**
     * 视图的名称
     *
     * @var string
     */
    protected $view = '';

    /**
     * 传入视图的数据
     *
     * @var array
     */
    protected $data = [];

    /**
     * 判断视图是否存在
     *
     * @param   string  $view  视图名称
     *
     * @return  bool
     */
    public function exists(string $view): bool
    {
        $view = str_replace('.', '/', $view);
        $view_file = __DIR__ . "/../Views/$view.php";
        return file_exists($view_file);
    }

    /**
     * 合并传入视图的数据
     *
     * @param   array  $data  数据
     *
     * @return  View   this
     */
    public function assign(array $data): View
    {
        $this->data = array_merge($this->data, $data);
        if (!isset($this->data['errors'])) {
            $this->data['errors'] = [];
        }
        return $this;
    }

    /**
     * 添加传入视图的数据
     *
     * @param   string  $key    数据的 Key
     * @param   mixed   $value  数据
     *
     * @return  View            this
     */
    public function with(string $key, $value): View
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * 创建或修改视图
     *
     * @param   string  $view  视图名称
     * @param   array   $data  传入视图的数据
     *
     * @return  View
     */
    public function make(string $view, array $data = []): View
    {
        $this->view = $view;
        $this->assign($data);
        return $this;
    }

    /**
     * 渲染视图
     *
     * @return  string
     */
    public function render(): string
    {
        ob_start();
        ViewHtml::$data = $this->data;
        include $this->getView($this->view);
        $content = ob_get_clean();
        if (ViewHtml::$extends) {
            ob_start();
            include $this->getView(ViewHtml::$extends);
            $content = ob_get_clean();
            ViewHtml::$extends = null;
        }
        ViewHtml::$data = [];
        return $content;
    }

    /**
     * 获取视图路径
     *
     * @param   string  $view  视图名称
     *
     * @return  string         视图路径
     */
    private function getView(string $view): string
    {
        $view_file = view_path($view);
        if (!file_exists($view_file)) {
            throw new UnexpectedValueException(
                "The view $view_file does not exist"
            );
        }
        return $view_file;
    }
}
