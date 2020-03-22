<?php

namespace App\Kernel;

use App\Facades\Auth;

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
     * 继承至其他视图
     *
     * @var string|null
     */
    protected $extends = null;

    /**
     * 继承父级视图的填充数据
     *
     * @var array
     */
    protected $section = [];
    protected $section_name = null;

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
        extract($this->pushCommon());
        extract($this->data);
        include $this->getView($this->view);
        $content = ob_get_clean();
        if ($this->extends) {
            ob_start();
            include $this->getView($this->extends);
            $content = ob_get_clean();
            $this->extends = null;
        }
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
            throw new \UnexpectedValueException("The view $view_file does not exist");
        }
        return $view_file;
    }

    /**
     * 返回通用的视图数据或方法
     *
     * @return  array  通用的视图数据或方法
     */
    private function pushCommon()
    {
        return [
            'csrf' => '<input type="hidden" name="_token" value="' . csrf_token() . '">',
            'csrf_token' => csrf_token(),
            'request' => request(),
            'auth' => Auth::check(),
            'guest' => Auth::guest(),
            'include' => function ($view) {
                include view_path($view);
            },
            'echo' => function ($content) {
                echo htmlspecialchars($content);
            },
            'json' => function ($data, $options = 0) {
                echo json_encode($data, $options);
            },
            'asset' => function ($asset) {
                echo asset($asset);
            },
            'extends' => function ($view) {
                $this->extends = $view;
            },
            'section' => function ($name, $data = null) {
                if ($data !== null) {
                    $this->section[$name] = $data;
                } else {
                    ob_start();
                    $this->section_name = $name;
                }
            },
            'endsection' => function () {
                if ($this->section_name === null) {
                    throw new \RuntimeException("Endsection does not have a corresponding start section.");
                }
                $this->section[$this->section_name] = ob_get_clean();
            },
            'yield' => function ($name) {
                echo $this->section[$name];
            },
            'error' => function ($name) {
                if (isset($this->data['errors'][$name])) {
                    return $this->data['errors'][$name];
                } else {
                    return false;
                }
            }
        ];
    }
}
